<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class OperationalLog extends Model
{
    use HasUuids;

    // Menonaktifkan updated_at karena ini adalah append-only immutable log
    const UPDATED_AT = null;

    protected $fillable = [
        'sequence',
        'event_type',
        'entity_type',
        'entity_id',
        'flight_schedule_id',
        'booking_id',
        'passenger_id',
        'actor_type',
        'actor_id',
        'level',
        'event_payload',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'event_payload' => 'array',
        'created_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($log) {
            $lastSeq = static::max('sequence');
            $log->sequence = $lastSeq ? $lastSeq + 1 : 1;
        });
    }

    public function flightSchedule()
    {
        return $this->belongsTo(FlightSchedule::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function passenger()
    {
        return $this->belongsTo(Passenger::class);
    }
}
