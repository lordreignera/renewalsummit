<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Services\QrCodeService;
use App\Services\SwappPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(
        protected SwappPaymentService $swapp,
        protected QrCodeService $qrService,
    ) {}

    /**
     * Swapp POST callback – called by the payment gateway when a
     * transaction completes (success or failure).
     */
    public function callback(Request $request): JsonResponse
    {
        $payload = $request->all();

        // Treat the callback as a notification. Confirm the transaction with
        // SwApp using our existing API credentials before changing any status.
        $result = $this->swapp->handleVerifiedCallback($payload);

        if ($result && $result['status_changed'] && $result['payment']->isSuccessful()) {
            $payment = $result['payment']->load('registration');
            $reg = $payment->registration;

            if ($payment->payment_context === 'registration' && $reg && $reg->email) {
                $this->qrService->generateAndDispatch($reg);
            }

            if ($payment->payment_context === 'accommodation' && $reg) {
                $reg->update(['accommodation_payment_status' => 'paid']);
            }
        }

        return response()->json(['status' => 'received']);
    }

    /**
     * Donation POST callback.
     */
    public function donationCallback(Request $request): JsonResponse
    {
        if (! $this->verifySignature($request)) {
            Log::warning('SwApp donation callback rejected: invalid signature', [
                'ip' => $request->ip(),
            ]);

            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $payload = $request->all();
        $status  = strtolower($payload['status'] ?? '');
        $txnId   = $payload['transaction_id'] ?? null;

        if ($txnId) {
            \App\Models\Donation::where('swapp_transaction_id', $txnId)
                ->update([
                    'status'  => in_array($status, ['success', 'successful', 'completed']) ? 'success' : 'failed',
                    'paid_at' => in_array($status, ['success', 'successful', 'completed']) ? now() : null,
                    'swapp_response' => $payload,
                ]);
        }

        return response()->json(['status' => 'received']);
    }

    /**
     * Polling endpoint – front-end polls this every few seconds on the
     * pending page to know when the MM payment is confirmed.
     *
     * On localhost the SwApp callback URL is unreachable, so this method
     * actively polls SwApp's /getstatus and self-completes the payment.
     */
    public function statusLegacy(Request $request, string $reference): JsonResponse
    {
        $reg = Registration::where('reference', $reference)
            ->whereKey($request->session()->get('registration_id'))
            ->firstOrFail();

        return $this->status($reg->reference, $reg->qr_token);
    }

    public function status(string $reference, string $token): JsonResponse
    {
        $reg = Registration::where('reference', $reference)
            ->where('qr_token', $token)
            ->with('payments')
            ->firstOrFail();

        // Actively poll SwApp when payment is still pending
        if (! $reg->isPaid()) {
            $payment = $reg->payments()
                ->where('payment_context', 'registration')
                ->whereNotNull('swapp_reference')
                ->latest()
                ->first();

            if ($payment) {
                try {
                    $swappStatus = $this->swapp->checkStatus($payment->swapp_reference);

                    if ($swappStatus['status'] === 'success' && $payment->status !== 'success') {
                        $payment->update(['status' => 'success', 'paid_at' => now()]);
                        $reg->update(['status' => 'paid']);
                        $reg->refresh();

                        // Generate QR + send confirmation email
                        if ($reg->email) {
                            $this->qrService->generateAndDispatch($reg);
                        }
                    } elseif (in_array($swappStatus['status'], ['failed', 'cancelled'])) {
                        $payment->update([
                            'status'         => $swappStatus['status'],
                            'failure_reason' => $swappStatus['message'] ?? null,
                        ]);
                    }
                } catch (\Throwable $e) {
                    Log::warning('SwApp status poll failed', ['error' => $e->getMessage()]);
                }
            }
        }

        return response()->json([
            'status'    => $reg->status,
            'paid'      => $reg->isPaid(),
            'reference' => $reg->reference,
        ]);
    }

    public function accommodationStatus(string $reference, string $token): JsonResponse
    {
        $reg = Registration::where('reference', $reference)
            ->where('qr_token', $token)
            ->with('payments')
            ->firstOrFail();

        $payment = $reg->payments()
            ->where('payment_context', 'accommodation')
            ->whereNotNull('swapp_reference')
            ->latest()
            ->first();

        if ($payment && $payment->status !== 'success') {
            try {
                $swappStatus = $this->swapp->checkStatus($payment->swapp_reference);

                if ($swappStatus['status'] === 'success') {
                    $payment->update(['status' => 'success', 'paid_at' => now()]);
                    $reg->update(['accommodation_payment_status' => 'paid']);
                    $reg->refresh();
                } elseif (in_array($swappStatus['status'], ['failed', 'cancelled'], true)) {
                    $payment->update([
                        'status'         => $swappStatus['status'],
                        'failure_reason' => $swappStatus['message'] ?? null,
                    ]);
                }
            } catch (\Throwable $e) {
                Log::warning('Accommodation status poll failed', ['error' => $e->getMessage()]);
            }
        }

        return response()->json([
            'accommodation_payment_status' => $reg->accommodation_payment_status,
            'paid' => $reg->accommodation_payment_status === 'paid',
            'reference' => $reg->reference,
        ]);
    }

    /**
     * Verify Swapp signature header.
     */
    private function verifySignature(Request $request): bool
    {
        $secret    = (string) config('services.swapp.callback_secret', '');
        $signature = $request->header('X-Swapp-Signature');

        if ($secret === '' || ! is_string($signature) || $signature === '') {
            return false;
        }

        $expected  = hash_hmac('sha256', $request->getContent(), $secret);
        return hash_equals($expected, trim($signature));
    }
}
