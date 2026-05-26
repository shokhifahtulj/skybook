<?php

namespace App\Services\Booking;

use App\Models\Booking;
use Illuminate\Support\Facades\DB;

class BookingExpirationService
{
    /**
     * Auto cancel expired unpaid bookings.
     */
    public function cancelExpiredBookings()
    {
        $expiredBookings = Booking::whereIn('booking_status', ['draft', 'pending_payment'])
            ->where('expires_at', '<', now())
            ->get();

        foreach ($expiredBookings as $booking) {
            DB::transaction(function () use ($booking) {
                $booking->update([
                    'booking_status' => 'expired',
                    'payment_status' => 'failed'
                ]);
            });
        }
        
        return $expiredBookings->count();
    }
}
