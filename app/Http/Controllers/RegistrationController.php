<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Services\SwappPaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function __construct(protected SwappPaymentService $swapp) {}

    /* ─────────────────────────────────────────────────────────────
     | STEP 1 – Personal Info
     |────────────────────────────────────────────────────────────*/

    /**
     * Show the registration start page (or resume form).
     */
    public function start(): View
    {
        return view('registration.start');
    }

    /**
     * Look up an existing registration by phone so the user can resume.
     */
    public function resume(Request $request): RedirectResponse
    {
        $request->validate(['phone' => 'required|string']);

        $reg = Registration::where('phone', $request->phone)
            ->whereNotIn('status', ['paid', 'cancelled', 'checked_in'])
            ->latest()
            ->first();

        if (! $reg) {
            return back()->with('error', 'No pending registration found for that phone number. Please start a new registration.');
        }

        $request->session()->put('registration_id', $reg->id);

        return redirect()->route('register.step', $reg->current_step)
            ->with('info', "Resuming your registration — you were on Step {$reg->current_step}.");
    }

    /* ─────────────────────────────────────────────────────────────
     | STEP 1 – Personal details form
     |────────────────────────────────────────────────────────────*/

    public function step1(): View
    {
        $reg = $this->currentReg();
        return view('registration.step1', compact('reg'));
    }

    public function saveStep1(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'full_name'           => 'required|string|max:255',
            'designation'         => 'required|in:fcc_regional_leader,senior_pastor,church_leader,corporate',
            'designation_specify' => 'required_if:designation,church_leader|nullable|string|max:255',
            'phone'               => 'required|string|max:20',
            'email'               => 'nullable|email|max:255',
            'address'             => 'nullable|string|max:500',
            'country_type'        => 'required|in:local,africa,international',
            'nationality'         => 'nullable|string|max:100',
        ]);

        // Fee tiers: local=UGX 150k, africa=$50, international=$100
        $feeTiers = [
            'local'         => ['amount' => (int) env('SUMMIT_FEE_LOCAL',         150000), 'currency' => 'UGX'],
            'africa'        => ['amount' => (int) env('SUMMIT_FEE_AFRICA',         50),     'currency' => 'USD'],
            'international' => ['amount' => (int) env('SUMMIT_FEE_INTERNATIONAL', 100),    'currency' => 'USD'],
        ];
        $tier     = $feeTiers[$data['country_type']];
        $baseFee  = $tier['amount'];
        $currency = $tier['currency'];

        // Check for existing paid registration with same phone
        $existing = Registration::where('phone', $data['phone'])
            ->whereIn('status', ['paid', 'checked_in'])
            ->first();

        if ($existing) {
            return back()->with('error', 'This phone number already has a completed registration (Ref: ' . $existing->reference . ').');
        }

        $regId = session('registration_id');
        $reg   = $regId ? Registration::find($regId) : null;

        // Validate unique phone only for NEW registrations
        if (! $reg) {
            $phoneExists = Registration::where('phone', $data['phone'])
                ->whereNotIn('status', ['cancelled'])
                ->exists();
            if ($phoneExists) {
                return back()->with('error', 'A registration with this phone number already exists. Use "Resume" to continue.');
            }
        }

        $reg = Registration::updateOrCreate(
            ['id' => $regId],
            array_merge($data, [
                'currency'     => $currency,
                'base_fee'     => $baseFee,
                'total_amount' => $baseFee,
                'current_step' => 2,
                'status'       => 'draft',
            ])
        );

        session(['registration_id' => $reg->id]);

        return redirect()->route('register.step', 2);
    }

    /* ─────────────────────────────────────────────────────────────
     | STEP 2 – Church affiliation
     |────────────────────────────────────────────────────────────*/

    public function step2(): View|RedirectResponse
    {
        $reg = $this->currentReg();
        if (! $reg) return redirect()->route('register.start');
        return view('registration.step2', compact('reg'));
    }

    public function saveStep2(Request $request): RedirectResponse
    {
        $reg = $this->currentReg();
        if (! $reg) return redirect()->route('register.start');

        $data = $request->validate([
            'affiliation'          => 'required|in:fcc,other',
            'fcc_region'           => 'required_if:affiliation,fcc|nullable|string|max:200',
            'fcc_regional_leader'  => 'required_if:affiliation,fcc|nullable|string|max:200',
            'fcc_church'           => 'required_if:affiliation,fcc|nullable|string|max:200',
            'fcc_pastor'           => 'nullable|string|max:200',
        ]);

        $reg->update(array_merge($data, ['current_step' => 3]));

        return redirect()->route('register.step', 3);
    }

    /* ─────────────────────────────────────────────────────────────
     | STEP 3 – Payment
     |────────────────────────────────────────────────────────────*/

    public function step3(): View|RedirectResponse
    {
        $reg = $this->currentReg();
        if (! $reg) return redirect()->route('register.start');
        return view('registration.step3', compact('reg'));
    }

    public function submitPayment(Request $request): RedirectResponse
    {
        $reg = $this->currentReg();
        if (! $reg) return redirect()->route('register.start');

        $data = $request->validate([
            'payment_method' => 'required|in:mobile_money,visa',
            'phone_number'   => 'required_if:payment_method,mobile_money|nullable|string|max:20',
            'network'        => 'required_if:payment_method,mobile_money|nullable|in:MTN,AIRTEL',
        ]);

        $reg->update(['status' => 'pending', 'current_step' => 3]);

        if ($data['payment_method'] === 'mobile_money') {
            $result = $this->swapp->initiateMobileMoney($reg, $data['phone_number'], $data['network'] ?? 'MTN');

            if ($result['success']) {
                return redirect()->route('register.pending', $reg->reference)
                    ->with('info', $result['message']);
            }
            return back()->with('error', $result['message']);
        }

        // VISA – redirect to hosted checkout
        $result = $this->swapp->initiateVisa($reg);
        if ($result['success'] && $result['redirect_url']) {
            return redirect()->away($result['redirect_url']);
        }

        return back()->with('error', $result['message']);
    }

    /* ─────────────────────────────────────────────────────────────
     | Pending / Complete pages
     |────────────────────────────────────────────────────────────*/

    public function pending(string $reference): View
    {
        $reg = Registration::where('reference', $reference)->firstOrFail();
        return view('registration.pending', compact('reg'));
    }

    public function complete(Request $request): View
    {
        $reference = $request->query('ref') ?? session('registration_id');
        $reg = Registration::where('reference', $reference)
            ->orWhere('id', $reference)
            ->where('status', 'paid')
            ->firstOrFail();

        session()->forget('registration_id');
        return view('registration.complete', compact('reg'));
    }

    /* ─────────────────────────────────────────────────────────────
     | QR Verification
     |────────────────────────────────────────────────────────────*/

    public function verify(string $token): View
    {
        $reg = Registration::where('qr_token', $token)
            ->whereIn('status', ['paid', 'checked_in'])
            ->first();

        return view('registration.verify', compact('reg'));
    }

    /* ─────────────────────────────────────────────────────────────
     | Helpers
     |────────────────────────────────────────────────────────────*/

    private function currentReg(): ?Registration
    {
        $id = session('registration_id');
        return $id ? Registration::find($id) : null;
    }
}
