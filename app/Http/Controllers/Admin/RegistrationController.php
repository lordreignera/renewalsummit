<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Services\QrCodeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function __construct(protected QrCodeService $qrService) {}

    public function index(Request $request): View
    {
        // ── KPI counts (always reflect full dataset, unaffected by filters) ──
        $kpiTotal = Registration::count();

        $kpiFullyPaid = Registration::where('status', 'paid')
            ->where(function ($q) {
                $q->whereNull('accommodation_hotel_id')
                  ->orWhere('accommodation_payment_status', 'paid')
                  ->orWhere('accommodation_booking_mode', 'self_book')
                  ->orWhere('accommodation_payment_status', 'not_required');
            })->count();

        $kpiRegPaidAccPending = Registration::where('status', 'paid')
            ->whereNotNull('accommodation_hotel_id')
            ->where('accommodation_booking_mode', 'book_through_us_and_pay')
            ->where(function ($q) {
                $q->where('accommodation_payment_status', '!=', 'paid')
                  ->orWhereNull('accommodation_payment_status');
            })->count();

        $kpiAwaitingReg = Registration::whereIn('status', ['draft', 'pending'])->count();

        $kpiCheckedIn = Registration::where('status', 'checked_in')->count();

        // ── Filtered query ────────────────────────────────────────────────────
        $query = Registration::with(['latestPayment', 'accommodationHotel'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('affiliation')) {
            $query->where('affiliation', $request->affiliation);
        }
        if ($request->filled('country_type')) {
            $query->where('country_type', $request->country_type);
        }
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }
        if ($request->filled('search')) {
            $term = '%' . $request->search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('full_name', 'like', $term)
                    ->orWhere('phone', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhere('reference', 'like', $term);
            });
        }
        if ($request->filled('acc_status')) {
            switch ($request->acc_status) {
                case 'fully_paid':
                    $query->where('status', 'paid')
                        ->where(function ($q) {
                            $q->whereNull('accommodation_hotel_id')
                              ->orWhere('accommodation_payment_status', 'paid')
                              ->orWhere('accommodation_booking_mode', 'self_book')
                              ->orWhere('accommodation_payment_status', 'not_required');
                        });
                    break;
                case 'reg_paid_acc_pending':
                    $query->where('status', 'paid')
                        ->whereNotNull('accommodation_hotel_id')
                        ->where('accommodation_booking_mode', 'book_through_us_and_pay')
                        ->where(function ($q) {
                            $q->where('accommodation_payment_status', '!=', 'paid')
                              ->orWhereNull('accommodation_payment_status');
                        });
                    break;
                case 'awaiting_reg':
                    $query->whereIn('status', ['draft', 'pending']);
                    break;
                case 'checked_in':
                    $query->where('status', 'checked_in');
                    break;
            }
        }

        $registrations = $query->paginate(25)->withQueryString();

        return view('admin.registrations.index', compact(
            'registrations',
            'kpiTotal',
            'kpiFullyPaid',
            'kpiRegPaidAccPending',
            'kpiAwaitingReg',
            'kpiCheckedIn'
        ));
    }

    public function show(Registration $registration): View
    {
        $registration->load(['payments', 'accommodationHotel']);
        return view('admin.registrations.show', compact('registration'));
    }

    /**
     * Show form to create a manual registration from admin.
     */
    public function create(): View
    {
        return view('admin.registrations.create');
    }

    /**
     * Store a manual registration created by an admin.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'full_name'               => 'required|string|max:191',
            'phone'                   => 'required|string|max:50|unique:registrations,phone',
            'email'                   => 'nullable|email|max:191',
            'address'                 => 'nullable|string|max:500',
            'country_type'            => 'required|in:local,africa,international',
            'nationality'             => 'nullable|string|max:191',
            'affiliation'             => 'required|in:fcc,other',
            'fcc_region'              => 'nullable|required_if:affiliation,fcc|string|max:191',
            'fcc_regional_leader'     => 'nullable|required_if:affiliation,fcc|string|max:191',
            'fcc_church'              => 'nullable|required_if:affiliation,fcc|string|max:191',
            'fcc_pastor'              => 'nullable|string|max:191',
            'designation'             => 'nullable|string|max:191',
            'designation_specify'     => 'nullable|required_if:designation,church_leader|string|max:191',

            'emergency_contact_name'  => 'nullable|string|max:191',
            'emergency_contact_phone' => 'nullable|string|max:50',
            'medical_conditions'      => 'nullable|string',
            'allergies'               => 'nullable|string',
            'mobility_needs'          => 'nullable|string',
            'special_needs'           => 'nullable|string',

            'accommodation_required'  => 'nullable|boolean',
            'accommodation_choice'    => 'nullable|string|max:191',
            'accommodation_fee'       => 'nullable|integer',

            'sms_opt_in'              => 'nullable|boolean',
            'admin_notes'             => 'nullable|string',
        ]);

        // Normalize booleans
        $data['accommodation_required'] = (bool) ($data['accommodation_required'] ?? false);
        $data['sms_opt_in'] = (bool) ($data['sms_opt_in'] ?? false);

        // Registration fee is paid first (same tiers as public step 1).
        $feeTiers = [
            'local'         => ['amount' => (int) env('SUMMIT_FEE_LOCAL', 150000), 'currency' => 'UGX'],
            'africa'        => ['amount' => (int) env('SUMMIT_FEE_AFRICA', 50), 'currency' => 'USD'],
            'international' => ['amount' => (int) env('SUMMIT_FEE_INTERNATIONAL', 100), 'currency' => 'USD'],
        ];
        $tier = $feeTiers[$data['country_type']];

        // Accommodation details are captured for logistics but not charged here.
        $data['status'] = 'pending';
        $data['currency'] = $tier['currency'];
        $data['base_fee'] = $tier['amount'];
        $data['total_amount'] = $tier['amount'];
        $data['accommodation_payment_status'] = $data['accommodation_required'] ? 'pending' : 'not_required';
        $data['current_step'] = 3;

        $registration = Registration::create($data);

        // Reuse the existing payment step flow so QR + email happen after successful payment.
        session(['registration_id' => $registration->id]);

        return redirect()->route('admin.registrations.payment', $registration)
            ->with('success', "Manual registration for {$registration->full_name} created. Proceed with registration fee payment.");
    }

    /**
     * Show payment step embedded inside admin dashboard.
     */
    public function payment(Registration $registration): View|RedirectResponse
    {
        if (in_array($registration->status, ['paid', 'checked_in'], true)) {
            return redirect()->route('admin.registrations.show', $registration)
                ->with('info', 'Registration is already paid.');
        }

        // Reuse public step 3 payment submission flow by binding this record to session.
        session(['registration_id' => $registration->id]);

        return view('admin.registrations.payment', compact('registration'));
    }

    /**
     * Check-in a guest by scanning their QR token OR reference number.
     * Accepts:
     *  - A QR token UUID (old format)
     *  - A plain reference like RS-2026-00001
     *  - The full plain-text QR content (new format) — extracts Ref: line
     */
    public function checkIn(string $token): RedirectResponse
    {
        // Decode any URL encoding (e.g. newlines as %0A)
        $input = urldecode($token);

        // Attempt to extract a reference from multi-line QR text
        $reference = null;
        if (str_contains($input, 'Ref:')) {
            if (preg_match('/Ref:\s*(RS[\w-]+)/i', $input, $m)) {
                $reference = $m[1];
            }
        } elseif (preg_match('/^RS[\w-]+$/i', trim($input))) {
            // Bare reference number passed directly
            $reference = trim($input);
        }

        // Look up by reference first, fall back to qr_token
        $reg = $reference
            ? Registration::where('reference', $reference)
                          ->whereIn('status', ['paid', 'checked_in'])
                          ->first()
            : Registration::where('qr_token', trim($input))
                          ->whereIn('status', ['paid', 'checked_in'])
                          ->first();

        if (! $reg) {
            return redirect()->route('admin.checkin')
                ->with('error', '❌ No confirmed registration found for that QR / reference.');
        }

        if ($reg->isCheckedIn()) {
            return redirect()->route('admin.checkin')
                ->with('warning', "⚠ {$reg->full_name} already checked in at " . $reg->checked_in_at->format('D d M Y H:i'));
        }

        $reg->update([
            'status'        => 'checked_in',
            'checked_in_at' => now(),
        ]);

        return redirect()->route('admin.checkin')
            ->with('success', "✅ {$reg->full_name} ({$reg->reference}) checked in successfully!");
    }

    /**
     * Manual check-in by registration ID (POST).
     * Used by the attendee list direct "Check In" button.
     */
    public function checkInById(Registration $registration): RedirectResponse
    {
        if (! in_array($registration->status, ['paid', 'checked_in'])) {
            return redirect()->route('admin.checkin')
                ->with('error', '❌ Registration is not eligible for check-in.');
        }

        if ($registration->isCheckedIn()) {
            return redirect()->route('admin.checkin')
                ->with('warning', "⚠ {$registration->full_name} already checked in at " . $registration->checked_in_at->format('D d M Y H:i'));
        }

        $registration->update([
            'status'        => 'checked_in',
            'checked_in_at' => now(),
        ]);

        return redirect()->route('admin.checkin')
            ->with('success', "✅ {$registration->full_name} ({$registration->reference}) checked in successfully!");
    }

    /**
     * Show the check-in scanner page + attendee list.
     */
    public function checkInPage(Request $request): View
    {
        $todayCount = Registration::where('status', 'checked_in')
            ->whereDate('checked_in_at', today())
            ->count();

        $totalPaid = Registration::whereIn('status', ['paid', 'checked_in'])->count();

        $query = Registration::whereIn('status', ['paid', 'checked_in'])->latest();

        if ($request->filled('search')) {
            $term = '%' . $request->search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('full_name', 'like', $term)
                  ->orWhere('phone', 'like', $term)
                  ->orWhere('email', 'like', $term)
                  ->orWhere('reference', 'like', $term);
            });
        }

        if ($request->filled('origin')) {
            $query->where('country_type', $request->origin);
        }

        $attendees = $query->paginate(20)->withQueryString();

        return view('admin.checkin', compact('todayCount', 'totalPaid', 'attendees'));
    }

    /**
     * Manually resend the QR confirmation email.
     */
    public function resendQr(Registration $registration): RedirectResponse
    {
        if (! $registration->isPaid() && ! $registration->isCheckedIn()) {
            return back()->with('error', 'QR can only be sent to paid registrations.');
        }

        $this->qrService->generateAndDispatch($registration);

        return back()->with('success', 'QR code email queued for resending.');
    }

    /**
     * Export as CSV.
     */
    public function export(Request $request)
    {
        $query = Registration::with('accommodationHotel')->whereIn('status', ['paid', 'checked_in']);

        if ($request->filled('affiliation')) {
            $query->where('affiliation', $request->affiliation);
        }
        if ($request->filled('country_type')) {
            $query->where('country_type', $request->country_type);
        }

        $registrations = $query->orderBy('full_name')->get();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="summit26_registrations_' . now()->format('Ymd_His') . '.csv"',
        ];

        $callback = function () use ($registrations) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Reference', 'Full Name', 'Designation', 'Phone', 'Email', 'Address',
                'Attendee Type', 'Nationality', 'Affiliation',
                'FCC Region', 'FCC Regional Leader', 'FCC Church', 'FCC Pastor',
                'Currency', 'Base Fee', 'Total Amount',
                'Accommodation Hotel', 'Accommodation Mode', 'Room Type', 'Nights', 'Accommodation Currency', 'Accommodation Fee', 'Accommodation Payment Status',
                'Status', 'Checked In At', 'Registered At',
            ]);

            foreach ($registrations as $reg) {
                fputcsv($handle, [
                    $reg->reference,
                    $reg->full_name,
                    $reg->designation . ($reg->designation_specify ? " – {$reg->designation_specify}" : ''),
                    $reg->phone,
                    $reg->email,
                    $reg->address,
                    $reg->country_type,
                    $reg->nationality,
                    $reg->affiliation,
                    $reg->fcc_region,
                    $reg->fcc_regional_leader,
                    $reg->fcc_church,
                    $reg->fcc_pastor,
                    $reg->currency,
                    $reg->base_fee,
                    $reg->total_amount,
                    $reg->accommodationHotel->name ?? $reg->accommodation_choice,
                    $reg->accommodation_booking_mode,
                    $reg->accommodation_room_type,
                    $reg->accommodation_nights,
                    $reg->accommodation_currency,
                    $reg->accommodation_fee,
                    $reg->accommodation_payment_status,
                    $reg->status,
                    $reg->checked_in_at?->format('Y-m-d H:i:s'),
                    $reg->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
