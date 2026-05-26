<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Flight extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = ['flight_number', 'airline_id', 'route_id', 'aircraft_id'];

    public function airline()
    {
        return $this->belongsTo(Airline::class);
    }

    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function aircraft()
    {
        return $this->belongsTo(Aircraft::class);
    }

    public function schedules()
    {
        return $this->hasMany(FlightSchedule::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}