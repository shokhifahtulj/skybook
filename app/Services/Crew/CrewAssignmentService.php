<?php

namespace App\Services\Crew;

use App\Models\FlightSchedule;
use App\Models\CrewMember;
use App\Models\FlightCrewAssignment;
use App\Events\Operations\CrewAssigned;
use App\Events\Operations\CrewUnassigned;
use App\Events\Operations\CrewDutyWarning;
use Illuminate\Support\Facades\DB;
use Exception;

class CrewAssignmentService
{
    /**
     * Assign a crew member to a flight schedule.
     */
    public function assignCrew(FlightSchedule $schedule, CrewMember $crewMember, $userId = null): FlightCrewAssignment
    {
        return DB::transaction(function () use ($schedule, $crewMember, $userId) {
            // 1. Validation: Prevent double booking for the same schedule
            $existingAssignment = FlightCrewAssignment::where('flight_schedule_id', $schedule->id)
                ->where('crew_member_id', $crewMember->id)
                ->where('status', 'assigned')
                ->first();

            if ($existingAssignment) {
                throw new Exception("Crew member {$crewMember->crew_code} is already assigned to this flight.");
            }

            // 2. Validation: Crew overlapping schedule (Overlap check)
            $conflict = $this->detectOverlap($schedule, $crewMember);
            if ($conflict) {
                throw new Exception("Crew member {$crewMember->crew_code} has an overlapping assignment on flight {$conflict->flight->flight_number}.");
            }

            // 3. Validation: Legality Duty Check (Max 14 hours per calendar day)
            $flightDate = $schedule->departure_datetime->toDateString();
            $dailyAssignments = FlightCrewAssignment::where('crew_member_id', $crewMember->id)
                ->where('status', 'assigned')
                ->whereHas('schedule', function ($query) use ($flightDate) {
                    $query->whereDate('departure_datetime', $flightDate);
                })
                ->with('schedule')
                ->get();

            // Calculate duty hours
            $earliestDeparture = $schedule->departure_datetime->copy()->subHour(); // Duty starts 1 hour before departure
            $latestArrival = $schedule->arrival_datetime->copy()->addMinutes(30); // Duty ends 30 min after arrival

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

            if ($totalDutyHours > 14) {
                $reason = "Exceeded maximum daily duty limit of 14 hours on {$flightDate} (Projected: {$totalDutyHours}h)";
                event(new CrewDutyWarning($crewMember, $schedule, $reason, $userId));
                throw new Exception("Crew duty violation: {$reason}.");
            }

            // Create assignment
            $assignment = FlightCrewAssignment::create([
                'flight_schedule_id' => $schedule->id,
                'crew_member_id' => $crewMember->id,
                'crew_role_id' => $crewMember->crew_role_id,
                'assigned_at' => now(),
                'assigned_by' => $userId,
                'status' => 'assigned'
            ]);

            // Rebuild crew rotation chain
            $this->rebuildRotationChain($crewMember, $flightDate);

            // Update crew status if they were available
            if ($crewMember->operational_status === 'available') {
                $crewMember->update(['operational_status' => 'assigned']);
            }

            event(new CrewAssigned($assignment, $userId));

            return $assignment;
        });
    }

    /**
     * Rebuild the doubly-linked list for crew rotation chain
     */
    public function rebuildRotationChain(CrewMember $crewMember, string $dateString): void
    {
        $assignments = FlightCrewAssignment::where('crew_member_id', $crewMember->id)
            ->where('status', 'assigned')
            ->whereHas('schedule', function ($query) use ($dateString) {
                $query->whereDate('departure_datetime', $dateString)
                      ->where('status', '!=', 'cancelled');
            })
            ->join('flight_schedules', 'flight_crew_assignments.flight_schedule_id', '=', 'flight_schedules.id')
            ->orderBy('flight_schedules.departure_datetime', 'asc')
            ->select('flight_crew_assignments.*')
            ->get();

        foreach ($assignments as $index => $assignment) {
            $prev = $index > 0 ? $assignments[$index - 1]->id : null;
            $next = $index < count($assignments) - 1 ? $assignments[$index + 1]->id : null;

            if ($assignment->previous_assignment_id !== $prev || $assignment->next_assignment_id !== $next) {
                $assignment->update([
                    'previous_assignment_id' => $prev,
                    'next_assignment_id' => $next
                ]);
            }
        }
    }

    /**
     * Unassign a crew member from a flight schedule.
     */
    public function unassignCrew(FlightCrewAssignment $assignment, $userId = null): void
    {
        DB::transaction(function () use ($assignment, $userId) {
            $assignment->update(['status' => 'removed']);

            $flightDate = $assignment->schedule->departure_datetime->toDateString();
            $this->rebuildRotationChain($assignment->crewMember, $flightDate);

            event(new CrewUnassigned($assignment, $userId));

            // Check if this was their last assignment for the day/currently active?
            $activeAssignments = FlightCrewAssignment::where('crew_member_id', $assignment->crew_member_id)
                ->where('status', 'assigned')
                ->exists();

            if (!$activeAssignments) {
                $assignment->crewMember->update(['operational_status' => 'available']);
            }
        });
    }

    private function detectOverlap(FlightSchedule $schedule, CrewMember $crewMember): ?FlightSchedule
    {
        // Turnaround buffer is 30 minutes for Crew.
        $startBuffer = $schedule->departure_datetime->copy()->subMinutes(30);
        $endBuffer = $schedule->arrival_datetime->copy()->addMinutes(30);

        $conflict = FlightSchedule::whereHas('crewAssignments', function ($query) use ($crewMember) {
                $query->where('crew_member_id', $crewMember->id)->where('status', 'assigned');
            })
            ->where('id', '!=', $schedule->id)
            ->where('status', '!=', 'cancelled')
            ->where(function($query) use ($startBuffer, $endBuffer) {
                $query->whereBetween('departure_datetime', [$startBuffer, $endBuffer])
                      ->orWhereBetween('arrival_datetime', [$startBuffer, $endBuffer])
                      ->orWhere(function($q) use ($startBuffer, $endBuffer) {
                          $q->where('departure_datetime', '<=', $startBuffer)
                            ->where('arrival_datetime', '>=', $endBuffer);
                      });
            })->first();

        return $conflict;
    }
}
