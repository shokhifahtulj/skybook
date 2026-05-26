<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SimulationSession extends Model
{
    protected $fillable = [
        'name',
        'scenario_type',
        'scenario_seed',
        'baseline_snapshot',
        'kpi_snapshot',
        'status',
        'created_by',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'baseline_snapshot' => 'array',
        'kpi_snapshot' => 'array',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];
}
