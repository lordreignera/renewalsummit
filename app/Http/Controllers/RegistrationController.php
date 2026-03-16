<?php

namespace App\Http\Controllers;

use App\Mail\RegistrationConfirmationMail;
use App\Models\Registration;
use App\Services\QrCodeService;
use App\Services\SwappPaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
            'full_name'            => 'required|string|max:255',
            'designation'          => 'required|in:fcc_regional_leader,senior_pastor,church_leader,corporate',
            'designation_specify'  => 'required_if:designation,church_leader|nullable|string|max:255',
            'phone'                => 'required|string|max:20',
            'email'                => 'nullable|email|max:255',
            'address'              => 'nullable|string|max:500',
            'country_type'         => 'required|in:local,africa,international',
            'nationality'          => 'nullable|string|max:100',
            // FCC membership (captured here as question 2)
            'affiliation'          => 'required|in:fcc,other',
            'fcc_region'           => 'required_if:affiliation,fcc|nullable|string|max:200',
            'fcc_regional_leader'  => 'required_if:affiliation,fcc|nullable|string|max:200',
            'fcc_church'           => 'required_if:affiliation,fcc|nullable|string|max:200',
            'fcc_pastor'           => 'nullable|string|max:200',
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
     | STEP 2 – Review / confirmation (affiliation already saved in step 1)
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

        // All affiliation data was already saved in saveStep1.
        // Just advance the step.
        $reg->update(['current_step' => 3]);

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
            // Mobile Money
            'phone_number'   => 'required_if:payment_method,mobile_money|nullable|string|max:20',
            // VISA card fields (demo — format checked only, no real charge)
            'card_name'      => 'required_if:payment_method,visa|nullable|string|max:100',
            'card_number'    => ['required_if:payment_method,visa', 'nullable', 'string',
                                 'regex:/^\d{4}\s?\d{4}\s?\d{4}\s?\d{4}$/'],
            'card_expiry'    => ['required_if:payment_method,visa', 'nullable',
                                 'regex:/^(0[1-9]|1[0-2])\/\d{2}$/'],
            'card_cvc'       => 'required_if:payment_method,visa|nullable|digits_between:3,4',
        ], [
            'card_number.regex'  => 'Please enter a valid 16-digit card number.',
            'card_expiry.regex'  => 'Expiry must be in MM/YY format.',
        ]);

        // ── MOBILE MONEY ──────────────────────────────────────────────────
        if ($data['payment_method'] === 'mobile_money') {
            $result = $this->swapp->initiateMobileMoney($reg, $data['phone_number']);

            if (! $result['success']) {
                return back()->withInput()
                    ->with('error', $result['message'] ?? 'Payment initiation failed. Please try again.');
            }

            // Store SwApp request ID in session so pending page can reference it
            session(['swapp_request_id' => $result['request_id']]);

            // Redirect to pending page — user approves on their phone
            // The /payment/callback route handles marking the registration paid
            return redirect()->route('register.pending', $reg->reference);
        }

        // ── VISA / CARD ────────────────────────────────────────────────────
        // Card gateway not yet integrated. Mark paid immediately for now.
        $reg->update([
            'status'       => 'paid',
            'current_step' => 3,
        ]);

        // Generate QR code
        try {
            $qrPath = app(QrCodeService::class)->generateForRegistration($reg);
            $reg->update(['qr_code_path' => $qrPath]);
            $reg->refresh();
        } catch (\Throwable $e) {
            Log::error('QR generation failed', ['error' => $e->getMessage(), 'reg' => $reg->id]);
        }

        // Send confirmation email
        if ($reg->email) {
            try {
                RegistrationConfirmationMail::dispatchToRegistration($reg);
            } catch (\Throwable $e) {
                Log::error('Confirmation email failed', ['error' => $e->getMessage(), 'reg' => $reg->id]);
            }
        }

        session()->forget('registration_id');
        session(['completed_ref' => $reg->reference]);

        return redirect()->route('register.complete')
            ->with('success', 'Payment confirmed! You will receive a confirmation email shortly.');
    }

    /* ─────────────────────────────────────────────────────────────
     | Pending / Complete pages
     |────────────────────────────────────────────────────────────*/

    public function pending(string $reference): View
    {
        $reg = Registration::where('reference', $reference)->firstOrFail();
        return view('registration.pending', compact('reg'));
    }

    public function complete(Request $request): View|RedirectResponse
    {
        $reference = session('completed_ref') ?? $request->query('ref');

        if (! $reference) {
            return redirect()->route('register.start')
                ->with('info', 'Registration already completed or session expired.');
        }

        $reg = Registration::where('reference', $reference)
            ->where('status', 'paid')
            ->first();

        if (! $reg) {
            return redirect()->route('register.start')
                ->with('error', 'Registration not found or not yet confirmed. Reference: ' . $reference);
        }

        $qrUrl = $reg->qr_code_path
            ? route('qr.show', ['reference' => $reg->reference])
            : null;

        return view('registration.complete', compact('reg', 'qrUrl'));
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
