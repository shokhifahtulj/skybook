<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Aircraft extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'aircrafts';
    
    protected $fillable = [
        'airline_id',
        'model',
        'capacity',
        'seat_layout',
        'status',
        'operational_status',
    ];

    public function airline()
    {
        return $this->belongsTo(Airline::class);
    }

    public function flights()
    {
        return $this->hasMany(Flight::class);
    }

    public function seats()
    {
        return $this->hasMany(AircraftSeat::class);
    }

    public function maintenanceEvents()
    {
        return $this->hasMany(AircraftMaintenanceEvent::class);
    }
}