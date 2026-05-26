<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecoveryRecommendation extends Model
{
    protected $fillable = [
        'flight_schedule_id',
        'simulation_session_id',
        'recommendation_payload',
        'selected_strategy_id',
        'final_score',
        'execution_outcome',
    ];

    protected $casts = [
        'recommendation_payload' => 'array',
    ];
}
