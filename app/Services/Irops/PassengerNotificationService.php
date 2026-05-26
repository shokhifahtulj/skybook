<?php

namespace App\Services\Irops;

use App\Events\Irops\PassengerNotified;
use App\Models\FlightSchedule;
use Illuminate\Support\Facades\Log;

class PassengerNotificationService
{
    /**
     * Dispatch notifications to all passengers on a flight schedule
     */
    public function notifyFlightPassengers(FlightSchedule $schedule, string $type, string $message): void
    {
        // Get all active booking segment passengers for this flight
        $passengers = $schedule->bookingSegments()
            ->with(['bookingSegmentPassengers.passenger', 'bookingSegmentPassengers.segment.booking'])
            ->get()
            ->flatMap(function ($segment) {
                return $segment->bookingSegmentPassengers;
            })
            ->filter(function ($bsp) {
                return in_array($bsp->operational_status, ['ticketed', 'checked_in', 'boarded']);
            });

        foreach ($passengers as $bsp) {
            $passenger = $bsp->passenger;
            $flightNum = $schedule->flight->flight_number;
            $pnr = $bsp->segment?->booking?->pnr ?? 'N/A';

            // Fake/Log channel delivery
            Log::info("[IROPS_NOTIFICATION]", [
                'Passenger' => strtoupper($passenger->first_name . ' ' . $passenger->last_name),
                'Booking' => $pnr,
                'Flight' => $flightNum,
                'Type' => $type,
                'Message' => $message,
            ]);

            PassengerNotified::dispatch($bsp, $type, $message);
        }
    }
}
