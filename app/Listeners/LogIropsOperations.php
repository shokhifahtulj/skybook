<?php

namespace App\Listeners;

use App\Events\Irops\BoardingPassRegenerated;
use App\Events\Irops\BoardingPassSuperseded;
use App\Events\Irops\FlightCancelled;
use App\Events\Irops\FlightDelayed;
use App\Events\Irops\GateChanged;
use App\Events\Irops\PassengerNotified;
use App\Services\Operations\OperationalLogService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\Dispatcher;
use Illuminate\Queue\InteractsWithQueue;

class LogIropsOperations
{
    protected $logService;

    public function __construct(OperationalLogService $logService)
    {
        $this->logService = $logService;
    }

    public function subscribe(Dispatcher $events): void
    {
        $events->listen(FlightDelayed::class, [LogIropsOperations::class, 'handleFlightDelayed']);
        $events->listen(GateChanged::class, [LogIropsOperations::class, 'handleGateChanged']);
        $events->listen(FlightCancelled::class, [LogIropsOperations::class, 'handleFlightCancelled']);
        $events->listen(BoardingPassSuperseded::class, [LogIropsOperations::class, 'handleBoardingPassSuperseded']);
        $events->listen(BoardingPassRegenerated::class, [LogIropsOperations::class, 'handleBoardingPassRegenerated']);
        $events->listen(PassengerNotified::class, [LogIropsOperations::class, 'handlePassengerNotified']);
        
        // Fleet & Crew Events
        $events->listen(\App\Events\Operations\CrewAssigned::class, [LogIropsOperations::class, 'handleCrewAssigned']);
        $events->listen(\App\Events\Operations\CrewUnassigned::class, [LogIropsOperations::class, 'handleCrewUnassigned']);
        $events->listen(\App\Events\Operations\AircraftStatusChanged::class, [LogIropsOperations::class, 'handleAircraftStatusChanged']);
        $events->listen(\App\Events\Operations\RotationConflictDetected::class, [LogIropsOperations::class, 'handleRotationConflictDetected']);
        $events->listen(\App\Events\Operations\CrewDutyWarning::class, [LogIropsOperations::class, 'handleCrewDutyWarning']);
        $events->listen(\App\Events\Operations\RotationDelayPropagated::class, [LogIropsOperations::class, 'handleRotationDelayPropagated']);
        $events->listen(\App\Events\Operations\CrewLegalityViolation::class, [LogIropsOperations::class, 'handleCrewLegalityViolation']);
        
        $events->listen(\App\Events\Operations\AircraftGrounded::class, [LogIropsOperations::class, 'handleAircraftGrounded']);
        $events->listen(\App\Events\Operations\AircraftReleasedFromMaintenance::class, [LogIropsOperations::class, 'handleAircraftReleasedFromMaintenance']);
        $events->listen(\App\Events\Operations\AircraftGroundedConflict::class, [LogIropsOperations::class, 'handleAircraftGroundedConflict']);
        
        $events->listen(\App\Events\Operations\GateConflictDetected::class, [LogIropsOperations::class, 'handleGateConflictDetected']);
    }

    public function handleFlightDelayed(FlightDelayed $event): void
    {
        $this->logService->log('flight_delayed', $event->schedule->id, [
            'level' => 'warning',
            'description' => "Flight delayed by {$event->delayMinutes} minutes. New departure: {$event->newDepartureTime}",
            'payload' => [
                'old_departure' => $event->oldDepartureTime->toIso8601String(),
                'new_departure' => $event->newDepartureTime->toIso8601String(),
                'delay_minutes' => $event->delayMinutes,
            ]
        ]);
    }

    public function handleGateChanged(GateChanged $event): void
    {
        $this->logService->log('gate_changed', $event->schedule->id, [
            'level' => 'warning',
            'description' => "Gate changed from {$event->oldGate} to {$event->newGate}. Reason: {$event->reason}",
            'payload' => [
                'old_gate' => $event->oldGate,
                'new_gate' => $event->newGate,
                'reason' => $event->reason,
            ]
        ]);
    }

