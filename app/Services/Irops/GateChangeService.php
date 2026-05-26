<?php

namespace App\Services\Irops;

use App\Events\Irops\GateChanged;
use App\Models\FlightSchedule;
use Illuminate\Support\Facades\DB;

class GateChangeService
{
    protected $regenService;
    protected $notificationService;

    public function __construct(
        BoardingPassRegenerationService $regenService,
        PassengerNotificationService $notificationService
    ) {
        $this->regenService = $regenService;
        $this->notificationService = $notificationService;
    }

    /**
     * Change gate and trigger workflows
     */
    public function changeGate(FlightSchedule $schedule, string $newGate, ?string $reason = null): FlightSchedule
    {
        return DB::transaction(function () use ($schedule, $newGate, $reason) {
            $oldGate = $schedule->gate;
            
            $schedule->update([
                'gate' => $newGate,
            ]);

            GateChanged::dispatch($schedule, $oldGate, $newGate, $reason);

            // Trigger BP Regeneration
            $this->regenService->regenerateForSchedule($schedule, 'Gate Change to ' . $newGate);

            // Notify Passengers
            $message = "Your gate for flight {$schedule->flight->flight_number} has been changed to Gate {$newGate}. Please refresh your boarding pass.";
            $this->notificationService->notifyFlightPassengers($schedule, 'GATE_CHANGE', $message);

            return $schedule;
        });
    }
}
