<?php

namespace App\Events\Operations;

use App\Models\CrewMember;
use App\Models\FlightSchedule;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CrewLegalityViolation
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $crewMember;
    public $schedule;
    public $reason;
    public $projectedHours;

    /**
     * Create a new event instance.
     */
    public function __construct(CrewMember $crewMember, FlightSchedule $schedule, string $reason, int $projectedHours)
    {
        $this->crewMember = $crewMember;
        $this->schedule = $schedule;
        $this->reason = $reason;
        $this->projectedHours = $projectedHours;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
