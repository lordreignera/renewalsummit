<?php

namespace App\Services;

use App\Models\Registration;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class QrCodeService
{
    /**
     * Generate a QR code image for the registration and save to storage.
     * Returns the storage path relative to disk root.
     */
    public function generateForRegistration(Registration $registration): string
    {
        $typeLabel = match($registration->country_type) {
            'local'         => 'Ugandan',
            'africa'        => 'Rest of Africa',
            'international' => 'International',
            default         => ucfirst($registration->country_type ?? ''),
        };

        $desigLabel = match($registration->designation) {
            'fcc_regional_leader' => 'FCC Regional Leader',
            'senior_pastor'       => 'Senior Pastor',
            'church_leader'       => 'Church Leader' . ($registration->designation_specify ? ' – ' . $registration->designation_specify : ''),
            'corporate'           => 'Corporate / Organisation',
            default               => ucfirst($registration->designation ?? ''),
        };

        $qrData = implode("\n", [
            '=== RENEWAL SUMMIT 2026 ===',
            'Ref: '         . $registration->reference,
            'Name: '        . $registration->full_name,
            'Designation: ' . $desigLabel,
            'Type: '        . $typeLabel,
            'Affiliation: ' . strtoupper($registration->affiliation ?? ''),
            'Status: '      . strtoupper($registration->status ?? ''),
            'Fee Paid: '    . $registration->formattedTotal,
            'Phone: '       . $registration->phone,
        ]);

        $qrCode = new QrCode(
            data:                 $qrData,
            encoding:             new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            size:                 350,
            margin:               10,
            roundBlockSizeMode:   RoundBlockSizeMode::Margin,
            foregroundColor:      new Color(0, 0, 0),
            backgroundColor:      new Color(255, 255, 255),
        );

        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        $path = "qrcodes/{$registration->reference}.png";

        $disk = config('filesystems.qr_disk', 'r2');
        Storage::disk($disk)->put($path, $result->getString(), 'public');

        return $path;
    }

    /**
     * Get public URL of a registration QR code.
     */
    public function getPublicUrl(Registration $registration): ?string
    {
        if (! $registration->qr_code_path) {
            return null;
        }

        return route('qr.show', ['reference' => $registration->reference]);
    }

    /**
     * Generate QR, save path on model, then send the confirmation email.
     */
    public function generateAndDispatch(Registration $registration): void
    {
        try {
            $path = $this->generateForRegistration($registration);

            $registration->update([
                'qr_code_path' => $path,
                'qr_sent_at'   => null, // will be set after mail sent
            ]);

            // Dispatch mail job
            \App\Mail\RegistrationConfirmationMail::dispatchToRegistration($registration);

        } catch (\Exception $e) {
            Log::error('QR generation failed', [
                'registration_id' => $registration->id,
                'error'           => $e->getMessage(),
            ]);
        }
    }

    /**
     * Verify a QR token. Returns the Registration or null.
     */
    public function verify(string $token): ?Registration
    {
        return Registration::where('qr_token', $token)
            ->whereIn('status', ['paid', 'checked_in'])
            ->first();
    }
}
