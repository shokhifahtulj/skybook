<?php

namespace App\Events\Irops;

use App\Models\FlightSchedule;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FlightCancelled
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $schedule;
    public $reason;

    public function __construct(FlightSchedule $schedule, string $reason)
    {
        $this->schedule = $schedule;
        $this->reason = $reason;
    }
}
