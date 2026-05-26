<?php

namespace App\Services\Payment;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Str;

class PaymentService
{
    /**
     * Create a payment record for a booking.
     */
    public function createPaymentForBooking(Booking $booking, $amount, $gateway = 'mock'): Payment
    {
        return Payment::create([
            'booking_id' => $booking->id,
            'payment_reference' => 'PAY-' . strtoupper(Str::random(10)),
            'gateway' => $gateway,
            'amount' => $amount,
            'currency' => $booking->currency,
            'status' => 'pending'
        ]);
    }
}
