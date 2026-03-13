<?php

namespace App\Mail;

use App\Models\Registration;
use App\Services\QrCodeService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class RegistrationConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Registration $registration) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '✅ Renewal Summit 2026 Registration Confirmed – ' . $this->registration->reference,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.registration-confirmation',
            with: [
                'registration' => $this->registration,
                'qrUrl'        => $this->registration->qr_code_path
                    ? Storage::disk('public')->url($this->registration->qr_code_path)
                    : null,
            ],
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        if ($this->registration->qr_code_path &&
            Storage::disk('public')->exists($this->registration->qr_code_path)) {
            return [
                Attachment::fromStorageDisk('public', $this->registration->qr_code_path)
                    ->as('RS2026-QRCode-' . $this->registration->reference . '.png')
                    ->withMime('image/png'),
            ];
        }
        return [];
    }

    /**
     * Generate QR if needed, then queue the confirmation email.
     */
    public static function dispatchToRegistration(Registration $registration): void
    {
        if (! $registration->email) return;

        if (! $registration->qr_code_path) {
            app(QrCodeService::class)->generateForRegistration($registration);
            $registration->refresh();
        }

        Mail::to($registration->email)->queue(new static($registration));
        $registration->update(['qr_sent_at' => now()]);
    }
}
