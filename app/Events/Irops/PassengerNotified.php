<?php

namespace App\Events\Irops;

use App\Models\BookingSegmentPassenger;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PassengerNotified
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $bsp;
    public $type;
    public $message;

    public function __construct(BookingSegmentPassenger $bsp, string $type, string $message)
    {
        $this->bsp = $bsp;
        $this->type = $type;
        $this->message = $message;
    }
}
