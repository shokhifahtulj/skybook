<?php

namespace App\Services\Ancillary;

use App\Events\AncillaryPurchased;
use App\Models\BookingPassengerAncillary;
use Illuminate\Support\Str;

class AncillaryPaymentService
{
    /**
     * Simulate payment for an ancillary service
     */
    public function processPayment(BookingPassengerAncillary $ancillary)
    {
        // For MVP, we simulate a successful payment instantly
        $paymentReference = (string) Str::uuid();

        $ancillary->update([
            'status' => 'paid',
            'payment_id' => $paymentReference
        ]);

        // Dispatch event for operational logging
        AncillaryPurchased::dispatch($ancillary);

        return $ancillary;
    }
}
