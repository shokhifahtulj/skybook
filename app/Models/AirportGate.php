<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AirportGate extends Model
{
    protected $fillable = [
        'airport_id',
        'terminal',
        'gate_number',
        'status',
    ];

    public function airport()
    {
        return $this->belongsTo(Airport::class);
    }

    public function departureSchedules()
    {
        return $this->hasMany(FlightSchedule::class, 'departure_gate_id');
    }

    public function arrivalSchedules()
    {
        return $this->hasMany(FlightSchedule::class, 'arrival_gate_id');
    }
}
