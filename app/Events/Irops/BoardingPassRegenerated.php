<?php

namespace App\Events\Irops;

use App\Models\BoardingPass;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BoardingPassRegenerated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $oldPass;
    public $newPass;
    public $reason;

    public function __construct(BoardingPass $oldPass, BoardingPass $newPass, string $reason)
    {
        $this->oldPass = $oldPass;
        $this->newPass = $newPass;
        $this->reason = $reason;
    }
}
