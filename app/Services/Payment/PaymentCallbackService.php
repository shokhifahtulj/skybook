<?php

namespace App\Services\Payment;

use App\Events\PaymentConfirmed;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Exception;

class PaymentCallbackService
{
    protected $confirmationService;

    public function __construct(\App\Services\Booking\BookingConfirmationService $confirmationService)
    {
        $this->confirmationService = $confirmationService;
    }

    /**
     * Process payment success idempotently.
     */
    public function handleSuccess($paymentReference, array $payload = [])
    {
        \Illuminate\Support\Facades\Log::channel('payment')->info("Received payment success callback for {$paymentReference}");

        return DB::transaction(function () use ($paymentReference, $payload) {
            $payment = Payment::where('payment_reference', $paymentReference)->lockForUpdate()->first();

            if (!$payment) {
                throw new Exception("Payment not found");
            }

            // Idempotency check
            if ($payment->status === 'paid') {
                \Illuminate\Support\Facades\Log::channel('payment')->warning("Duplicate payment callback ignored for {$paymentReference}");
                return $payment;
            }

            // Update payment
            $payment->update([
                'status' => 'paid',
                'paid_at' => now(),
                'raw_payload' => $payload
            ]);

            // Trigger Booking Confirmation orchestration
            $this->confirmationService->confirmBooking($payment->booking);

            event(new PaymentConfirmed($payment));

            return $payment;
        });
    }
    
    public function handleFailed($paymentReference, array $payload = [])
    {
        return DB::transaction(function () use ($paymentReference, $payload) {
            $payment = Payment::where('payment_reference', $paymentReference)->lockForUpdate()->first();

            if (!$payment) {
                throw new Exception("Payment not found");
            }

            if ($payment->status === 'failed' || $payment->status === 'paid') {
                return $payment;
            }

            $payment->update([
                'status' => 'failed',
                'raw_payload' => $payload
            ]);

            return $payment;
        });
    }
}
