<?php

namespace App\Events\Operations;

use App\Models\FlightSchedule;
use App\Models\AirportGate;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GateConflictDetected
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $schedule;
    public $gate;
    public $conflictingSchedule;
    public $reason;
    public $severityLevel;

    /**
     * Create a new event instance.
     */
    public function __construct(FlightSchedule $schedule, AirportGate $gate, FlightSchedule $conflictingSchedule, string $reason, string $severityLevel = 'MEDIUM')
    {
        $this->schedule = $schedule;
        $this->gate = $gate;
        $this->conflictingSchedule = $conflictingSchedule;
        $this->reason = $reason;
        $this->severityLevel = $severityLevel;
    }
}
