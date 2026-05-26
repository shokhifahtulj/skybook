<?php

namespace App\Services\Operations;

use App\Models\FlightSchedule;
use App\Models\FlightCrewAssignment;
use App\Events\Operations\CrewLegalityViolation;
use Illuminate\Support\Carbon;

class CrewLegalityEngineService
{
    const MAX_DUTY_HOURS = 14;

    /**
     * Evaluate legality for all crew members assigned to a delayed schedule
     */
    public function evaluateLegalityForSchedule(FlightSchedule $schedule): void
    {
        $assignments = FlightCrewAssignment::where('flight_schedule_id', $schedule->id)
            ->where('status', 'assigned')
            ->with('crewMember')
            ->get();

        foreach ($assignments as $assignment) {
            $this->evaluateCrewLegality($assignment->crewMember, $schedule->departure_datetime->toDateString());
        }
    }

    /**
     * Calculate projected duty and emit Warning if exceeded.
     */
    public function evaluateCrewLegality($crewMember, $dateString): void
    {
        $assignments = FlightCrewAssignment::where('crew_member_id', $crewMember->id)
            ->where('status', 'assigned')
            ->whereHas('schedule', function ($query) use ($dateString) {
                $query->whereDate('departure_datetime', $dateString)
                      ->where('status', '!=', 'cancelled');
            })
            ->with('schedule.flight')
            ->get();

        if ($assignments->isEmpty()) return;

        $earliestDeparture = null;
        $latestArrival = null;

        foreach ($assignments as $da) {
            $daDeparture = $da->schedule->departure_datetime->copy()->subHour();
            $daArrival = $da->schedule->arrival_datetime->copy()->addMinutes(30);
            
            if (!$earliestDeparture || $daDeparture->lessThan($earliestDeparture)) {
                $earliestDeparture = $daDeparture;
            }
            if (!$latestArrival || $daArrival->greaterThan($latestArrival)) {
                $latestArrival = $daArrival;
            }
        }

        $totalDutyHours = $earliestDeparture->diffInHours($latestArrival);

        if ($totalDutyHours > self::MAX_DUTY_HOURS) {
            $reason = "Projected duty exceeds 14h limit ({$totalDutyHours}h) due to network delays.";
            
            // For MVP, we do not auto-unassign. We only raise a critical event.
            event(new CrewLegalityViolation($crewMember, $assignments->first()->schedule, $reason, $totalDutyHours));
        }
    }
}
