<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class FlightCrewAssignment extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'flight_schedule_id',
        'crew_member_id',
        'crew_role_id',
        'previous_assignment_id',
        'next_assignment_id',
        'assigned_at',
        'assigned_by',
        'status', // assigned, replaced, removed
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
    ];

    public function schedule()
    {
        return $this->belongsTo(FlightSchedule::class, 'flight_schedule_id');
    }

    public function crewMember()
    {
        return $this->belongsTo(CrewMember::class, 'crew_member_id');
    }

    public function role()
    {
        return $this->belongsTo(CrewRole::class, 'crew_role_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function previousAssignment()
    {
        return $this->belongsTo(FlightCrewAssignment::class, 'previous_assignment_id');
    }

    public function nextAssignment()
    {
        return $this->belongsTo(FlightCrewAssignment::class, 'next_assignment_id');
    }
}
