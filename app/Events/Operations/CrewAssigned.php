<?php

namespace App\Events\Operations;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CrewAssigned
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $assignment;
    public $user;

    /**
     * Create a new event instance.
     */
    public function __construct($assignment, $user = null)
    {
        $this->assignment = $assignment;
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
