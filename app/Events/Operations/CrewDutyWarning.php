<?php

namespace App\Events\Operations;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CrewDutyWarning
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $crewMember;
    public $attemptedSchedule;
    public $reason;
    public $user;

    /**
     * Create a new event instance.
     */
    public function __construct($crewMember, $attemptedSchedule, $reason, $user = null)
    {
        $this->crewMember = $crewMember;
        $this->attemptedSchedule = $attemptedSchedule;
        $this->reason = $reason;
        $this->user = $user;
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
