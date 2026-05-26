<?php

namespace App\Services\Fleet;

use App\Models\FlightSchedule;
use App\Models\Aircraft;
use App\Events\Operations\AircraftStatusChanged;
use App\Events\Operations\RotationConflictDetected;
use Illuminate\Support\Facades\DB;
use Exception;

class FleetAssignmentService
{
    /**
     * Assign aircraft to a flight schedule with conflict checking.
     */
    public function assignAircraft(FlightSchedule $schedule, Aircraft $aircraft, $user = null): void
    {
        DB::transaction(function () use ($schedule, $aircraft, $user) {
            // Check for conflict (Turnaround aware)
            $conflict = $this->detectConflict($schedule, $aircraft);

            if ($conflict) {
                event(new RotationConflictDetected($aircraft, $conflict, $schedule, $user));
                throw new Exception("Aircraft {$aircraft->model} is already assigned to a conflicting schedule ({$conflict->flight->flight_number}). Minimum turnaround is 45 minutes.");
            }

            // Unlink from old chain if it was assigned to a different aircraft previously
            // But we don't have this requirement in MVP, assume we are assigning first time or overriding.
            $schedule->aircraft_id = $aircraft->id;
            $schedule->save();
            
            // Rebuild rotation chain for this aircraft today
            $this->rebuildRotationChain($aircraft, $schedule->departure_datetime->toDateString());

            // Auto update status to assigned
            $this->changeStatus($aircraft, 'assigned', 'Assigned to flight ' . $schedule->flight->flight_number, $user);
        });
    }

    /**
     * Rebuild the doubly-linked list for rotation chain
     */
    public function rebuildRotationChain(Aircraft $aircraft, string $dateString): void
    {
        $schedules = FlightSchedule::where('aircraft_id', $aircraft->id)
            ->whereDate('departure_datetime', $dateString)
            ->where('status', '!=', 'cancelled')
            ->orderBy('departure_datetime', 'asc')
            ->get();

        foreach ($schedules as $index => $schedule) {
            $prev = $index > 0 ? $schedules[$index - 1]->id : null;
            $next = $index < count($schedules) - 1 ? $schedules[$index + 1]->id : null;

            if ($schedule->previous_schedule_id !== $prev || $schedule->next_schedule_id !== $next) {
                $schedule->update([
                    'previous_schedule_id' => $prev,
                    'next_schedule_id' => $next
                ]);
            }
        }
    }

    /**
     * Change an aircraft's operational status
     */
    public function changeStatus(Aircraft $aircraft, string $newStatus, string $reason = null, $user = null): void
    {
        $oldStatus = $aircraft->operational_status;
        if ($oldStatus === $newStatus) return;

        $aircraft->operational_status = $newStatus;
        $aircraft->save();

        event(new AircraftStatusChanged($aircraft, $oldStatus, $newStatus, $user, $reason));
    }

    private function detectConflict(FlightSchedule $schedule, Aircraft $aircraft): ?FlightSchedule
    {
        // Turnaround buffer is 45 minutes.
        // We check if this schedule overlaps with any existing schedule + 45 min buffer.
        
        $startBuffer = $schedule->departure_datetime->copy()->subMinutes(45);
        $endBuffer = $schedule->arrival_datetime->copy()->addMinutes(45);

        $conflict = FlightSchedule::where('aircraft_id', $aircraft->id)
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
