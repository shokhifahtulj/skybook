<?php

namespace App\Events\Operations;

use App\Models\BookingSegmentPassenger;
use App\Models\FlightSchedule;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PassengerRebooked
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $bookingSegmentPassenger;
    public $oldSchedule;
    public $newSchedule;
    public $reason;

    /**
     * Create a new event instance.
     */
    public function __construct(BookingSegmentPassenger $bsp, FlightSchedule $oldSchedule, FlightSchedule $newSchedule, string $reason)
    {
        $this->bookingSegmentPassenger = $bsp;
        $this->oldSchedule = $oldSchedule;
        $this->newSchedule = $newSchedule;
        $this->reason = $reason;
    }
}
