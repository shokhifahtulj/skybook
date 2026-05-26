<?php

namespace App\Listeners;

use App\Events\AncillaryPurchased;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogAncillaryPurchase
{
    protected $logService;

    /**
     * Create the event listener.
     */
    public function __construct(\App\Services\Operations\OperationalLogService $logService)
    {
        $this->logService = $logService;
    }

    /**
     * Handle the event.
     */
    public function handle(AncillaryPurchased $event): void
    {
        $ancillary = $event->ancillary;
        
        $this->logService->log('ancillary_purchased', $ancillary->bookingSegmentPassenger->segment->flight_schedule_id, [
            'entity_type' => 'booking_passenger_ancillary',
            'entity_id' => $ancillary->id,
            'booking_id' => $ancillary->bookingSegmentPassenger->booking_id,
            'passenger_id' => $ancillary->bookingSegmentPassenger->passenger_id,
            'actor_type' => 'Passenger/System',
            'level' => 'info',
            'payload' => [
                'type' => $ancillary->type,
                'snapshot_name' => $ancillary->snapshot_name,
                'metadata' => $ancillary->metadata,
            ]
        ]);
    }
}
