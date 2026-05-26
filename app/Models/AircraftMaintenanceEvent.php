<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AircraftMaintenanceEvent extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'aircraft_id',
        'maintenance_type',
        'status',
        'severity',
        'start_at',
        'end_at',
        'notes',
        'created_by',
        'dispatch_released_at',
        'dispatch_released_by',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'dispatch_released_at' => 'datetime',
    ];

    public function aircraft()
    {
        return $this->belongsTo(Aircraft::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function dispatchReleasedBy()
    {
        return $this->belongsTo(User::class, 'dispatch_released_by');
    }
}
