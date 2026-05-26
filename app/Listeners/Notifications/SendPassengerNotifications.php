<?php

namespace App\Listeners\Notifications;

use App\Events\Operations\GateChanged;
use App\Events\Operations\FlightDisrupted;
use App\Events\Operations\PassengerRebooked;
use App\Services\Notifications\NotificationOrchestratorService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendPassengerNotifications implements ShouldQueue
{
    use InteractsWithQueue;

    protected $orchestrator;

    /**
     * Create the event listener.
     */
    public function __construct(NotificationOrchestratorService $orchestrator)
    {
        $this->orchestrator = $orchestrator;
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        if ($event instanceof GateChanged) {
            $this->orchestrator->handleGateChange($event->schedule, $event->oldGate, $event->newGate, $event->type);
        } elseif ($event instanceof FlightDisrupted) {
            $this->orchestrator->handleFlightDisrupted($event->schedule, $event->type, $event->reason);
        } elseif ($event instanceof PassengerRebooked) {
            $this->orchestrator->handlePassengerRebooked($event->bookingSegmentPassenger, $event->oldSchedule, $event->newSchedule, $event->reason);
        }
    }
}
