<?php

namespace App\Events;

use App\Models\BookingSegmentPassenger;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PassengerCheckedIn
{
    use Dispatchable, SerializesModels;

    public $segmentPassenger;

    /**
     * Create a new event instance.
     */
    public function __construct(BookingSegmentPassenger $segmentPassenger)
    {
        $this->segmentPassenger = $segmentPassenger;
    }
}
