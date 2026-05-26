<?php

namespace App\Services\Operations;

use App\Models\Aircraft;
use App\Models\AircraftMaintenanceEvent;
use App\Models\FlightSchedule;
use App\Events\Operations\AircraftGrounded;
use App\Events\Operations\AircraftReleasedFromMaintenance;
use App\Events\Operations\AircraftGroundedConflict;
use App\Services\Fleet\FleetAssignmentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class EngineeringControlService
{
    protected $fleetAssignmentService;

    public function __construct(FleetAssignmentService $fleetAssignmentService)
    {
        $this->fleetAssignmentService = $fleetAssignmentService;
    }

    /**
     * Schedule and potentially start a maintenance event.
     */
    public function groundAircraft(Aircraft $aircraft, array $data, $userId = null): AircraftMaintenanceEvent
    {
        return DB::transaction(function () use ($aircraft, $data, $userId) {
            $maintenance = AircraftMaintenanceEvent::create([
                'aircraft_id' => $aircraft->id,
                'maintenance_type' => $data['maintenance_type'],
                'status' => $data['status'] ?? 'planned',
                'severity' => $data['severity'] ?? 'major',
                'start_at' => $data['start_at'],
                'end_at' => $data['end_at'],
                'notes' => $data['notes'] ?? null,
                'created_by' => $userId,
            ]);

            // If it's starting immediately or is an AOG
            if ($maintenance->status === 'in_progress' || $maintenance->maintenance_type === 'aog') {
                $this->executeGrounding($aircraft, $maintenance);
            }

            return $maintenance;
        });
    }

    /**
     * Execute the grounding of an aircraft.
     */
    protected function executeGrounding(Aircraft $aircraft, AircraftMaintenanceEvent $maintenance): void
    {
        // 1. Update aircraft status
        $aircraft->update(['operational_status' => 'grounded']);

        // 2. Find overlapping schedules
        $start = Carbon::parse($maintenance->start_at);
        $end = Carbon::parse($maintenance->end_at);

        $conflictingSchedules = FlightSchedule::where('aircraft_id', $aircraft->id)
            ->where('status', '!=', 'cancelled')
            ->where(function($query) use ($start, $end) {
                $query->whereBetween('departure_datetime', [$start, $end])
                      ->orWhereBetween('arrival_datetime', [$start, $end])
                      ->orWhere(function($q) use ($start, $end) {
                          $q->where('departure_datetime', '<=', $start)
                            ->where('arrival_datetime', '>=', $end);
                      });
            })
            ->get();

        if ($conflictingSchedules->isNotEmpty()) {
            // 3. Unassign the aircraft from these schedules
            foreach ($conflictingSchedules as $schedule) {
                $schedule->update([
                    'aircraft_id' => null,
                    'previous_schedule_id' => null,
                    'next_schedule_id' => null
                ]);
            }
            
            // 4. Rebuild the rotation chain for the aircraft's remaining schedules
            if ($conflictingSchedules->isNotEmpty()) {
                // Group by date to rebuild chains for affected days
                $dates = $conflictingSchedules->map(fn($s) => $s->departure_datetime->toDateString())->unique();
                foreach ($dates as $date) {
                    $this->fleetAssignmentService->rebuildRotationChain($aircraft, $date);
                }
            }

            // 5. Fire the Conflict event
            event(new AircraftGroundedConflict($aircraft, $maintenance, $conflictingSchedules));
        }

        // 6. Fire the Grounded event
        event(new AircraftGrounded($aircraft, $maintenance));
    }

    /**
     * Release an aircraft from maintenance.
     */
    public function releaseAircraft(AircraftMaintenanceEvent $maintenance, $userId = null, $resolution = null): void
    {
        DB::transaction(function () use ($maintenance, $userId, $resolution) {
            $maintenance->update([
                'status' => 'completed',
                'dispatch_released_at' => now(),
                'dispatch_released_by' => $userId,
                'notes' => $resolution ? $maintenance->notes . "\n\nResolution: " . $resolution : $maintenance->notes,
            ]);

            $aircraft = $maintenance->aircraft;
            
            // Revert aircraft status to available if it was grounded
            if ($aircraft->operational_status === 'grounded' || $aircraft->operational_status === 'maintenance') {
                $aircraft->update(['operational_status' => 'available']);
            }

            event(new AircraftReleasedFromMaintenance($aircraft, $maintenance));
        });
    }
}
