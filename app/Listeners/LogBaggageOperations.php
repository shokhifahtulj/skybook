<?php

namespace App\Listeners;

use App\Events\BaggageLoaded;
use App\Events\BaggageTagGenerated;
use App\Services\Operations\OperationalLogService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Events\Dispatcher;

class LogBaggageOperations
{
    protected $logService;

    public function __construct(OperationalLogService $logService)
    {
        $this->logService = $logService;
    }

    public function subscribe(Dispatcher $events): void
    {
        $events->listen(
            BaggageTagGenerated::class,
            [LogBaggageOperations::class, 'handleTagGenerated']
        );

        $events->listen(
            BaggageLoaded::class,
            [LogBaggageOperations::class, 'handleTagLoaded']
        );
    }

    public function handleTagGenerated(BaggageTagGenerated $event): void
    {
        $tag = $event->tag;
        $scheduleId = $tag->ancillary->bookingSegmentPassenger->bookingSegment->flight_schedule_id;

        $this->logService->log('baggage_checked_in', $scheduleId, [
            'entity_type' => 'baggage_tag',
            'entity_id' => $tag->id,
            'booking_id' => $tag->ancillary->bookingSegmentPassenger->booking_id,
            'passenger_id' => $tag->ancillary->bookingSegmentPassenger->passenger_id,
            'actor_type' => 'Staff',
            'level' => 'info',
            'description' => "Baggage tag {$tag->tag_number} generated and checked in for {$tag->destination_airport_code}.",
            'payload' => [
                'tag_number' => $tag->tag_number,
                'weight_kg' => $tag->weight_kg,
            ]
        ]);
    }

    public function handleTagLoaded(BaggageLoaded $event): void
    {
        $tag = $event->tag;
        $scheduleId = $tag->ancillary->bookingSegmentPassenger->bookingSegment->flight_schedule_id;

        $this->logService->log('baggage_loaded', $scheduleId, [
            'entity_type' => 'baggage_tag',
            'entity_id' => $tag->id,
            'booking_id' => $tag->ancillary->bookingSegmentPassenger->booking_id,
            'passenger_id' => $tag->ancillary->bookingSegmentPassenger->passenger_id,
            'actor_type' => 'BaggageHandler',
            'level' => 'info',
            'description' => "Baggage {$tag->tag_number} loaded onto aircraft.",
            'payload' => [
                'tag_number' => $tag->tag_number,
                'weight_kg' => $tag->weight_kg,
            ]
        ]);
    }
}
