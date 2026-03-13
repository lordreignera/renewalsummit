<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Registration;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Swapp Payment Gateway Service
 *
 * Integrates with the Swapp payment API for Mobile Money (MTN/Airtel)
 * and VISA card payments.
 *
 * Set these in .env:
 *   SWAPP_BASE_URL=https://api.swapp.ug/v1
 *   SWAPP_API_KEY=your_api_key
 *   SWAPP_SECRET_KEY=your_secret_key
 *   SWAPP_CALLBACK_URL=https://yourdomain.com/payment/callback
 *   SWAPP_RETURN_URL=https://yourdomain.com/registration/complete
 */
class SwappPaymentService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $secretKey;
    protected string $callbackUrl;
    protected string $returnUrl;

    public function __construct()
    {
        $this->baseUrl     = rtrim(config('services.swapp.base_url', env('SWAPP_BASE_URL', 'https://api.swapp.ug/v1')), '/');
        $this->apiKey      = config('services.swapp.api_key',    env('SWAPP_API_KEY', ''));
        $this->secretKey   = config('services.swapp.secret_key', env('SWAPP_SECRET_KEY', ''));
        $this->callbackUrl = config('services.swapp.callback_url', env('SWAPP_CALLBACK_URL', url('/payment/callback')));
        $this->returnUrl   = config('services.swapp.return_url',   env('SWAPP_RETURN_URL',   url('/registration/complete')));
    }

    /* ─── Mobile Money ───────────────────────────────────────────── */

    /**
     * Initiate a Mobile Money payment (MTN or Airtel Uganda).
     *
     * @param  Registration  $registration
     * @param  string        $phone    e.g. 0772123456 or 256772123456
     * @param  string        $network  MTN | AIRTEL
     * @return array{success: bool, payment: Payment, message: string}
     */
    public function initiateMobileMoney(Registration $registration, string $phone, string $network = 'MTN'): array
    {
        $payment = Payment::create([
            'registration_id' => $registration->id,
            'payment_method'  => 'mobile_money',
            'phone_number'    => $phone,
            'network'         => strtoupper($network),
            'amount'          => $registration->total_amount,
            'currency'        => 'UGX',
            'status'          => 'initiated',
        ]);

        try {
            $response = Http::withHeaders($this->headers())
                ->timeout(30)
                ->post("{$this->baseUrl}/payments/mobile-money", [
                    'reference'    => $registration->reference,
                    'amount'       => $registration->total_amount,
                    'currency'     => 'UGX',
                    'phone'        => $this->normalisePhone($phone),
                    'network'      => strtoupper($network),
                    'description'  => "Renewal Summit 2026 Registration - {$registration->reference}",
                    'callback_url' => $this->callbackUrl,
                    'return_url'   => $this->returnUrl,
                    'metadata'     => [
                        'registration_id'  => $registration->id,
                        'payment_id'       => $payment->id,
                        'attendee'         => $registration->full_name,
                    ],
                ]);

            return $this->handleInitiateResponse($response, $payment);

        } catch (\Exception $e) {
            Log::error('Swapp MM initiation failed', [
                'error'           => $e->getMessage(),
                'registration_id' => $registration->id,
            ]);

            $payment->update(['status' => 'failed', 'failure_reason' => $e->getMessage()]);

            return ['success' => false, 'payment' => $payment, 'message' => 'Payment initiation failed. Please try again.'];
        }
    }

    /* ─── VISA / Card ────────────────────────────────────────────── */

    /**
     * Create a VISA card checkout session.
     * Returns a redirect URL for the hosted payment page.
     *
     * @return array{success: bool, payment: Payment, redirect_url: string|null, message: string}
     */
    public function initiateVisa(Registration $registration): array
    {
        $payment = Payment::create([
            'registration_id' => $registration->id,
            'payment_method'  => 'visa',
            'amount'          => $registration->total_amount,
            'currency'        => 'UGX',
            'status'          => 'initiated',
        ]);

        try {
            $response = Http::withHeaders($this->headers())
                ->timeout(30)
                ->post("{$this->baseUrl}/payments/card", [
                    'reference'    => $registration->reference,
                    'amount'       => $registration->total_amount,
                    'currency'     => 'UGX',
                    'email'        => $registration->email,
                    'name'         => $registration->full_name,
                    'description'  => "Renewal Summit 2026 Registration - {$registration->reference}",
                    'callback_url' => $this->callbackUrl,
                    'return_url'   => $this->returnUrl . '?ref=' . $registration->reference,
                    'metadata'     => [
                        'registration_id' => $registration->id,
                        'payment_id'      => $payment->id,
                    ],
                ]);

            $data = $response->json();
            $payment->update([
                'swapp_response'       => $data,
                'swapp_transaction_id' => $data['transaction_id'] ?? null,
                'swapp_reference'      => $data['reference'] ?? null,
                'status'               => 'pending',
            ]);

            return [
                'success'      => $response->successful() && isset($data['checkout_url']),
                'payment'      => $payment->fresh(),
                'redirect_url' => $data['checkout_url'] ?? null,
                'message'      => $data['message'] ?? 'Redirecting to payment page...',
            ];

        } catch (\Exception $e) {
            Log::error('Swapp VISA initiation failed', ['error' => $e->getMessage()]);
            $payment->update(['status' => 'failed', 'failure_reason' => $e->getMessage()]);

            return ['success' => false, 'payment' => $payment, 'redirect_url' => null, 'message' => 'Card payment initiation failed.'];
        }
    }

    /* ─── Callback / Verification ────────────────────────────────── */

    /**
     * Handle the Swapp callback / webhook.
     * Called from PaymentController@callback.
     */
    public function handleCallback(array $payload): bool
    {
        Log::info('Swapp callback received', $payload);

        $transactionId = $payload['transaction_id'] ?? null;
        $reference     = $payload['reference'] ?? null;
        $status        = strtolower($payload['status'] ?? '');

        $payment = Payment::where('swapp_transaction_id', $transactionId)
            ->orWhere('swapp_reference', $reference)
            ->first();

        if (! $payment) {
            Log::warning('Swapp callback: payment not found', $payload);
            return false;
        }

        $payment->update([
            'status'          => $this->mapStatus($status),
            'swapp_response'  => $payload,
            'paid_at'         => $status === 'success' ? now() : null,
            'failure_reason'  => $payload['message'] ?? null,
        ]);

        if ($status === 'success') {
            $payment->registration->update(['status' => 'paid']);
        }

        return true;
    }

    /**
     * Verify a transaction directly with Swapp API.
     */
    public function verifyTransaction(string $transactionId): array
    {
        try {
            $response = Http::withHeaders($this->headers())
                ->timeout(15)
                ->get("{$this->baseUrl}/payments/{$transactionId}/verify");

            return $response->json() ?? [];
        } catch (\Exception $e) {
            Log::error('Swapp verification failed', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /* ─── Donation payment ───────────────────────────────────────── */

    public function initiateDonationMobileMoney(array $donationData): array
    {
        try {
            $response = Http::withHeaders($this->headers())
                ->timeout(30)
                ->post("{$this->baseUrl}/payments/mobile-money", [
                    'reference'    => 'DONATE-' . time(),
                    'amount'       => $donationData['amount'],
                    'currency'     => 'UGX',
                    'phone'        => $this->normalisePhone($donationData['phone']),
                    'network'      => strtoupper($donationData['network'] ?? 'MTN'),
                    'description'  => 'Renewal Summit 2026 Donation',
                    'callback_url' => url('/donation/callback'),
                    'metadata'     => ['donor' => $donationData['donor_name']],
                ]);

            return ['success' => $response->successful(), 'data' => $response->json()];
        } catch (\Exception $e) {
            return ['success' => false, 'data' => [], 'message' => $e->getMessage()];
        }
    }

    /* ─── Private helpers ────────────────────────────────────────── */

    private function headers(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->apiKey,
            'X-Secret-Key'  => $this->secretKey,
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
        ];
    }

    private function normalisePhone(string $phone): string
    {
        // Convert 0772... to 256772...
        $phone = preg_replace('/\D/', '', $phone);
        if (str_starts_with($phone, '0')) {
            $phone = '256' . substr($phone, 1);
        }
        return $phone;
    }

    private function handleInitiateResponse(Response $response, Payment $payment): array
    {
        $data = $response->json();

        $payment->update([
            'swapp_response'       => $data,
            'swapp_transaction_id' => $data['transaction_id'] ?? null,
            'swapp_reference'      => $data['reference'] ?? null,
            'status'               => $response->successful() ? 'pending' : 'failed',
            'failure_reason'       => $response->failed() ? ($data['message'] ?? 'Gateway error') : null,
        ]);

        return [
            'success' => $response->successful(),
            'payment' => $payment->fresh(),
            'message' => $data['message'] ?? ($response->successful() ? 'Payment request sent to your phone.' : 'Payment failed.'),
        ];
    }

    private function mapStatus(string $swappStatus): string
    {
        return match ($swappStatus) {
            'success', 'successful', 'completed' => 'success',
            'pending', 'processing'              => 'pending',
            'failed', 'error'                    => 'failed',
            'cancelled'                          => 'cancelled',
            default                              => 'pending',
        };
    }
}
