<?php
/**
 * Test Script: Send confirmation email + SMS for an existing paid registration
 *
 * Usage:
 *   php scripts/test_sms_email.php
 *
 * This bootstraps the Laravel app so all services, .env config, and models
 * are available — same as running from a real request.
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Registration;
use App\Mail\RegistrationConfirmationMail;
use App\Services\AfricaIsTalkingSmsService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

// ── Load Norah's registration ──────────────────────────────────────────────
$registration = Registration::where('reference', 'RS2026-00001')->first();

if (! $registration) {
    echo "❌ Registration not found.\n";
    exit(1);
}

echo "✅ Found: {$registration->full_name} | {$registration->email} | {$registration->phone}\n";
echo "   Ref: {$registration->reference} | Status: {$registration->status}\n\n";

// ── 1. Send confirmation email ─────────────────────────────────────────────
// Temporarily override QR disk to 'public' so the attachment check works locally
// (the file is on R2 in production; locally we skip the attachment if not found)
echo "📧 Sending email to {$registration->email} ...\n";

try {
    // Temporarily set qr_disk to local so attachment skip works gracefully
    config(['filesystems.qr_disk' => 'public']);
    Mail::to($registration->email)->send(new RegistrationConfirmationMail($registration));
    echo "   ✅ Email sent successfully (QR attachment skipped if not on local disk).\n\n";
} catch (\Exception $e) {
    echo "   ❌ Email failed: " . $e->getMessage() . "\n\n";
}

// ── 2. Send SMS via Africa is Talking sandbox ──────────────────────────────
// NOTE: Sandbox only delivers to numbers added in your AT sandbox simulator.
// Add Norah's number (+256708356505) in the AT dashboard simulator first.

$phone = $registration->phone;

// Normalise to E.164 (+256...) — strip leading 0 and prepend +256
if (str_starts_with($phone, '0')) {
    $phone = '+256' . substr($phone, 1);
} elseif (! str_starts_with($phone, '+')) {
    $phone = '+256' . $phone;
}

$qrUrl = route('qr.show', ['reference' => $registration->reference]);

echo "📱 Sending SMS to {$phone} ...\n";
echo "   QR URL: {$qrUrl}\n";

$sms    = new AfricaIsTalkingSmsService();
$result = $sms->sendRegistrationConfirmation(
    $phone,
    $registration->full_name,
    $registration->reference,
    $qrUrl,
);

if ($result['success']) {
    echo "   ✅ SMS sent successfully.\n";
    print_r($result['data']);
} else {
    echo "   ❌ SMS failed: " . $result['error'] . "\n";
    if (isset($result['raw'])) {
        echo "   Raw response: " . json_encode($result['raw'], JSON_PRETTY_PRINT) . "\n";
    }
}

echo "\n✅ Test complete.\n";
