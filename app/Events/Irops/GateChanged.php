<?php

namespace App\Events\Irops;

use App\Models\FlightSchedule;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GateChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $schedule;
    public $oldGate;
    public $newGate;
    public $reason;

    public function __construct(FlightSchedule $schedule, ?string $oldGate, string $newGate, ?string $reason)
    {
        $this->schedule = $schedule;
        $this->oldGate = $oldGate;
        $this->newGate = $newGate;
        $this->reason = $reason;
    }
}
