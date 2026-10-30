<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\Registration;
use App\Services\AfricaIsTalkingSmsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_callback_does_not_trust_an_unconfirmed_success_status(): void
    {
        Http::fake([
            '*/token' => Http::response(['access_token' => 'test-token']),
            '*/getstatus' => Http::response(['payment' => ['status' => 'PENDING']]),
        ]);

        $registration = Registration::create([
            'full_name' => 'Test Attendee',
            'phone' => '0700000001',
            'status' => 'pending',
            'total_amount' => 50000,
        ]);

        Payment::create([
            'registration_id' => $registration->id,
            'payment_context' => 'registration',
            'swapp_reference' => 'req-security-1',
            'amount' => 50000,
            'status' => 'pending',
        ]);

        $response = $this->postJson(route('payment.callback'), [
            'RequestId' => 'req-security-1',
            'Status' => 'SUCCESSFUL',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('payments', [
            'swapp_reference' => 'req-security-1',
            'status' => 'pending',
        ]);
        $this->assertDatabaseHas('registrations', [
            'id' => $registration->id,
            'status' => 'pending',
        ]);
    }

    public function test_payment_callback_uses_verified_provider_status_even_when_payload_disagrees(): void
    {
        Http::fake([
            '*/token' => Http::response(['access_token' => 'test-token']),
            '*/getstatus' => Http::response(['payment' => ['status' => 'SUCCESSFUL']]),
        ]);

        $registration = Registration::create([
            'full_name' => 'Test Attendee',
            'phone' => '0700000002',
            'status' => 'pending',
            'total_amount' => 50000,
        ]);

        Payment::create([
            'registration_id' => $registration->id,
            'payment_context' => 'registration',
            'swapp_reference' => 'req-security-2',
            'amount' => 50000,
            'status' => 'pending',
        ]);

        $response = $this->postJson(route('payment.callback'), [
            'RequestId' => 'req-security-2',
            'Status' => 'FAILED',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('payments', [
            'swapp_reference' => 'req-security-2',
            'status' => 'success',
        ]);
        $this->assertDatabaseHas('registrations', [
            'id' => $registration->id,
            'status' => 'paid',
        ]);
    }

    public function test_payment_callback_leaves_payment_pending_when_provider_status_is_unavailable(): void
    {
        Http::fake([
            '*/token' => Http::response(['access_token' => 'test-token']),
            '*/getstatus' => Http::response(['payment' => ['status' => 'SUCCESSFUL']], 503),
        ]);

        $registration = Registration::create([
            'full_name' => 'Test Attendee',
            'phone' => '0700000003',
            'status' => 'pending',
            'total_amount' => 50000,
        ]);

        Payment::create([
            'registration_id' => $registration->id,
            'payment_context' => 'registration',
            'swapp_reference' => 'req-security-3',
            'amount' => 50000,
            'status' => 'pending',
        ]);

        $this->postJson(route('payment.callback'), [
            'RequestId' => 'req-security-3',
            'Status' => 'SUCCESSFUL',
        ])->assertOk();

        $this->assertDatabaseHas('payments', [
            'swapp_reference' => 'req-security-3',
            'status' => 'pending',
        ]);
    }

    public function test_old_reference_only_status_requires_matching_registration_session(): void
    {
        $this->get('/payment/status/RS2026-00001')->assertNotFound();
        $this->get('/qr/RS2026-00001')->assertNotFound();
    }

    public function test_old_payment_status_url_works_only_for_the_original_registration_session(): void
    {
        $registration = Registration::create([
            'full_name' => 'Test Attendee',
            'phone' => '0700000004',
            'status' => 'paid',
            'total_amount' => 50000,
        ]);

        $url = "/payment/status/{$registration->reference}";

        $this->get($url)->assertNotFound();
        $this->withSession(['registration_id' => $registration->id])
            ->get($url)
            ->assertOk()
            ->assertJsonPath('paid', true);
    }

    public function test_old_pending_url_redirects_only_for_the_original_registration_session(): void
    {
        $registration = Registration::create([
            'full_name' => 'Test Attendee',
            'phone' => '0700000005',
            'status' => 'pending',
            'total_amount' => 50000,
        ]);

        $url = "/registration/pending/{$registration->reference}";

        $this->get($url)->assertNotFound();
        $this->withSession(['registration_id' => $registration->id])
            ->get($url)
            ->assertRedirect(route('register.pending', [
                'reference' => $registration->reference,
                'token' => $registration->qr_token,
            ]));
    }

    public function test_registration_sms_includes_the_replacement_qr_link(): void
    {
        config()->set('services.africaistalking.username', 'test');
        config()->set('services.africaistalking.api_key', 'test-key');
        config()->set('services.africaistalking.base_url', 'https://api.sandbox.africastalking.com');

        Http::fake(['api.sandbox.africastalking.com/*' => Http::response(['ok' => true])]);

        $link = 'https://example.test/qr/RS2026-00001/test-token';
        app(AfricaIsTalkingSmsService::class)
            ->sendRegistrationConfirmation('0700000006', 'Test Attendee', 'RS2026-00001', $link);

        Http::assertSent(fn ($request) => str_contains((string) $request['message'], $link));
    }
}
