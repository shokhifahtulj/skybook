<?php

namespace App\Events\Operations;

use App\Models\Aircraft;
use App\Models\AircraftMaintenanceEvent;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AircraftGrounded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $aircraft;
    public $maintenance;

    /**
     * Create a new event instance.
     */
    public function __construct(Aircraft $aircraft, AircraftMaintenanceEvent $maintenance)
    {
        $this->aircraft = $aircraft;
        $this->maintenance = $maintenance;
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
