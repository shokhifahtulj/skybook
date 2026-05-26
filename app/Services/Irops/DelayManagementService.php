<?php

namespace App\Services\Irops;

use App\Events\Irops\FlightDelayed;
use App\Models\FlightSchedule;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DelayManagementService
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
     * Declare delay and trigger workflows
     */
    public function declareDelay(FlightSchedule $schedule, Carbon $newDepartureTime, int $delayMinutes, string $delaySource = 'manual', string $delayReason = null): FlightSchedule
    {
        return DB::transaction(function () use ($schedule, $newDepartureTime, $delayMinutes, $delaySource, $delayReason) {
            $oldDepartureTime = $schedule->departure_datetime;
            $newArrivalTime = Carbon::parse($schedule->arrival_datetime)->addMinutes($delayMinutes);
            
            // accumulate delay if already delayed
            $totalDelayMinutes = $schedule->delay_minutes + $delayMinutes;

            $schedule->update([
                'status' => 'delayed',
                'departure_datetime' => $newDepartureTime,
                'arrival_datetime' => $newArrivalTime,
                'delay_minutes' => $totalDelayMinutes,
                'delay_source' => $delaySource,
                'delay_reason' => $delayReason
            ]);

            FlightDelayed::dispatch($schedule, $oldDepartureTime, $newDepartureTime, $delayMinutes);

            // Trigger BP Regeneration
            $this->regenService->regenerateForSchedule($schedule, "Flight Delayed by {$delayMinutes} minutes");

            // Notify Passengers
            $formattedTime = $newDepartureTime->format('H:i');
            $message = "Flight {$schedule->flight->flight_number} is delayed. New departure time is {$formattedTime}. Please refresh your boarding pass.";
            $this->notificationService->notifyFlightPassengers($schedule, 'DELAY', $message);

            return $schedule;
        });
    }
}
