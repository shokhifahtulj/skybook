<?php

namespace App\Listeners;

use App\Events\Irops\FlightDelayed;
use App\Services\Operations\RotationEngineService;
use App\Services\Operations\CrewLegalityEngineService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class PropagateRotationDelays
{
    protected $rotationService;
    protected $crewLegalityService;

    /**
     * Create the event listener.
     */
    public function __construct(RotationEngineService $rotationService, CrewLegalityEngineService $crewLegalityService)
    {
        $this->rotationService = $rotationService;
        $this->crewLegalityService = $crewLegalityService;
    }

    /**
     * Handle the event.
     */
    public function handle(FlightDelayed $event): void
    {
        // 1. Evaluate Crew Legality for the delayed schedule
        $this->crewLegalityService->evaluateLegalityForSchedule($event->schedule);

        // 2. Validate Gate Occupancy Conflicts
        $gateService = app(\App\Services\Operations\GateManagementService::class);
        $gateService->validateScheduleGates($event->schedule);

        // 3. Propagate aircraft rotation delay
        $this->rotationService->propagateDelay($event->schedule);
    }
}
