<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GateOccupancy extends Model
{
    protected $fillable = [
        'gate_id',
        'flight_schedule_id',
        'occupied_from',
        'occupied_until',
        'occupancy_type',
    ];

    protected $casts = [
        'occupied_from' => 'datetime',
        'occupied_until' => 'datetime',
    ];
}
