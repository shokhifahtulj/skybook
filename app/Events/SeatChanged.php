<?php

namespace App\Events;

use App\Models\BookingSegmentPassenger;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SeatChanged
{
    use Dispatchable, SerializesModels;

    public $segmentPassenger;
    public $oldSeatId;
    public $newSeatId;

    /**
     * Create a new event instance.
     */
    public function __construct(BookingSegmentPassenger $segmentPassenger, $oldSeatId, $newSeatId)
    {
        $this->segmentPassenger = $segmentPassenger;
        $this->oldSeatId = $oldSeatId;
        $this->newSeatId = $newSeatId;
    }
}
