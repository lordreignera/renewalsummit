<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Registration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * SwApp Mobile Money Payment Service
 *
 * Implements the real SwApp OAuth 2.0 API flow:
 *   1. POST /token          → get Bearer token (cached until expiry)
 *   2. POST /validate       → verify phone number is valid
 *   3. POST /collection     → initiate debit (customer pays)
 *   4. POST /transaction-status → poll until confirmed
 *
 * Required .env values:
 *   SWAPP_BASE_URL=https://www.swapp.co.ug/apitest/mm   (test)
 *   SWAPP_CLIENT_ID=           ← from SwApp merchant portal
 *   SWAPP_API_KEY=             ← from SwApp merchant portal
 *   SWAPP_API_SECRET=          ← from SwApp merchant portal
 *   SWAPP_CALLBACK_URL="${APP_URL}/payment/callback"
 *   SWAPP_RETURN_URL="${APP_URL}/registration/complete"
 */
class SwappPaymentService
{
    protected string $baseUrl;
    protected string $clientId;
    protected string $apiKey;
    protected string $apiSecret;
    protected string $callbackUrl;
    protected string $returnUrl;

    public function __construct()
    {
        $this->baseUrl     = rtrim(config('services.swapp.base_url',     env('SWAPP_BASE_URL',     'https://www.swapp.co.ug/apitest/mm')), '/');
        $this->clientId    = config('services.swapp.client_id',          env('SWAPP_CLIENT_ID',    ''));
        $this->apiKey      = config('services.swapp.api_key',            env('SWAPP_API_KEY',      ''));
        $this->apiSecret   = config('services.swapp.api_secret',         env('SWAPP_API_SECRET',   ''));
        $this->callbackUrl = config('services.swapp.callback_url',       env('SWAPP_CALLBACK_URL', url('/payment/callback')));
        $this->returnUrl   = config('services.swapp.return_url',         env('SWAPP_RETURN_URL',   url('/registration/complete')));
    }

    /* ═══════════════════════════════════════════════════════════════
     | STEP 1 — OAuth Token
     ═══════════════════════════════════════════════════════════════ */

