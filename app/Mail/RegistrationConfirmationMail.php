<?php

namespace App\Mail;

use App\Models\Registration;
use App\Services\QrCodeService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
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

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('renewalsummit@africarenewal.org', 'Renewal Summit 2026'),
            subject: '✅ Registration Confirmed – Renewal Summit 2026 | ' . $this->registration->reference,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.registration-confirmation',
            with: [
                'registration' => $this->registration,
                'qrUrl'        => $this->registration->qr_code_path
                    ? route('qr.show', ['reference' => $this->registration->reference])
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
            Storage::disk(config('filesystems.qr_disk', 'r2'))->exists($this->registration->qr_code_path)) {
            return [
                Attachment::fromStorageDisk(config('filesystems.qr_disk', 'r2'), $this->registration->qr_code_path)
                    ->as('RS2026-QRCode-' . $this->registration->reference . '.png')
                    ->withMime('image/png'),
            ];
        }
        return [];
    }

    /**
     * Generate QR if needed, then send the confirmation email synchronously.
     */
    public static function dispatchToRegistration(Registration $registration): void
    {
        if (! $registration->email) return;

        if (! $registration->qr_code_path) {
            $path = app(QrCodeService::class)->generateForRegistration($registration);
            $registration->update(['qr_code_path' => $path]);
            $registration->refresh();
        }

        // send() is synchronous — no queue worker needed for demo/testing.
        // Switch to ->queue() or ->later() when a real queue worker is running.
        Mail::to($registration->email)->send(new static($registration));
        $registration->update(['qr_sent_at' => now()]);
    }
}
