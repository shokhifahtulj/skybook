<?php

namespace App\Services\Operations;

use App\Models\FlightSchedule;
use App\Models\BookingSegmentPassenger;
use App\Models\BookingReassignment;
use App\Events\Operations\PassengerRebooked;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;

class PassengerReaccommodationService
{
    /**
     * Get candidates for rebooking a disrupted schedule
     */
    public function getRebookingCandidates(FlightSchedule $disruptedSchedule): Collection
    {
        // 1. Same route
        $routeId = $disruptedSchedule->flight->route_id;
        
        // 2. Departure > now + 1 hour buffer
        $minDeparture = now()->addHours(1);

        $candidates = FlightSchedule::whereHas('flight', function ($q) use ($routeId) {
                $q->where('route_id', $routeId);
            })
            ->where('status', 'scheduled')
            ->where('departure_datetime', '>', $minDeparture)
            ->where('id', '!=', $disruptedSchedule->id)
            ->where('available_seats', '>', 0)
            ->orderBy('departure_datetime', 'asc')
            ->take(5)
            ->get();

        $scoredCandidates = collect();

        foreach ($candidates as $candidate) {
            $score = 0;

            // Time proximity (up to 48 hours later)
            $hoursDiff = $candidate->departure_datetime->diffInHours($disruptedSchedule->departure_datetime);
            if ($hoursDiff < 6) $score += 30;
            else if ($hoursDiff < 12) $score += 20;
            else if ($hoursDiff < 24) $score += 10;

            // Seat availability
            if ($candidate->available_seats >= 10) $score += 40;
            else if ($candidate->available_seats > 0) $score += 20;

            // Same terminal (if known)
            if ($candidate->terminal === $disruptedSchedule->terminal) $score += 10;

            $scoredCandidates->push((object)[
                'schedule' => $candidate,
                'score' => $score
            ]);
        }

        return $scoredCandidates->sortByDesc('score')->values();
    }

    /**
     * Rebook passengers from a disrupted schedule to a new schedule
     */
    public function rebookPassengers(FlightSchedule $disruptedSchedule, FlightSchedule $newSchedule, string $reason): int
    {
        // Get all active passengers on the disrupted schedule
        $passengers = BookingSegmentPassenger::whereHas('bookingSegment', function ($q) use ($disruptedSchedule) {
                $q->where('flight_schedule_id', $disruptedSchedule->id);
            })
            // Exclude already reassigned passengers from this flight
            ->whereDoesntHave('reassignments', function ($q) use ($disruptedSchedule) {
                $q->where('from_flight_schedule_id', $disruptedSchedule->id);
            })
            ->get();

        $rebookedCount = 0;

        foreach ($passengers as $bsp) {
            if ($newSchedule->available_seats > 0) {
                // Enterprise-safe rebooking: Append to booking_reassignments, don't overwrite booking_segments
                $reassignment = BookingReassignment::create([
                    'booking_segment_passenger_id' => $bsp->id,
                    'from_flight_schedule_id' => $disruptedSchedule->id,
                    'to_flight_schedule_id' => $newSchedule->id,
                    'reason' => $reason,
                    'triggered_by_event' => 'Reaccommodation Engine'
                ]);

                // Adjust inventory on the new schedule
                $newSchedule->decrement('available_seats');

                // Adjust inventory on the old schedule
                $disruptedSchedule->increment('available_seats');

                event(new PassengerRebooked($bsp, $disruptedSchedule, $newSchedule, $reason));
                
                $rebookedCount++;
            } else {
                break; // No more seats
            }
        }

        return $rebookedCount;
    }
}
