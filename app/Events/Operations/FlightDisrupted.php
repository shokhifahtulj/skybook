<?php

namespace App\Events\Operations;

use App\Models\FlightSchedule;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FlightDisrupted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $schedule;
    public $reason;
    public $type; // cancelled, long_delay

    /**
     * Create a new event instance.
     */
    public function __construct(FlightSchedule $schedule, string $type, string $reason)
    {
        $this->schedule = $schedule;
        $this->type = $type;
        $this->reason = $reason;
    }
}
