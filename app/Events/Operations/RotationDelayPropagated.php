<?php

namespace App\Events\Operations;

use App\Models\FlightSchedule;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RotationDelayPropagated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $sourceSchedule;
    public $impactedSchedule;
    public $delayMinutes;

    /**
     * Create a new event instance.
     */
    public function __construct(FlightSchedule $sourceSchedule, FlightSchedule $impactedSchedule, int $delayMinutes)
    {
        $this->sourceSchedule = $sourceSchedule;
        $this->impactedSchedule = $impactedSchedule;
        $this->delayMinutes = $delayMinutes;
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