    public function handleFlightCancelled(FlightCancelled $event): void
    {
        $this->logService->log('flight_cancelled', $event->schedule->id, [
            'level' => 'critical',
            'description' => "Flight cancelled. Reason: {$event->reason}",
            'payload' => [
                'reason' => $event->reason,
            ]
        ]);
    }

    public function handleBoardingPassSuperseded(BoardingPassSuperseded $event): void
    {
        $bsp = $event->oldPass->bookingSegmentPassenger;
        $this->logService->log('boarding_pass_superseded', $bsp->bookingSegment->flight_schedule_id, [
            'entity_type' => 'boarding_pass',
            'entity_id' => $event->oldPass->id,
            'passenger_id' => $bsp->passenger_id,
            'booking_id' => $bsp->booking_id,
            'level' => 'info',
            'description' => "Boarding pass {$event->oldPass->boarding_pass_number} superseded. Reason: {$event->reason}",
        ]);
    }

    public function handleBoardingPassRegenerated(BoardingPassRegenerated $event): void
    {
        $bsp = $event->newPass->bookingSegmentPassenger;
        $this->logService->log('boarding_pass_regenerated', $bsp->bookingSegment->flight_schedule_id, [
            'entity_type' => 'boarding_pass',
            'entity_id' => $event->newPass->id,
            'passenger_id' => $bsp->passenger_id,
            'booking_id' => $bsp->booking_id,
            'level' => 'info',
            'description' => "New boarding pass {$event->newPass->boarding_pass_number} generated to replace {$event->oldPass->boarding_pass_number}.",
        ]);
    }

    public function handlePassengerNotified(PassengerNotified $event): void
    {
        $this->logService->log('passenger_notified', $event->bsp->bookingSegment->flight_schedule_id, [
            'passenger_id' => $event->bsp->passenger_id,
            'booking_id' => $event->bsp->booking_id,
            'level' => 'info',
            'description' => "Passenger notified regarding {$event->type}.",
            'payload' => [
                'type' => $event->type,
                'message' => $event->message,
            ]
        ]);
    }

    public function handleCrewAssigned(\App\Events\Operations\CrewAssigned $event): void
    {
        $this->logService->log('crew_assigned', $event->assignment->flight_schedule_id, [
            'level' => 'info',
            'description' => "Crew {$event->assignment->crewMember->crew_code} assigned to flight.",
            'payload' => [
                'crew_member_id' => $event->assignment->crew_member_id,
                'role' => $event->assignment->role->code ?? 'N/A',
            ]
        ]);
    }

    public function handleCrewUnassigned(\App\Events\Operations\CrewUnassigned $event): void
    {
        $this->logService->log('crew_unassigned', $event->assignment->flight_schedule_id, [
            'level' => 'info',
            'description' => "Crew {$event->assignment->crewMember->crew_code} unassigned from flight.",
            'payload' => [
                'crew_member_id' => $event->assignment->crew_member_id,
                'role' => $event->assignment->role->code ?? 'N/A',
            ]
        ]);
    }

    public function handleAircraftStatusChanged(\App\Events\Operations\AircraftStatusChanged $event): void
    {
        // For aircraft status changes, the target entity is the aircraft, but we use the operational logs 
        // to record it. If no specific schedule is tied, we use a global schedule string or null if the system allows.
        // Assuming we can pass null for schedule_id if it's a global aircraft event.
        // Wait, operational_logs migration requires flight_schedule_id? Let's check or just pass null. 
        // We'll pass null or a dummy uuid. Let's just pass null.
        $this->logService->log('aircraft_status_changed', null, [
            'level' => 'warning',
            'description' => "Aircraft {$event->aircraft->model} status changed from {$event->oldStatus} to {$event->newStatus}.",
            'payload' => [
                'aircraft_id' => $event->aircraft->id,
                'old_status' => $event->oldStatus,
                'new_status' => $event->newStatus,
                'reason' => $event->reason,
            ]
        ]);
    }

