<?php

namespace App\Events\Irops;

use App\Models\FlightSchedule;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class FlightDelayed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $schedule;
    public $oldDepartureTime;
    public $newDepartureTime;
    public $delayMinutes;

    public function __construct(FlightSchedule $schedule, Carbon $oldDepartureTime, Carbon $newDepartureTime, int $delayMinutes)
    {
        $this->schedule = $schedule;
        $this->oldDepartureTime = $oldDepartureTime;
        $this->newDepartureTime = $newDepartureTime;
        $this->delayMinutes = $delayMinutes;
    }
}
