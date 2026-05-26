<?php

namespace App\Events\Operations;

use App\Models\FlightSchedule;
use App\Models\AirportGate;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GateChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $schedule;
    public $oldGate;
    public $newGate;
    public $type; // departure or arrival

    /**
     * Create a new event instance.
     */
    public function __construct(FlightSchedule $schedule, ?AirportGate $oldGate, AirportGate $newGate, string $type)
    {
        $this->schedule = $schedule;
        $this->oldGate = $oldGate;
        $this->newGate = $newGate;
        $this->type = $type;
    }
}
