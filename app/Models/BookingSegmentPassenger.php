<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingSegmentPassenger extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'booking_segment_id',
        'passenger_id',
        'flight_schedule_seat_id',
        'ticket_number',
        'operational_status',
        'checked_in_at',
        'boarding_pass_issued_at',
        'boarded_at'
    ];

    protected $casts = [
        'checked_in_at' => 'datetime',
        'boarding_pass_issued_at' => 'datetime',
        'boarded_at' => 'datetime',
    ];

    public function segment()
    {
        return $this->belongsTo(BookingSegment::class, 'booking_segment_id');
    }

    public function bookingSegment()
    {
        return $this->segment();
    }

    public function passenger()
    {
        return $this->belongsTo(Passenger::class);
    }

    public function seat()
    {
        return $this->belongsTo(FlightScheduleSeat::class, 'flight_schedule_seat_id');
    }

    public function ticket()
    {
        return $this->hasOne(Ticket::class, 'booking_segment_passenger_id');
    }

    public function ancillaries()
    {
        return $this->hasMany(BookingPassengerAncillary::class, 'booking_segment_passenger_id');
    }

    public function reassignments()
    {
        return $this->hasMany(BookingReassignment::class, 'booking_segment_passenger_id')->orderBy('created_at', 'desc');
    }

    /**
     * Enterprise-Safe Accessor: Get the effective flight schedule 
     * considering any reassignments (IROPS).
     */
    public function getCurrentScheduleAttribute()
    {
        $latestReassignment = $this->reassignments()->first();
        if ($latestReassignment) {
            return $latestReassignment->toSchedule;
        }

        return $this->segment->flightSchedule;
    }
}
