<?php

namespace App\Listeners;

use App\Events\SeatChanged;
use App\Jobs\GenerateBoardingPassPdf;
use App\Services\Operations\BoardingPassService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RegenerateBoardingPassOnSeatChange
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
    public function handle(SeatChanged $event): void
    {
        // Jika penumpang sudah check-in, maka boarding pass perlu diregenerate
        if ($event->segmentPassenger->operational_status === 'checked_in') {
            $boardingPass = $this->boardingPassService->regenerate($event->segmentPassenger);
            GenerateBoardingPassPdf::dispatch($boardingPass->id);
            
            $this->logService->log('seat_changed', $event->segmentPassenger->segment->flight_schedule_id, [
                'entity_type' => 'boarding_pass',
                'entity_id' => $boardingPass->id,
                'booking_id' => $event->segmentPassenger->booking_id,
                'passenger_id' => $event->segmentPassenger->passenger_id,
                'actor_type' => 'System/Passenger',
                'payload' => ['new_seat' => $event->segmentPassenger->seat->seat_number ?? null]
            ]);
        }
    }
}
