<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingReassignment extends Model
{
    protected $fillable = [
        'booking_segment_passenger_id',
        'from_flight_schedule_id',
        'to_flight_schedule_id',
        'reason',
        'triggered_by_event',
    ];

    public function bookingSegmentPassenger()
    {
        return $this->belongsTo(BookingSegmentPassenger::class);
    }

    public function fromSchedule()
    {
        return $this->belongsTo(FlightSchedule::class, 'from_flight_schedule_id');
    }

    public function toSchedule()
    {
        return $this->belongsTo(FlightSchedule::class, 'to_flight_schedule_id');
    }
}
