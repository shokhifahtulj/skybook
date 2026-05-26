<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FlightSchedule extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'flight_id', 
        'aircraft_id',
        'previous_schedule_id',
        'next_schedule_id',
        'departure_datetime', 
        'arrival_datetime', 
        'gate', 
        'terminal', 
        'status', 
        'delay_minutes',
        'delay_source',
        'delay_reason',
        'available_seats',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'departure_datetime' => 'datetime',
        'arrival_datetime' => 'datetime',
    ];

    public function flight()
    {
        return $this->belongsTo(Flight::class);
    }

    public function aircraft()
    {
        return $this->belongsTo(Aircraft::class, 'aircraft_id');
    }

    public function assignedAircraft()
    {
        return $this->aircraft();
    }

    public function previousSchedule()
    {
        return $this->belongsTo(FlightSchedule::class, 'previous_schedule_id');
    }

    public function nextSchedule()
    {
        return $this->belongsTo(FlightSchedule::class, 'next_schedule_id');
    }

    public function crewAssignments()
    {
        return $this->hasMany(FlightCrewAssignment::class);
    }

    public function departureGate()
    {
        return $this->belongsTo(AirportGate::class, 'departure_gate_id');
    }

    public function arrivalGate()
    {
        return $this->belongsTo(AirportGate::class, 'arrival_gate_id');
    }

    public function seats()
    {
        return $this->hasMany(FlightScheduleSeat::class, 'flight_schedule_id');
    }

    public function prices()
    {
        return $this->hasMany(FlightSchedulePrice::class);
    }

    public function bookingSegments()
    {
        return $this->hasMany(BookingSegment::class, 'flight_schedule_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}