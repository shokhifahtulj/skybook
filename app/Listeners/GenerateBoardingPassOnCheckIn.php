<?php

namespace App\Listeners;

use App\Events\PassengerCheckedIn;
use App\Jobs\GenerateBoardingPassPdf;
use App\Services\Operations\BoardingPassService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class GenerateBoardingPassOnCheckIn
{
    protected $boardingPassService;
    protected $logService;

    /**
     * Create the event listener.
     */
    public function __construct(BoardingPassService $boardingPassService, \App\Services\Operations\OperationalLogService $logService)
    {
        $this->boardingPassService = $boardingPassService;
        $this->logService = $logService;
    }

    /**
     * Handle the event.
     */
    public function handle(PassengerCheckedIn $event): void
    {
        // 1. Generate the boarding pass database record
        $boardingPass = $this->boardingPassService->generate($event->segmentPassenger);

        // 2. Dispatch Job to render the PDF asynchronously
        GenerateBoardingPassPdf::dispatch($boardingPass->id);
        
        // 3. Log Operational Event
        $this->logService->log('checked_in', $event->segmentPassenger->segment->flight_schedule_id, [
            'entity_type' => 'boarding_pass',
            'entity_id' => $boardingPass->id,
            'booking_id' => $event->segmentPassenger->booking_id,
            'passenger_id' => $event->segmentPassenger->passenger_id,
            'actor_type' => 'Passenger',
            'payload' => ['seat' => $event->segmentPassenger->seat->seat_number ?? null]
        ]);
    }
}
