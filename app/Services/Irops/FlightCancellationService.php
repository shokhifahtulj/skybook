<?php

namespace App\Services\Irops;

use App\Events\Irops\FlightCancelled;
use App\Models\BoardingPass;
use App\Models\FlightSchedule;
use Illuminate\Support\Facades\DB;

class FlightCancellationService
{
    protected $notificationService;

    public function __construct(PassengerNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Cancel flight and trigger workflows
     */
    public function cancelFlight(FlightSchedule $schedule, string $reason): FlightSchedule
    {
        return DB::transaction(function () use ($schedule, $reason) {
            $schedule->update([
                'status' => 'cancelled',
            ]);

            FlightCancelled::dispatch($schedule, $reason);

            // Revoke all active boarding passes
            $boardingPasses = BoardingPass::whereHas('bookingSegmentPassenger.bookingSegment', function ($q) use ($schedule) {
                    $q->where('flight_schedule_id', $schedule->id);
                })
                ->whereIn('status', ['generated', 'active'])
                ->get();

            foreach ($boardingPasses as $pass) {
                $pass->update([
                    'status' => 'revoked',
                    'revoked_at' => now(),
                ]);
            }

            // Halt baggage operations and freeze check-in
            // For MVP, freezing checkin is done naturally by the status == 'cancelled'

            // Notify Passengers
            $message = "URGENT: Flight {$schedule->flight->flight_number} has been cancelled. Reason: {$reason}. Please contact customer service for reaccommodation options.";
            $this->notificationService->notifyFlightPassengers($schedule, 'CANCELLED', $message);

            return $schedule;
        });
    }
}
