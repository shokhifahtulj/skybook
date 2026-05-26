<?php

namespace App\Events;

use App\Models\BoardingPass;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BoardingPassRegenerated
{
    use Dispatchable, SerializesModels;

    public $boardingPass;

    public function __construct(BoardingPass $boardingPass)
    {
        $this->boardingPass = $boardingPass;
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
