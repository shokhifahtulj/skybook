<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PredictiveAlert extends Model
{
    protected $fillable = [
        'flight_schedule_id',
        'prediction_type',
        'severity',
        'description',
        'confidence_score',
        'forecast_window_minutes',
        'predicted_at',
        'resolved_at',
        'resolution_method',
        'status',
        'automation_payload',
    ];

    protected $casts = [
        'predicted_at' => 'datetime',
        'resolved_at' => 'datetime',
        'automation_payload' => 'array',
    ];

    public function flightSchedule()
    {
        return $this->belongsTo(FlightSchedule::class);
    }
}
