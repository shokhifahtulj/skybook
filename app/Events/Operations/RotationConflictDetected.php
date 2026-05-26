<?php

namespace App\Events\Operations;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RotationConflictDetected
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $aircraft;
    public $conflictingSchedule;
    public $attemptedSchedule;
    public $user;

    /**
     * Create a new event instance.
     */
    public function __construct($aircraft, $conflictingSchedule, $attemptedSchedule, $user = null)
    {
        $this->aircraft = $aircraft;
        $this->conflictingSchedule = $conflictingSchedule;
        $this->attemptedSchedule = $attemptedSchedule;
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
