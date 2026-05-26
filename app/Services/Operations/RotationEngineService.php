<?php

namespace App\Services\Operations;

use App\Models\FlightSchedule;
use App\Services\Irops\DelayManagementService;
use App\Events\Operations\RotationDelayPropagated;
use Illuminate\Support\Carbon;

class RotationEngineService
{
    const MINIMUM_TURNAROUND_MINUTES = 45;

    protected $delayManagementService;

    public function __construct(DelayManagementService $delayManagementService)
    {
        $this->delayManagementService = $delayManagementService;
    }

    /**
     * Monitor and propagate delays across the aircraft rotation chain
     * recursively but deterministically based on minimum turnaround time.
     */
    public function propagateDelay(FlightSchedule $sourceSchedule): void
    {
        if (!$sourceSchedule->next_schedule_id) {
            return; // End of chain
        }

        $nextSchedule = FlightSchedule::find($sourceSchedule->next_schedule_id);

        if (!$nextSchedule || $nextSchedule->status === 'cancelled') {
            return; // Chain broken or next flight cancelled
        }

        $arrivalOfSource = Carbon::parse($sourceSchedule->arrival_datetime);
        $departureOfNext = Carbon::parse($nextSchedule->departure_datetime);

        // Required departure time for the next flight to satisfy MTT
        $requiredDeparture = $arrivalOfSource->copy()->addMinutes(self::MINIMUM_TURNAROUND_MINUTES);

        // If the required departure is later than the scheduled/current departure of next flight
        if ($requiredDeparture->greaterThan($departureOfNext)) {
            $delayMinutes = $departureOfNext->diffInMinutes($requiredDeparture);

            // We must delay the next flight
            $delayReason = "Late inbound aircraft ({$sourceSchedule->flight->flight_number})";

            // Delay it! This will trigger another FlightDelayed event which we will catch and recurse
            $this->delayManagementService->declareDelay(
                $nextSchedule, 
                $requiredDeparture, 
                $delayMinutes, 
                'rotation', 
                $delayReason
            );

            // Emit explicit Rotation Delay event for logging/OCC warning
            event(new RotationDelayPropagated($sourceSchedule, $nextSchedule, $delayMinutes));
        }
    }
}
