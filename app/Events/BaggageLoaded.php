<?php

namespace App\Events;

use App\Models\BaggageTag;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BaggageLoaded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $tag;

    /**
     * Create a new event instance.
     */
    public function __construct(BaggageTag $tag)
    {
        $this->tag = $tag;
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
