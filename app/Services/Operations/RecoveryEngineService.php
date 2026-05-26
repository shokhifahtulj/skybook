<?php

namespace App\Services\Operations;

use App\Models\FlightSchedule;
use App\Models\Aircraft;
use App\Models\CrewMember;
use App\Models\FlightCrewAssignment;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;

class RecoveryEngineService
{
    /**
     * Suggest optimal replacement aircraft for a disrupted flight.
     */
    public function suggestAircraftSwap(FlightSchedule $schedule): Collection
    {
        // Get all structurally available aircraft
        $availableAircrafts = Aircraft::where('operational_status', 'available')->get();

        $suggestions = collect();

        // Turnaround Buffer (MTT) for aircraft
        $startBuffer = $schedule->departure_datetime->copy()->subMinutes(45);
        $endBuffer = $schedule->arrival_datetime->copy()->addMinutes(45);

        foreach ($availableAircrafts as $aircraft) {
            // Check overlap
            $hasOverlap = FlightSchedule::where('aircraft_id', $aircraft->id)
                ->where('status', '!=', 'cancelled')
                ->where('id', '!=', $schedule->id)
                ->where(function ($query) use ($startBuffer, $endBuffer) {
                    $query->whereBetween('departure_datetime', [$startBuffer, $endBuffer])
                          ->orWhereBetween('arrival_datetime', [$startBuffer, $endBuffer])
                          ->orWhere(function($q) use ($startBuffer, $endBuffer) {
                              $q->where('departure_datetime', '<=', $startBuffer)
                                ->where('arrival_datetime', '>=', $endBuffer);
                          });
                })->exists();

            if (!$hasOverlap) {
                // Determine fitness
                $suggestions->push((object)[
                    'aircraft' => $aircraft,
                    'reasons' => [
                        'No schedule overlap with 45m MTT',
                        "Capacity: {$aircraft->capacity} seats"
                    ],
                    'capacity' => $aircraft->capacity
                ]);
            }
        }

        // Sort by capacity descending (safest bet for re-accommodation without knowing PNR counts)
        return $suggestions->sortByDesc('capacity')->values();
    }

    /**
     * Suggest reserve crew to cover an assignment.
     */
    public function suggestReserveCrew(FlightSchedule $schedule, $roleId = null): Collection
    {
        $query = CrewMember::where('operational_status', 'available')->with('role');
        if ($roleId) {
            $query->where('crew_role_id', $roleId);
        }
        
        $availableCrews = $query->get();
        $suggestions = collect();

        // Turnaround Buffer for crew is 30 mins
        $startBuffer = $schedule->departure_datetime->copy()->subMinutes(30);
        $endBuffer = $schedule->arrival_datetime->copy()->addMinutes(30);
        $flightDate = $schedule->departure_datetime->toDateString();

        foreach ($availableCrews as $crew) {
            // 1. Check Overlap
            $hasOverlap = FlightSchedule::whereHas('crewAssignments', function ($q) use ($crew) {
                    $q->where('crew_member_id', $crew->id)->where('status', 'assigned');
                })
                ->where('status', '!=', 'cancelled')
                ->where('id', '!=', $schedule->id)
                ->where(function ($query) use ($startBuffer, $endBuffer) {
                    $query->whereBetween('departure_datetime', [$startBuffer, $endBuffer])
                          ->orWhereBetween('arrival_datetime', [$startBuffer, $endBuffer])
                          ->orWhere(function($q) use ($startBuffer, $endBuffer) {
                              $q->where('departure_datetime', '<=', $startBuffer)
                                ->where('arrival_datetime', '>=', $endBuffer);
                          });
                })->exists();

            if ($hasOverlap) {
                continue;
            }

            // 2. Check 14-Hour Legality Projection if assigned
            $dailyAssignments = FlightCrewAssignment::where('crew_member_id', $crew->id)
                ->where('status', 'assigned')
                ->whereHas('schedule', function ($q) use ($flightDate) {
                    $q->whereDate('departure_datetime', $flightDate);
                })
                ->with('schedule')
                ->get();

            $earliestDeparture = $schedule->departure_datetime->copy()->subHour();
            $latestArrival = $schedule->arrival_datetime->copy()->addMinutes(30);

            foreach ($dailyAssignments as $da) {
                $daDeparture = $da->schedule->departure_datetime->copy()->subHour();
                $daArrival = $da->schedule->arrival_datetime->copy()->addMinutes(30);
                
                if ($daDeparture->lessThan($earliestDeparture)) {
                    $earliestDeparture = $daDeparture;
                }
                if ($daArrival->greaterThan($latestArrival)) {
                    $latestArrival = $daArrival;
                }
            }

            $totalDutyHours = $earliestDeparture->diffInHours($latestArrival);

            if ($totalDutyHours <= 14) {
                $suggestions->push((object)[
                    'crew' => $crew,
                    'reasons' => [
                        'No schedule overlap',
                        "Projected duty: {$totalDutyHours}h (Legal)"
                    ],
                    'projected_duty' => $totalDutyHours
                ]);
            }
        }

        // Sort by lowest projected duty (freshest crew first)
        return $suggestions->sortBy('projected_duty')->values();
    }

    /**
     * Suggest an alternative gate when there is a conflict.
     */
    public function suggestGateSwap(FlightSchedule $schedule, string $type = 'departure'): Collection
    {
        $airportId = $type === 'departure' ? $schedule->flight->route->origin_id : $schedule->flight->route->destination_id;
        
        $availableGates = \App\Models\AirportGate::where('airport_id', $airportId)
            ->where('status', 'active')
            ->get();

        $gateService = app(\App\Services\Operations\GateManagementService::class);
        $suggestions = collect();

        foreach ($availableGates as $gate) {
            $conflict = $gateService->detectOverlap($gate, $schedule, $type);
            
            if (!$conflict) {
                $suggestions->push((object)[
                    'gate' => $gate,
                    'reasons' => [
                        "Available for {$type}",
                        "Terminal {$gate->terminal}"
                    ]
                ]);
            }
        }

        return $suggestions;
    }
}
