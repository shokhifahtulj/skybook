<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrewMember extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'crew_role_id',
        'crew_code',
        'first_name',
        'last_name',
        'base_airport_id',
        'operational_status',
        'license_number',
        'license_expiry',
    ];

    protected $casts = [
        'license_expiry' => 'date',
    ];

    public function role()
    {
        return $this->belongsTo(CrewRole::class, 'crew_role_id');
    }

    public function baseAirport()
    {
        return $this->belongsTo(Airport::class, 'base_airport_id');
    }

    public function assignments()
    {
        return $this->hasMany(FlightCrewAssignment::class);
    }
}
