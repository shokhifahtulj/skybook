<?php

namespace App\Services\Booking;

use App\Models\Booking;
use Illuminate\Support\Facades\URL;
use Exception;

class BookingLookupService
{
    /**
     * Find booking by PNR and Last Name, return signed URL for portal access.
     */
    public function generatePortalAccess(string $pnr, string $lastName): string
    {
        $booking = Booking::with('passengers')->where('pnr', strtoupper($pnr))->first();

        if (!$booking) {
            throw new Exception('Booking tidak ditemukan.');
        }

        // Match last name or first name (in case of single name)
        $match = $booking->passengers->first(function ($passenger) use ($lastName) {
            $lastNameLower = strtolower(trim($lastName));
            return strtolower($passenger->last_name) === $lastNameLower || 
                   strtolower($passenger->first_name) === $lastNameLower;
        });

        if (!$match) {
            throw new Exception('Data penumpang tidak sesuai dengan PNR ini.');
        }

        // Generate a signed route valid for 2 hours
        return URL::temporarySignedRoute(
            'manage-booking.portal', 
            now()->addHours(2), 
            ['pnr' => $booking->pnr]
        );
    }
}
