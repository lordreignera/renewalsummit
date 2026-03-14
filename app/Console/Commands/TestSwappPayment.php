<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class TestSwappPayment extends Command
{
    protected $signature   = 'swapp:test {phone=0708356505} {amount=500} {network=auto}';
    protected $description = 'Send a test SwApp Mobile Money payment prompt (network auto-detected)';

    public function handle(): int
    {
        $phone   = $this->argument('phone');
        $amount  = $this->argument('amount');
        $network = strtoupper($this->argument('network'));

        if ($network === 'AUTO') {
            $network = $this->detectNetwork($phone);
            $this->info("Auto-detected network: {$network}");
        }

        $baseUrl   = rtrim(config('services.swapp.base_url',   env('SWAPP_BASE_URL',   'https://www.swapp.co.ug/apitest/mm')), '/');
        $clientId  = config('services.swapp.client_id',         env('SWAPP_CLIENT_ID',  ''));
        $apiKey    = config('services.swapp.api_key',            env('SWAPP_API_KEY',    ''));
        $apiSecret = config('services.swapp.api_secret',         env('SWAPP_API_SECRET', ''));

        $this->info("Base URL : {$baseUrl}");
        $this->info("Client ID: {$clientId}");
        $this->info("Phone    : {$phone}");
        $this->info("Amount   : {$amount} UGX");
        $this->newLine();

        // ── STEP 1: Get token ─────────────────────────────────────────────
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
            $this->error('Token request failed. Check credentials and base URL above.');
            return 1;
        }

        $token = $tokenResp->json('access_token');
        if (! $token) {
            $this->error('No access_token in response: ' . $tokenResp->body());
            return 1;
        }
        $this->info("Token obtained: " . substr($token, 0, 30) . '...');
        $this->newLine();

        // ── STEP 2: Validate phone ────────────────────────────────────────
        $this->line('<fg=yellow>STEP 2: Validating phone ' . $phone . '...</>');

        $msisdn = $this->normalisePhone($phone);
        $this->line("MSISDN : {$msisdn}");

        // Try with Network field
        $validateResp = Http::timeout(20)
            ->withoutVerifying()
            ->withHeaders([
                'Swapp-Client-ID' => $clientId,
                'Authorization'   => "Bearer {$token}",
                'Accept'          => 'application/json',
                'Content-Type'    => 'application/json',
            ])
            ->post("{$baseUrl}/validate", [
                'Account' => $msisdn,
                'Network' => $network,
            ]);

        $this->line("With Network={$network}: {$validateResp->status()} | " . $validateResp->body());

        // Try without Network field
        $validateResp2 = Http::timeout(20)
            ->withoutVerifying()
            ->withHeaders([
                'Swapp-Client-ID' => $clientId,
                'Authorization'   => "Bearer {$token}",
                'Accept'          => 'application/json',
                'Content-Type'    => 'application/json',
            ])
            ->post("{$baseUrl}/validate", [
                'Account' => $msisdn,
            ]);

        $this->line("Without Network:      {$validateResp2->status()} | " . $validateResp2->body());
        $this->newLine();

        // ── STEP 3: Try collection — multiple endpoint + field combos ────────
        $this->line('<fg=yellow>STEP 3: Sending payment prompt...</>');

        $callbackUrl = env('SWAPP_CALLBACK_URL', url('/payment/callback'));
        $endpoints   = ['/collection', '/collections', '/requestpayment', '/payment'];

        foreach ($endpoints as $ep) {
            $rid = (string) Str::uuid();
            $this->line("  POST {$baseUrl}{$ep}  [Account={$msisdn}, Network={$network}]");
            $r = Http::timeout(20)->withoutVerifying()
                ->withHeaders(['Swapp-Client-ID' => $clientId, 'Authorization' => "Bearer {$token}", 'Accept' => 'application/json', 'Content-Type' => 'application/json'])
                ->post("{$baseUrl}{$ep}", [
                    'RequestId'   => $rid,
                    'Account'     => $msisdn,
                    'Network'     => $network,
                    'Amount'      => (string) $amount,
                    'Narration'   => 'Renewal Summit 2026 - Test',
                    'CallbackUrl' => $callbackUrl,
                ]);
            $this->line("  → {$r->status()} | " . $r->body());
            if ($r->successful() && ! isset($r->json()['message'])) {
                $this->info("  ✅ This endpoint worked!");
                break;
            }
        }

        // Try with local format (no country code)
        $this->newLine();
        $this->line('<fg=yellow>STEP 3b: Retry with local phone format (no 256 prefix)...</>');
        $localMsisdn = '0' . substr($msisdn, 3); // 256708356505 → 0708356505
        $rid2 = (string) Str::uuid();
        $r2 = Http::timeout(20)->withoutVerifying()
            ->withHeaders(['Swapp-Client-ID' => $clientId, 'Authorization' => "Bearer {$token}", 'Accept' => 'application/json', 'Content-Type' => 'application/json'])
            ->post("{$baseUrl}/collection", [
                'RequestId'   => $rid2,
                'Account'     => $localMsisdn,
                'Network'     => $network,
                'Amount'      => (string) $amount,
                'Narration'   => 'Renewal Summit 2026 - Test',
                'CallbackUrl' => $callbackUrl,
            ]);
        $this->line("  Local format {$localMsisdn}: {$r2->status()} | " . $r2->body());

        // Try with an MTN number (sandbox may only support MTN)
        $this->newLine();
        $this->line('<fg=yellow>STEP 3c: Retry with a known MTN number (0772000000)...</>');
        $mtnNum = '256772000000';
        $rid3 = (string) Str::uuid();
        $r3 = Http::timeout(20)->withoutVerifying()
            ->withHeaders(['Swapp-Client-ID' => $clientId, 'Authorization' => "Bearer {$token}", 'Accept' => 'application/json', 'Content-Type' => 'application/json'])
            ->post("{$baseUrl}/collection", [
                'RequestId'   => $rid3,
                'Account'     => $mtnNum,
                'Network'     => 'MTN',
                'Amount'      => (string) $amount,
                'Narration'   => 'Renewal Summit 2026 - Test',
                'CallbackUrl' => $callbackUrl,
            ]);
        $this->line("  MTN {$mtnNum}: {$r3->status()} | " . $r3->body());

        $this->newLine();

        return 0;
    }

    private function detectNetwork(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);
        // Normalise to local format (remove country code)
        if (str_starts_with($digits, '256')) {
            $digits = '0' . substr($digits, 3);
        }

        $prefix3 = substr($digits, 0, 3);

        // MTN Uganda: 077x, 078x, 076x, 039x, 031x
        $mtn = ['077', '078', '076', '039', '031'];
        // Airtel Uganda: 070x, 075x, 074x, 020x
        $airtel = ['070', '075', '074', '020'];

        if (in_array($prefix3, $mtn))    return 'MTN';
        if (in_array($prefix3, $airtel)) return 'Airtel';   // SwApp expects "Airtel" not "AIRTEL"

        $this->warn("Cannot detect network for prefix '{$prefix3}', defaulting to MTN.");
        return 'MTN';
    }

    private function normalisePhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);
        if (str_starts_with($phone, '0')) {
            $phone = '256' . substr($phone, 1);
        } elseif (! str_starts_with($phone, '256')) {
            $phone = '256' . $phone;
        }
        return $phone;
    }
}
