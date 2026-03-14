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
 *   1. POST /token          â†’ get Bearer token (cached until expiry)
 *   2. POST /validate       â†’ verify phone number is valid
 *   3. POST /collection     â†’ initiate debit (customer pays)
 *   4. POST /transaction-status â†’ poll until confirmed
 *
 * Required .env values:
 *   SWAPP_BASE_URL=https://www.swapp.co.ug/apitest/mm   (test)
 *   SWAPP_CLIENT_ID=           â† from SwApp merchant portal
 *   SWAPP_API_KEY=             â† from SwApp merchant portal
 *   SWAPP_API_SECRET=          â† from SwApp merchant portal
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

    /* â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     | STEP 1 â€” OAuth Token
     â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• */

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
                throw new \RuntimeException('SwApp: could not obtain access token â€” ' . $response->body());
            }

            $data = $response->json();
            return $data['access_token'] ?? throw new \RuntimeException('SwApp: access_token missing from token response');
        });
    }

    /* â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     | STEP 2 â€” Validate Customer Phone
     â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• */

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
                    'Account' => $this->normalisePhone($phone),
                    'Network' => $network,
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

    /* â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     | STEP 3 â€” Initiate Collection (customer pays)
     â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• */

    /**
     * Charge a customer's Mobile Money wallet.
     * Sends a USSD prompt to the customer's phone.
     *
     * @return array{success: bool, payment: Payment, request_id: string|null, message: string}
     */
    /**
     * Detect MTN or AIRTEL from a Ugandan phone number prefix.
     */
    public static function detectNetwork(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);
        if (str_starts_with($digits, '256')) {
            $digits = '0' . substr($digits, 3);
        }
        $prefix = substr($digits, 0, 3);
        if (in_array($prefix, ['077', '078', '076', '039', '031'])) return 'MTN';
        if (in_array($prefix, ['070', '075', '074', '020']))          return 'Airtel';
        return 'MTN'; // default
    }

    public function initiateMobileMoney(Registration $registration, string $phone, string $network = ''): array
    {
        if (empty($network)) {
            $network = self::detectNetwork($phone);
        }
        // preserve casing returned by detectNetwork (e.g. "Airtel", "MTN")
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
                    'Account'     => $this->normalisePhone($phone),
                    'Network'     => $network,
                    'Amount'      => (string) $registration->total_amount,
                    'Narration'   => "Renewal Summit 2026 – {$registration->reference}",
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

    /* â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     | STEP 4 â€” Check Transaction Status
     â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• */

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

    /* â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     | Callback Handler
     â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• */

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

    /* â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     | Donation Collection
     â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• */

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

    /* â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     | Account Balance
     â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• */

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

    /* â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
     | Private Helpers
     â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• */

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
