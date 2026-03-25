<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$apiKey  = env('AFRICAISTALKING_API_KEY');
$username = env('AFRICAISTALKING_USERNAME');

echo "Username: {$username}\n";
echo "Key prefix: " . substr($apiKey, 0, 12) . "...\n\n";

// Africa is Talking uses 'apiKey' header, not Bearer token
$response = Illuminate\Support\Facades\Http::withHeaders([
    'Accept' => 'application/json',
    'apiKey' => $apiKey,
])->asForm()->post('https://api.africastalking.com/version1/messaging', [
    'username' => $username,
    'to'       => '+256708356505',
    'message'  => 'Test SMS - Renewal Summit 2026',
]);

echo "Status: " . $response->status() . "\n";
echo "Body: " . $response->body() . "\n";
