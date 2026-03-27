<?php

namespace App\Http\Controllers;

use App\Mail\RegistrationConfirmationMail;
use App\Models\Hotel;
use App\Models\Payment;
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

        $phone = trim((string) $request->phone);

        // First priority: continue an unfinished registration if one exists.
        $reg = Registration::where('phone', $phone)
            ->whereNotIn('status', ['paid', 'cancelled', 'checked_in'])
            ->latest()
            ->first();

        if ($reg) {
            $request->session()->put('registration_id', $reg->id);

            return redirect()->route('register.step', $reg->current_step)
                ->with('info', "Resuming your registration — you were on Step {$reg->current_step}.");
        }

        // If registration is already paid/checked_in, allow guest to continue to accommodation planning.
        $paidReg = Registration::where('phone', $phone)
            ->whereIn('status', ['paid', 'checked_in'])
            ->latest()
            ->first();

        if ($paidReg) {
            return redirect()->route('register.accommodation', [
                'reference' => $paidReg->reference,
                'token' => $paidReg->qr_token,
            ])->with('info', 'Registration is already paid. You can now continue with accommodation planning.');
        }

        return back()->with('error', 'No active registration found for that phone number. Please start a new registration.');
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
            // Organisation / Church / Ministry
            'organization'         => 'required|in:NFBAC,PAG,FCC,Compassion,Full Gospel Church,other',
            'organization_other'   => 'required_if:organization,other|nullable|string|max:200',
            // FCC details — only required when organisation = FCC
            'fcc_region'           => 'required_if:organization,FCC|nullable|string|max:200',
            'fcc_regional_leader'  => 'required_if:organization,FCC|nullable|string|max:200',
            'fcc_church'           => 'required_if:organization,FCC|nullable|string|max:200',
            'fcc_pastor'           => 'nullable|string|max:200',
            // Group attendance
            'is_group'             => 'nullable|boolean',
            'group_name'           => 'required_if:is_group,1|nullable|string|max:200',
            // Emergency & Medical
            'emergency_contact_name'  => 'nullable|string|max:191',
            'emergency_contact_phone' => 'nullable|string|max:50',
            'medical_conditions'      => 'nullable|string|max:1000',
            'allergies'               => 'nullable|string|max:1000',
            'mobility_needs'          => 'nullable|string|max:1000',
            'special_needs'           => 'nullable|string|max:1000',
        ]);

        // Fee tiers: local=UGX 150k, africa=$50, international=$150
        $feeTiers = [
            'local'         => ['amount' => (int) env('SUMMIT_FEE_LOCAL',         150000), 'currency' => 'UGX'],
            'africa'        => ['amount' => (int) env('SUMMIT_FEE_AFRICA',         50),     'currency' => 'USD'],
            'international' => ['amount' => (int) env('SUMMIT_FEE_INTERNATIONAL', 150),    'currency' => 'USD'],
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

        // Derive affiliation from organisation selection
        $data['affiliation'] = ($data['organization'] === 'FCC') ? 'fcc' : 'other';
        $data['is_group']    = (bool) ($data['is_group'] ?? false);

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

        return redirect()->route('register.step2', $request->boolean('embed') ? ['embed' => 1] : []);
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

        return redirect()->route('register.step3', $request->boolean('embed') ? ['embed' => 1] : []);
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
            'payment_method' => 'required|in:mobile_money',
            'phone_number'   => 'required|string|max:20',
        ]);

        // ── MOBILE MONEY ──────────────────────────────────────────────────
        $result = $this->swapp->initiateMobileMoney($reg, $data['phone_number']);

        if (! $result['success']) {
            return back()->withInput()
                ->with('error', $result['message'] ?? 'Payment initiation failed. Please try again.');
        }

        // Store SwApp request ID in session so pending page can reference it
        session(['swapp_request_id' => $result['request_id']]);

        // Redirect to pending page — user approves on their phone
        return redirect()->route('register.pending', $reg->reference);
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

    public function accommodation(string $reference, string $token): View|RedirectResponse
    {
        $reg = Registration::query()
            ->where('reference', $reference)
            ->where('qr_token', $token)
            ->first();

        if (! $reg || ! in_array($reg->status, ['paid', 'checked_in'], true)) {
            return redirect()->route('register.start')
                ->with('error', 'Accommodation planning is available after registration payment confirmation.');
        }

        $hotels = Hotel::query()
            ->where('is_active', true)
            ->with(['roomTypes' => fn ($q) => $q->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('registration.accommodation', compact('reg', 'hotels'));
    }

    public function accommodationPending(string $reference, string $token): View|RedirectResponse
    {
        $reg = Registration::query()
            ->where('reference', $reference)
            ->where('qr_token', $token)
            ->first();

        if (! $reg || ! in_array($reg->status, ['paid', 'checked_in'], true)) {
            return redirect()->route('register.start')
                ->with('error', 'Accommodation payment access is invalid or expired.');
        }

        if ($reg->accommodation_payment_status === 'paid') {
            return redirect()->route('register.complete', ['ref' => $reg->reference])
                ->with('success', 'Accommodation payment already confirmed.');
        }

        $latestAccommodationPayment = $reg->payments()
            ->where('payment_context', 'accommodation')
            ->latest()
            ->first();

        $defaultPhone = $latestAccommodationPayment?->phone_number ?: $reg->phone;

        return view('registration.accommodation_pending', compact('reg', 'defaultPhone'));
    }

    public function resendAccommodationPrompt(Request $request, string $reference, string $token): RedirectResponse
    {
        $reg = Registration::query()
            ->where('reference', $reference)
            ->where('qr_token', $token)
            ->first();

        if (! $reg || ! in_array($reg->status, ['paid', 'checked_in'], true)) {
            return redirect()->route('register.start')
                ->with('error', 'Accommodation payment access is invalid or expired.');
        }

        if ($reg->accommodation_payment_status === 'paid') {
            return redirect()->route('register.complete', ['ref' => $reg->reference])
                ->with('success', 'Accommodation payment already confirmed.');
        }

        if (($reg->accommodation_booking_mode ?? '') !== 'book_through_us_and_pay') {
            return redirect()->route('register.accommodation', ['reference' => $reg->reference, 'token' => $reg->qr_token])
                ->with('warning', 'Accommodation payment prompt is only for "book through us and pay now" mode.');
        }

        $data = $request->validate([
            'phone_number' => 'required|string|max:20',
        ]);

        $amount = (int) ($reg->accommodation_fee ?? 0);
        $currency = $reg->accommodation_currency ?: $reg->currency;

        if ($amount <= 0) {
            return back()->with('error', 'Accommodation amount is missing. Please return and save accommodation details again.');
        }

        $result = $this->swapp->initiateMobileMoneyForAmount(
            registration: $reg,
            amount: $amount,
            currency: $currency,
            phone: $data['phone_number'],
            context: 'accommodation',
        );

        if (! $result['success']) {
            return back()->withInput()
                ->with('error', $result['message'] ?? 'Could not resend payment prompt.');
        }

        $reg->update(['accommodation_payment_status' => 'pending']);

        return back()->with('info', 'A new accommodation payment prompt has been sent to your phone.');
    }

    public function saveAccommodation(Request $request, string $reference, string $token): RedirectResponse
    {
        $reg = Registration::query()
            ->where('reference', $reference)
            ->where('qr_token', $token)
            ->first();

        if (! $reg || ! in_array($reg->status, ['paid', 'checked_in'], true)) {
            return redirect()->route('register.start')
                ->with('error', 'Accommodation planning is available after registration payment confirmation.');
        }

        $data = $request->validate([
            'accommodation_booking_mode' => 'required|in:self_book,book_through_us_no_payment,book_through_us_and_pay',
            'accommodation_hotel_id' => 'required|exists:hotels,id',
            'accommodation_room_type' => 'required|string|max:50',
            'accommodation_nights' => 'nullable|integer|min:1|max:14',
            'accommodation_currency' => 'nullable|in:USD,UGX',
            'accommodation_fee' => 'nullable|integer|min:0',
            'payment_method' => 'nullable|in:mobile_money',
            'phone_number' => 'nullable|string|max:20',
        ]);

        $hotel = Hotel::with('roomTypes')->findOrFail((int) $data['accommodation_hotel_id']);
        $isSelfBook = $data['accommodation_booking_mode'] === 'self_book';
        $currency = $data['accommodation_currency'] ?? $reg->currency ?? 'UGX';
        $roomType = $data['accommodation_room_type'];
        $nights = $isSelfBook ? null : (int) ($data['accommodation_nights'] ?? 1);
        $perNight = $isSelfBook ? 0 : $hotel->priceForRoomType($currency, $roomType);
        $estimatedTotal = $isSelfBook ? 0 : ($perNight * $nights);

        $update = [
            'accommodation_required' => true,
            'accommodation_choice' => $hotel->name,
            'accommodation_hotel_id' => $hotel->id,
            'accommodation_booking_mode' => $data['accommodation_booking_mode'],
            'accommodation_room_type' => $roomType,
            'accommodation_nights' => $nights,
            'accommodation_currency' => $isSelfBook ? null : $currency,
            'accommodation_fee' => $isSelfBook ? null : $estimatedTotal,
        ];

        if ($data['accommodation_booking_mode'] === 'self_book') {
            $update['accommodation_payment_status'] = 'not_required';
            $reg->update($update);

            return redirect()->route('register.complete', ['ref' => $reg->reference])
                ->with('success', 'Accommodation preference saved. We can now track where you will stay.');
        }

        if ($data['accommodation_booking_mode'] === 'book_through_us_no_payment') {
            $update['accommodation_payment_status'] = 'pending';
            $reg->update($update);

            return redirect()->route('register.complete', ['ref' => $reg->reference])
                ->with('info', 'Accommodation request saved. You selected pay later.');
        }

        $paymentMethod = $request->validate([
            'payment_method' => 'required|in:mobile_money',
            'phone_number' => 'required|string|max:20',
        ]);

        $update['accommodation_payment_status'] = 'pending';
        $reg->update($update);

        $result = $this->swapp->initiateMobileMoneyForAmount(
            registration: $reg,
            amount: $estimatedTotal,
            currency: $currency,
            phone: $paymentMethod['phone_number'],
            context: 'accommodation',
        );

        if (! $result['success']) {
            return back()->withInput()
                ->with('error', $result['message'] ?? 'Accommodation payment initiation failed.');
        }

        return redirect()->route('register.accommodation.pending', ['reference' => $reg->reference, 'token' => $reg->qr_token])
            ->with('info', 'Accommodation payment prompt sent. Approve on your phone to complete.');
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
