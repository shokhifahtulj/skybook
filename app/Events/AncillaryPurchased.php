<?php

namespace App\Events;

use App\Models\BookingPassengerAncillary;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AncillaryPurchased
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $ancillary;

    /**
     * Create a new event instance.
     */
    public function __construct(BookingPassengerAncillary $ancillary)
    {
        $this->ancillary = $ancillary;
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