    public function handleRotationConflictDetected(\App\Events\Operations\RotationConflictDetected $event): void
    {
        $this->logService->log('rotation_conflict_detected', $event->attemptedSchedule->id, [
            'level' => 'critical',
            'description' => "Rotation conflict detected for aircraft {$event->aircraft->model}.",
            'payload' => [
                'aircraft_id' => $event->aircraft->id,
                'conflicting_schedule_id' => $event->conflictingSchedule->id,
            ]
        ]);
    }

    public function handleCrewDutyWarning(\App\Events\Operations\CrewDutyWarning $event): void
    {
        $this->logService->log('crew_duty_warning', $event->attemptedSchedule->id, [
            'level' => 'warning',
            'description' => "Crew duty warning for {$event->crewMember->crew_code}: {$event->reason}.",
            'payload' => [
                'crew_member_id' => $event->crewMember->id,
                'reason' => $event->reason,
            ]
        ]);
    }

    public function handleRotationDelayPropagated(\App\Events\Operations\RotationDelayPropagated $event): void
    {
        $this->logService->log('rotation_delay_propagated', $event->impactedSchedule->id, [
            'level' => 'danger',
            'description' => "Cascading delay of {$event->delayMinutes} mins propagated from flight {$event->sourceSchedule->flight->flight_number}.",
            'payload' => [
                'source_schedule_id' => $event->sourceSchedule->id,
                'impacted_schedule_id' => $event->impactedSchedule->id,
                'delay_minutes' => $event->delayMinutes,
            ]
        ]);
    }

    public function handleCrewLegalityViolation(\App\Events\Operations\CrewLegalityViolation $event): void
    {
        $this->logService->log('crew_legality_violation', $event->schedule->id, [
            'level' => 'critical',
            'description' => "CRITICAL: Crew legality busted for {$event->crewMember->crew_code}. {$event->reason}",
            'payload' => [
                'crew_member_id' => $event->crewMember->id,
                'projected_hours' => $event->projectedHours,
                'reason' => $event->reason,
            ]
        ]);
    }

    public function handleAircraftGrounded(\App\Events\Operations\AircraftGrounded $event): void
    {
        $this->logService->log('aircraft_grounded', null, [
            'level' => 'warning',
            'description' => "Aircraft {$event->aircraft->model} grounded for {$event->maintenance->maintenance_type}.",
            'payload' => [
                'aircraft_id' => $event->aircraft->id,
                'maintenance_id' => $event->maintenance->id,
            ]
        ]);
    }

    public function handleAircraftReleasedFromMaintenance(\App\Events\Operations\AircraftReleasedFromMaintenance $event): void
    {
        $this->logService->log('aircraft_released', null, [
            'level' => 'info',
            'description' => "Aircraft {$event->aircraft->model} released from maintenance.",
            'payload' => [
                'aircraft_id' => $event->aircraft->id,
                'maintenance_id' => $event->maintenance->id,
            ]
        ]);
    }

    public function handleAircraftGroundedConflict(\App\Events\Operations\AircraftGroundedConflict $event): void
    {
        // For conflict, we can log it against the first conflicting schedule or just globally.
        // Let's log against the first one, or loop through all.
        // Better to loop and log for each impacted schedule.
        foreach ($event->conflictingSchedules as $schedule) {
            $this->logService->log('aircraft_grounded_conflict', $schedule->id, [
                'level' => 'critical',
                'description' => "CRITICAL: Aircraft {$event->aircraft->model} grounded. Flight {$schedule->flight->flight_number} requires equipment swap.",
                'payload' => [
                    'aircraft_id' => $event->aircraft->id,
                    'maintenance_id' => $event->maintenance->id,
                    'flight_schedule_id' => $schedule->id,
                ]
            ]);
        }
    }

    public function handleGateConflictDetected(\App\Events\Operations\GateConflictDetected $event): void
    {
        $this->logService->log('gate_conflict_detected', $event->schedule->id, [
            'level' => 'critical',
            'description' => "CRITICAL: " . $event->reason,
            'payload' => [
                'gate_id' => $event->gate->id,
                'terminal' => $event->gate->terminal,
                'gate_number' => $event->gate->gate_number,
                'conflicting_schedule_id' => $event->conflictingSchedule->id,
            ]
        ]);
    }
}