    /**
     * Fetch (or return cached) OAuth Bearer token.
     * Token is cached for 50 minutes (most OAuth tokens expire in 60 min).
     */
    public function getToken(): string
    {
        return Cache::remember('swapp_token', now()->addMinutes(50), function () {
            $response = Http::timeout(20)
                ->withHeaders([
                    'Swapp-Client-ID' => $this->clientId,
                    'Authorization'   => 'Basic ' . base64_encode("{$this->apiKey}:{$this->apiSecret}"),
                    'Content-Type'    => 'application/x-www-form-urlencoded',
                ])
                ->asForm()
                ->post("{$this->baseUrl}/token", [
                    'grant_type' => 'client_credentials',
                ]);

            if (! $response->successful()) {
                Log::error('SwApp token request failed', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                throw new \RuntimeException('SwApp: could not obtain access token — ' . $response->body());
            }

            $data = $response->json();
            return $data['access_token'] ?? throw new \RuntimeException('SwApp: access_token missing from token response');
        });
    }

    /* ═══════════════════════════════════════════════════════════════
     | STEP 2 — Validate Customer Phone
     ═══════════════════════════════════════════════════════════════ */

    /**
     * Validate a Mobile Money phone number before charging.
     *
     * @return array{valid: bool, name: string|null, message: string}
     */
    public function validatePhone(string $phone, string $network = 'MTN'): array
    {
        try {
            $response = Http::timeout(20)
                ->withHeaders($this->bearerHeaders())
                ->post("{$this->baseUrl}/validate", [
                    'msisdn'  => $this->normalisePhone($phone),
                    'network' => strtoupper($network),
                ]);

            $data = $response->json();

            return [
                'valid'   => $response->successful() && ($data['status'] ?? '') === 'success',
                'name'    => $data['name']    ?? null,
                'message' => $data['message'] ?? 'Unknown response',
            ];
        } catch (\Exception $e) {
            Log::error('SwApp validate error', ['error' => $e->getMessage()]);
            return ['valid' => false, 'name' => null, 'message' => $e->getMessage()];
        }
    }

    /* ═══════════════════════════════════════════════════════════════
     | STEP 3 — Initiate Collection (customer pays)
     ═══════════════════════════════════════════════════════════════ */

    /**
     * Charge a customer's Mobile Money wallet.
     * Sends a USSD prompt to the customer's phone.
     *
     * @return array{success: bool, payment: Payment, request_id: string|null, message: string}
     */
    public function initiateMobileMoney(Registration $registration, string $phone, string $network = 'MTN'): array
    {
        $requestId = (string) Str::uuid();

        $payment = Payment::create([
            'registration_id' => $registration->id,
            'payment_method'  => 'mobile_money',
            'phone_number'    => $phone,
            'network'         => strtoupper($network),
            'amount'          => $registration->total_amount,
            'currency'        => 'UGX',
            'status'          => 'initiated',
            'swapp_reference' => $requestId,
        ]);

        try {
            $response = Http::timeout(30)
                ->withHeaders($this->bearerHeaders())
                ->post("{$this->baseUrl}/collection", [
                    'RequestId'   => $requestId,
                    'msisdn'      => $this->normalisePhone($phone),
                    'amount'      => (string) $registration->total_amount,
                    'narration'   => "Renewal Summit 2026 – {$registration->reference}",
                    'CallbackUrl' => $this->callbackUrl,
                ]);

            $data = $response->json();

            Log::info('SwApp collection response', ['data' => $data, 'status' => $response->status()]);

            $payment->update([
                'swapp_response'       => $data,
                'swapp_transaction_id' => $data['TransactionId'] ?? $data['transaction_id'] ?? null,
                'status'               => $response->successful() ? 'pending' : 'failed',
                'failure_reason'       => $response->failed() ? ($data['message'] ?? 'Gateway error') : null,
            ]);

            return [
                'success'    => $response->successful(),
                'payment'    => $payment->fresh(),
                'request_id' => $requestId,
                'message'    => $data['message'] ?? ($response->successful()
                    ? 'A payment prompt has been sent to ' . $phone . '. Please enter your PIN to confirm.'
                    : 'Payment request failed. Please try again.'),
            ];

        } catch (\Exception $e) {
            Log::error('SwApp collection failed', [
                'error'           => $e->getMessage(),
                'registration_id' => $registration->id,
            ]);
            $payment->update(['status' => 'failed', 'failure_reason' => $e->getMessage()]);

            return ['success' => false, 'payment' => $payment, 'request_id' => null, 'message' => 'Payment initiation failed. Please try again.'];
        }
    }

    /* ═══════════════════════════════════════════════════════════════
     | STEP 4 — Check Transaction Status
     ═══════════════════════════════════════════════════════════════ */

    /**
     * Poll SwApp for the latest status of a transaction.
     *
     * @return array{status: string, message: string, raw: array}
     */
    public function checkStatus(string $requestId): array
    {
        try {
            $response = Http::timeout(20)
                ->withHeaders($this->bearerHeaders())
                ->post("{$this->baseUrl}/transaction-status", [
                    'RequestId' => $requestId,
                ]);

            $data = $response->json();

            return [
                'status'  => $this->mapStatus(strtolower($data['status'] ?? $data['Status'] ?? '')),
                'message' => $data['message'] ?? $data['Message'] ?? '',
                'raw'     => $data,
            ];
        } catch (\Exception $e) {
            return ['status' => 'pending', 'message' => $e->getMessage(), 'raw' => []];
        }
    }

    /* ═══════════════════════════════════════════════════════════════
     | Callback Handler
     ═══════════════════════════════════════════════════════════════ */

    /**
     * Handle the SwApp webhook callback (POST from SwApp server).
     */
    public function handleCallback(array $payload): bool
    {
        Log::info('SwApp callback received', $payload);

        $requestId = $payload['RequestId'] ?? $payload['request_id'] ?? null;
        $txId      = $payload['TransactionId'] ?? $payload['transaction_id'] ?? null;
        $status    = strtolower($payload['Status'] ?? $payload['status'] ?? '');

        $payment = Payment::where('swapp_reference', $requestId)
            ->orWhere('swapp_transaction_id', $txId)
            ->first();

        if (! $payment) {
            Log::warning('SwApp callback: payment not found', $payload);
            return false;
        }

        $mapped = $this->mapStatus($status);

        $payment->update([
            'status'          => $mapped,
            'swapp_response'  => $payload,
            'paid_at'         => $mapped === 'success' ? now() : null,
            'failure_reason'  => in_array($mapped, ['failed', 'cancelled']) ? ($payload['message'] ?? $payload['Message'] ?? null) : null,
        ]);

        if ($mapped === 'success') {
            $payment->registration->update(['status' => 'paid']);
        }

        return true;
    }

    /* ═══════════════════════════════════════════════════════════════
     | Donation Collection
     ═══════════════════════════════════════════════════════════════ */

    public function initiateDonationMobileMoney(array $donationData): array
    {
        $requestId = (string) Str::uuid();

        try {
            $response = Http::timeout(30)
                ->withHeaders($this->bearerHeaders())
                ->post("{$this->baseUrl}/collection", [
                    'RequestId'   => $requestId,
                    'msisdn'      => $this->normalisePhone($donationData['phone']),
                    'amount'      => (string) $donationData['amount'],
                    'narration'   => 'Renewal Summit 2026 Donation',
                    'CallbackUrl' => url('/donation/callback'),
                ]);

            return [
                'success'    => $response->successful(),
                'request_id' => $requestId,
                'data'       => $response->json(),
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'request_id' => null, 'data' => [], 'message' => $e->getMessage()];
        }
    }

    /* ═══════════════════════════════════════════════════════════════
     | Account Balance
     ═══════════════════════════════════════════════════════════════ */

    public function getBalance(): array
    {
        try {
            $response = Http::timeout(15)
                ->withHeaders($this->bearerHeaders())
                ->post("{$this->baseUrl}/balance", []);

            return $response->json() ?? [];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /* ═══════════════════════════════════════════════════════════════
     | Private Helpers
     ═══════════════════════════════════════════════════════════════ */

    private function bearerHeaders(): array
    {
        return [
            'Authorization'   => 'Bearer ' . $this->getToken(),
            'Swapp-Client-ID' => $this->clientId,
            'Accept'          => 'application/json',
            'Content-Type'    => 'application/json',
        ];
    }

    private function normalisePhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);
        if (str_starts_with($phone, '0')) {
            $phone = '256' . substr($phone, 1);
        }
        // Already has country code without +
        if (str_starts_with($phone, '256') && strlen($phone) === 12) {
            return $phone;
        }
        return $phone;
    }

    private function mapStatus(string $swappStatus): string
    {
        return match (true) {
            in_array($swappStatus, ['success', 'successful', 'completed', 'approved']) => 'success',
            in_array($swappStatus, ['pending', 'processing', 'initiated', 'queued'])   => 'pending',
            in_array($swappStatus, ['failed', 'error', 'declined'])                    => 'failed',
            in_array($swappStatus, ['cancelled', 'canceled'])                          => 'cancelled',
            default => 'pending',
        };
    }
}

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
