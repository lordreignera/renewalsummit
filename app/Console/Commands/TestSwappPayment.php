<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class TestSwappPayment extends Command
{
    protected $signature   = 'swapp:test {phone=0782743720} {amount=500}';
    protected $description = 'Test SwApp Mobile Money API: token → balance → payout → status';

    public function handle(): int
    {
        $phone  = $this->argument('phone');
        $amount = (int) $this->argument('amount');

        $baseUrl   = rtrim(config('services.swapp.base_url',   env('SWAPP_BASE_URL',   'https://www.swapp.co.ug/apitest/mm')), '/');
        $clientId  = config('services.swapp.client_id',         env('SWAPP_CLIENT_ID',  ''));
        $apiKey    = config('services.swapp.api_key',            env('SWAPP_API_KEY',    ''));
        $apiSecret = config('services.swapp.api_secret',         env('SWAPP_API_SECRET', ''));

        // Per docs: Account must be WITHOUT international code or leading 0
        // e.g. 0782743720 → 782743720
        $account = $this->normalisePhone($phone);

        $this->info("Base URL : {$baseUrl}");
        $this->info("Client ID: {$clientId}");
        $this->info("Phone    : {$phone}  →  Account: {$account}");
        $this->info("Amount   : {$amount} UGX");
        $this->newLine();

        // ── STEP 1: Get OAuth token ───────────────────────────────────────
        // Docs: POST /token  |  Header: Swapp-Client-ID + Authorization: Basic base64(key:secret)
        $this->line('<fg=yellow>STEP 1: Getting OAuth token...</>');

        $tokenResp = Http::timeout(20)
            ->withoutVerifying()
            ->withHeaders([
                'Swapp-Client-ID' => $clientId,
                'Authorization'   => 'Basic ' . base64_encode("{$apiKey}:{$apiSecret}"),
                'Content-Type'    => 'application/x-www-form-urlencoded',
            ])
            ->asForm()
            ->post("{$baseUrl}/token", ['grant_type' => 'client_credentials']);

        $this->line("Status : {$tokenResp->status()}");
        $this->line("Body   : " . $tokenResp->body());
        $this->newLine();

        if (! $tokenResp->successful()) {
            $this->error('Token request failed. Check credentials and base URL.');
            return 1;
        }

        $token = $tokenResp->json('access_token');
        if (! $token) {
            $this->error('No access_token in response: ' . $tokenResp->body());
            return 1;
        }
        $this->info('Token: ' . substr($token, 0, 40) . '...');
        $this->newLine();

        $headers = [
            'Swapp-Client-ID' => $clientId,
            'Authorization'   => "Bearer {$token}",
            'Content-Type'    => 'application/json',
        ];

        // ── STEP 2: Get Account Balance ───────────────────────────────────
        // Docs: POST /balance  |  No body required
        $this->line('<fg=yellow>STEP 2: Checking account balance...</>');

        $balResp = Http::timeout(15)
            ->withoutVerifying()
            ->withHeaders($headers)
            ->post("{$baseUrl}/balance");

        $this->line("Status : {$balResp->status()}");
        $this->line("Body   : " . $balResp->body());
        $this->newLine();

        // ── STEP 3: Collect (USSD prompt to customer) ────────────────────
        // POST /collect  — pulls money FROM customer, sends USSD pop-up
        $requestId = (string) Str::uuid();
        $this->line('<fg=yellow>STEP 3: Initiating collection (USSD prompt)...</>');
        $this->line("Account   : {$account}");
        $this->line("Amount    : {$amount}");
        $this->line("RequestId : {$requestId}");

        $collectResp = Http::timeout(30)
            ->withoutVerifying()
            ->withHeaders($headers)
            ->post("{$baseUrl}/collect", [
                'Account'   => $account,
                'Amount'    => $amount,
                'RequestId' => $requestId,
            ]);

        $this->line("Status : {$collectResp->status()}");
        $this->line("Body   : " . $collectResp->body());
        $this->newLine();

        // ── STEP 4: Transaction Status ────────────────────────────────────
        $this->line('<fg=yellow>STEP 4: Checking transaction status...</>');
        $this->line("RequestId : {$requestId}");

        sleep(3); // give it a moment before polling

        $statusResp = Http::timeout(20)
            ->withoutVerifying()
            ->withHeaders($headers)
            ->post("{$baseUrl}/getstatus", [
                'RequestId' => $requestId,
            ]);

        $this->line("Status : {$statusResp->status()}");
        $this->line("Body   : " . $statusResp->body());
        $this->newLine();

        return 0;
    }

    /**
     * Strip leading 0 or country code (256/+256) per SwApp docs:
     * "The number should be without international codes or the leading 0."
     * e.g. 0782743720 → 782743720
     */
    private function normalisePhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);
        // Remove country code 256
        if (str_starts_with($phone, '256')) {
            $phone = substr($phone, 3);
        }
        // Remove leading 0
        if (str_starts_with($phone, '0')) {
            $phone = substr($phone, 1);
        }
        return $phone;
    }
}
