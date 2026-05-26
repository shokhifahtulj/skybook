<?php

namespace App\Services\Ancillary;

use App\Models\BookingPassengerAncillary;
use App\Models\BookingSegmentPassenger;

class BaggageService
{
    /**
     * Parse the weight allowance from the ancillary code (e.g. BG15 -> 15)
     */
    public function getWeightFromCode(string $code): int
    {
        if (preg_match('/BG(\d+)/', $code, $matches)) {
            return (int) $matches[1];
        }
        return 0;
    }

    /**
     * Check-in a baggage ancillary for operations
     */
    public function checkInBaggage(BookingPassengerAncillary $ancillary)
    {
        if ($ancillary->type !== 'baggage') {
            throw new \Exception("This ancillary is not a baggage service.");
        }

        if ($ancillary->status !== 'paid') {
            throw new \Exception("Baggage has not been paid for.");
        }

        $ancillary->update([
            'operational_status' => 'checked_in',
        ]);

        return $ancillary;
    }
}
