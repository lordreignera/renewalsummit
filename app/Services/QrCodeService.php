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
        $verifyUrl = url("/verify/{$registration->qr_token}");

        $qrCode = QrCode::create($verifyUrl)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::High)
            ->setSize(300)
            ->setMargin(10)
            ->setRoundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));

        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        $path = "qrcodes/{$registration->reference}.png";

        Storage::disk('public')->put($path, $result->getString());

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

        return Storage::disk('public')->url($registration->qr_code_path);
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
