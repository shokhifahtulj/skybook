<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GateSwapLog extends Model
{
    protected $fillable = [
        'flight_schedule_id',
        'old_gate_id',
        'new_gate_id',
        'swapped_by',
        'reason',
        'swap_type',
    ];
}
