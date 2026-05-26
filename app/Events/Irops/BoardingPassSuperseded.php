<?php

namespace App\Events\Irops;

use App\Models\BoardingPass;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BoardingPassSuperseded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $oldPass;
    public $reason;

    public function __construct(BoardingPass $oldPass, string $reason)
    {
        $this->oldPass = $oldPass;
        $this->reason = $reason;
    }
}
