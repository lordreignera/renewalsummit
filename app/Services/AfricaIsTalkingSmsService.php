<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Africa is Talking SMS Service
 *
 * Handles SMS notifications via Africa is Talking API.
 *
 * Required .env values:
 *   AFRICAISTALKING_USERNAME=your_username
 *   AFRICAISTALKING_API_KEY=your_api_key
 */
class AfricaIsTalkingSmsService
{
    protected string $username;
    protected string $apiKey;
    protected string $baseUrl = 'https://api.sandbox.africastalking.com'; // Switch to https://api.africastalking.com for production

    public function __construct()
    {
        $this->username = config('services.africaistalking.username', env('AFRICAISTALKING_USERNAME', ''));
        $this->apiKey   = config('services.africaistalking.api_key', env('AFRICAISTALKING_API_KEY', ''));
    }

    /**
     * Send SMS to a single recipient
     *
     * @param string $phoneNumber Phone number in E.164 format (e.g., +256700000000)
     * @param string $message SMS message content
     * @return array API response
     */
    public function send(string $phoneNumber, string $message): array
    {
        if (!$this->username || !$this->apiKey) {
            Log::error('Africa is Talking credentials not configured');
            return ['success' => false, 'error' => 'SMS service not configured'];
        }

        $senderId = config('services.africaistalking.sender_id', env('AFRICAISTALKING_SENDER_ID', ''));

        $payload = [
            'username' => $this->username,
            'to'       => $phoneNumber,
            'message'  => $message,
        ];

        // Only include 'from' if a sender ID is configured
        if ($senderId) {
            $payload['from'] = $senderId;
        }

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'apiKey' => $this->apiKey,
            ])->asForm()->post("{$this->baseUrl}/version1/messaging", $payload);

            $data = $response->json();

            if ($response->successful()) {
                Log::info('SMS sent successfully', [
                    'phone' => $phoneNumber,
                    'response' => $data,
                ]);
                return ['success' => true, 'data' => $data];
            } else {
                Log::warning('SMS send failed', [
                    'phone' => $phoneNumber,
                    'status' => $response->status(),
                    'response' => $data,
                ]);
                return ['success' => false, 'error' => $data['error'] ?? 'Unknown error', 'raw' => $data];
            }
        } catch (\Exception $e) {
            Log::error('SMS service error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send SMS to multiple recipients
     *
     * @param array $phoneNumbers Array of phone numbers
     * @param string $message SMS message content
     * @return array Results for each recipient
     */
    public function sendBulk(array $phoneNumbers, string $message): array
    {
        $results = [];
        foreach ($phoneNumbers as $phone) {
            $results[$phone] = $this->send($phone, $message);
        }
        return $results;
    }

    /**
     * Send registration confirmation SMS with QR code link
     *
     * @param string $phoneNumber User's phone number
     * @param string $name User's name
     * @param string $reference Registration reference number
     * @param string|null $qrUrl URL to view the QR code
     * @return array API response
     */
    public function sendRegistrationConfirmation(string $phoneNumber, string $name, string $reference, ?string $qrUrl = null): array
    {
        // Keep message short — Uganda carriers filter longer messages from unregistered senders
        $firstName = explode(' ', trim($name))[0];
        $message   = "Hi {$firstName}, RS2026 reg confirmed. Ref: {$reference}. Check email for QR code.";

        return $this->send($phoneNumber, $message);
    }
}
