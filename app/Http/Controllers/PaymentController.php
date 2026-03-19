<?php

namespace App\Http\Controllers;

use App\Models\Payment;
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
        Log::info('Swapp Callback', $request->all());

        $payload = $request->all();

        // Signature verification (uncomment when you have SWAPP_SECRET_KEY)
        // if (! $this->verifySignature($request)) {
        //     return response()->json(['error' => 'Invalid signature'], 401);
        // }

        $handled = $this->swapp->handleCallback($payload);

        if ($handled) {
            // If payment succeeded, generate QR and send confirmation email
            $transId = $payload['transaction_id'] ?? $payload['reference'] ?? null;
            $payment = Payment::where('swapp_transaction_id', $transId)
                ->orWhere('swapp_reference', $transId)
                ->with('registration')
                ->first();

            if ($payment && $payment->isSuccessful()) {
                $reg = $payment->registration;
                if ($payment->payment_context === 'registration' && $reg && $reg->email) {
                    $this->qrService->generateAndDispatch($reg);
                }

                if ($payment->payment_context === 'accommodation' && $reg) {
                    $reg->update(['accommodation_payment_status' => 'paid']);
                }
            }
        }

        return response()->json(['status' => 'received']);
    }

    /**
     * Donation POST callback.
     */
    public function donationCallback(Request $request): JsonResponse
    {
        Log::info('Swapp Donation Callback', $request->all());

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
    public function status(string $reference): JsonResponse
    {
        $reg = Registration::where('reference', $reference)
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

                    if ($swappStatus['status'] === 'success') {
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
        $secret    = config('services.swapp.secret_key', env('SWAPP_SECRET_KEY'));
        $signature = $request->header('X-Swapp-Signature');
        $expected  = hash_hmac('sha256', $request->getContent(), $secret);
        return hash_equals($expected, $signature ?? '');
    }
}
