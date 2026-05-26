<?php

namespace App\Events\Inventory;

use App\Models\FlightSchedule;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InventoryGenerated
{
    use Dispatchable, SerializesModels;

    public $schedule;

    public function __construct(FlightSchedule $schedule)
    {
        $this->schedule = $schedule;
    }
}
