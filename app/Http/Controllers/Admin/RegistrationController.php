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
        $query = Registration::with('latestPayment')->latest();

        // Filters
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

        $registrations = $query->paginate(25)->withQueryString();

        return view('admin.registrations.index', compact('registrations'));
    }

    public function show(Registration $registration): View
    {
        $registration->load('payments');
        return view('admin.registrations.show', compact('registration'));
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
            if (preg_match('/Ref:\s*(RS-[\w-]+)/i', $input, $m)) {
                $reference = $m[1];
            }
        } elseif (preg_match('/^RS-[\w-]+$/i', trim($input))) {
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
        $query = Registration::whereIn('status', ['paid', 'checked_in']);

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
