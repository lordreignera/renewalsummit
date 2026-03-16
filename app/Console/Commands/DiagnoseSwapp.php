<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class DiagnoseSwapp extends Command
{
    protected $signature   = 'swapp:diagnose';
    protected $description = 'Diagnose SwApp payment configuration and connectivity';

    public function handle(): int
    {
        $this->info('');
        $this->info('══════════════════════════════════════════');
        $this->info('  SwApp + Mail Diagnostic');
        $this->info('══════════════════════════════════════════');

        $ok = true;

        // 1. Env vars
        $this->info('');
        $this->info('── ENV VARS ─────────────────────────────');
        $vars = [
            'SWAPP_BASE_URL'     => env('SWAPP_BASE_URL'),
            'SWAPP_CLIENT_ID'    => env('SWAPP_CLIENT_ID'),
            'SWAPP_API_KEY'      => env('SWAPP_API_KEY') ? substr(env('SWAPP_API_KEY'), 0, 8) . '...' : null,
            'SWAPP_API_SECRET'   => env('SWAPP_API_SECRET') ? '(set)' : null,
            'SWAPP_CALLBACK_URL' => env('SWAPP_CALLBACK_URL'),
            'APP_URL'            => env('APP_URL'),
            'APP_ENV'            => env('APP_ENV'),
            'SUMMIT_FEE_LOCAL'   => env('SUMMIT_FEE_LOCAL'),
            'MAIL_MAILER'        => env('MAIL_MAILER'),
            'MAIL_HOST'          => env('MAIL_HOST'),
            'MAIL_USERNAME'      => env('MAIL_USERNAME'),
            'MAIL_FROM_ADDRESS'  => env('MAIL_FROM_ADDRESS'),
        ];

        foreach ($vars as $key => $val) {
            if (empty($val)) {
                $this->error("  ✗ {$key} = (MISSING)");
                $ok = false;
            } else {
                $this->line("  ✓ {$key} = {$val}");
            }
        }

        // 2. DB connectivity
        $this->info('');
        $this->info('── DATABASE ─────────────────────────────');
        try {
            $tables = DB::select("SHOW TABLES");
            $this->line('  ✓ DB connected. Tables: ' . count($tables));

            // Check payments table has failure_reason as TEXT
            $cols = DB::select("SHOW COLUMNS FROM payments LIKE 'failure_reason'");
            if (count($cols) > 0) {
                $type = $cols[0]->Type ?? 'unknown';
                $this->line("  ✓ payments.failure_reason type = {$type}");
            } else {
                $this->error('  ✗ payments.failure_reason column MISSING — run: php artisan migrate --force');
                $ok = false;
            }
        } catch (\Throwable $e) {
            $this->error('  ✗ DB error: ' . $e->getMessage());
            $ok = false;
        }

        // 3. SwApp token
        $this->info('');
        $this->info('── SWAPP TOKEN ──────────────────────────');
        $baseUrl   = rtrim(config('services.swapp.base_url', env('SWAPP_BASE_URL', '')), '/');
        $clientId  = config('services.swapp.client_id',   env('SWAPP_CLIENT_ID', ''));
        $apiKey    = config('services.swapp.api_key',     env('SWAPP_API_KEY', ''));
        $apiSecret = config('services.swapp.api_secret',  env('SWAPP_API_SECRET', ''));

        if (empty($baseUrl) || empty($clientId) || empty($apiKey) || empty($apiSecret)) {
            $this->error('  ✗ SwApp credentials missing — set SWAPP_* env vars in Laravel Cloud dashboard');
            $ok = false;
        } else {
            try {
                $response = Http::timeout(20)
                    ->withHeaders([
                        'Swapp-Client-ID' => $clientId,
                        'Authorization'   => 'Basic ' . base64_encode("{$apiKey}:{$apiSecret}"),
                        'Content-Type'    => 'application/x-www-form-urlencoded',
                    ])
                    ->asForm()
                    ->post("{$baseUrl}/token", ['grant_type' => 'client_credentials']);

                if ($response->successful()) {
                    $data  = $response->json();
                    $token = $data['access_token'] ?? null;
                    $this->line($token
                        ? '  ✓ Token obtained: ' . substr($token, 0, 20) . '...'
                        : '  ✗ Response OK but access_token missing: ' . $response->body());

                    // 4. Test balance check
                    if ($token) {
                        $this->info('');
                        $this->info('── SWAPP BALANCE ────────────────────────');
                        $bal = Http::timeout(20)
                            ->withHeaders([
                                'Authorization'   => 'Bearer ' . $token,
                                'Swapp-Client-ID' => $clientId,
                                'Accept'          => 'application/json',
                            ])
                            ->post("{$baseUrl}/balance");
                        $this->line('  Status: ' . $bal->status());
                        $this->line('  Body: ' . $bal->body());
                    }
                } else {
                    $this->error('  ✗ Token request failed (' . $response->status() . '): ' . $response->body());
                    $ok = false;
                }
            } catch (\Throwable $e) {
                $this->error('  ✗ Token request exception: ' . $e->getMessage());
                $ok = false;
            }
        }

        // 5. Mail config
        $this->info('');
        $this->info('── MAIL ─────────────────────────────────');
        $this->line('  Mailer : ' . config('mail.default'));
        $this->line('  Host   : ' . config('mail.mailers.smtp.host', '(n/a)'));
        $this->line('  Port   : ' . config('mail.mailers.smtp.port', '(n/a)'));
        $this->line('  From   : ' . config('mail.from.address'));

        // 6. Summary
        $this->info('');
        if ($ok) {
            $this->info('══ ALL CHECKS PASSED ═════════════════════');
        } else {
            $this->error('══ SOME CHECKS FAILED — see above ════════');
        }
        $this->info('');

        return $ok ? self::SUCCESS : self::FAILURE;
    }
}
