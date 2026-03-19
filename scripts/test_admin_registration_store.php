<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\RegistrationController;
use App\Models\Registration;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

session()->start();

$phone = '07' . str_pad((string) random_int(10000000, 99999999), 8, '0', STR_PAD_LEFT);

$payload = [
    'country_type' => 'local',
    'affiliation' => 'other',
    'full_name' => 'Admin Test Attendee',
    'designation' => 'senior_pastor',
    'phone' => $phone,
    'email' => 'admin.test+' . time() . '@example.com',
    'address' => 'Kampala, Uganda',
    'nationality' => 'Uganda',
    'emergency_contact_name' => 'Test Contact',
    'emergency_contact_phone' => '0700000000',
    'medical_conditions' => null,
    'allergies' => null,
    'mobility_needs' => null,
    'special_needs' => null,
    'accommodation_required' => '1',
    'accommodation_choice' => 'Speke Resort Munyonyo',
    'accommodation_fee' => 150000,
    'sms_opt_in' => '0',
    'admin_notes' => 'Created by test script',
];

$request = Request::create('/admin/registrations/create', 'POST', $payload);

$controller = app()->make(RegistrationController::class);
$response = $controller->store($request);

$created = Registration::where('phone', $phone)->latest()->first();

if (! $created) {
    fwrite(STDERR, "FAILED: no registration created.\n");
    exit(1);
}

echo "SUCCESS\n";
echo 'Reference: ' . $created->reference . PHP_EOL;
echo 'Phone: ' . $created->phone . PHP_EOL;
echo 'Status: ' . $created->status . PHP_EOL;
echo 'Currency: ' . $created->currency . PHP_EOL;
echo 'Base fee: ' . $created->base_fee . PHP_EOL;
echo 'Total amount (registration first): ' . $created->total_amount . PHP_EOL;
echo 'Accommodation required: ' . ($created->accommodation_required ? 'yes' : 'no') . PHP_EOL;
echo 'Accommodation payment status: ' . ($created->accommodation_payment_status ?? 'n/a') . PHP_EOL;
echo 'Redirect to: ' . $response->getTargetUrl() . PHP_EOL;
