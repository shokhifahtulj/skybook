<?php

namespace App\Services\Inventory;

use App\Models\FlightSchedule;
use App\Models\FlightScheduleSeat;

class SeatAvailabilityService
{
    /**
     * Get all currently available seats for a schedule.
     */
    public function getAvailableSeats(FlightSchedule $schedule)
    {
        return FlightScheduleSeat::where('flight_schedule_id', $schedule->id)
            ->where(function ($query) {
                $query->where('status', 'available')
                    ->orWhere(function ($q) {
                        $q->where('status', 'locked')
                          ->where('locked_until', '<', now());
                    });
            })
            ->orderBy('aircraft_seat_id')
            ->get();
    }

    /**
     * Check if a specific seat is available.
     */
    public function isSeatAvailable($scheduleId, $seatNumber)
    {
        return FlightScheduleSeat::where('flight_schedule_id', $scheduleId)
            ->where('seat_number', $seatNumber)
            ->where(function ($query) {
                $query->where('status', 'available')
                    ->orWhere(function ($q) {
                        $q->where('status', 'locked')
                          ->where('locked_until', '<', now());
                    });
            })
            ->exists();
    }

    /**
     * Count available seats.
     */
    public function countAvailableSeats($scheduleId)
    {
        return FlightScheduleSeat::where('flight_schedule_id', $scheduleId)
            ->where(function ($query) {
                $query->where('status', 'available')
                    ->orWhere(function ($q) {
                        $q->where('status', 'locked')
                          ->where('locked_until', '<', now());
                    });
            })
            ->count();
    }
}
