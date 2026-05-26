<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingSegment extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'booking_id',
        'flight_schedule_id',
        'segment_order',
        'cabin_class',
        'segment_status',
        'fare_snapshot',
        'tax_snapshot'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function schedule()
    {
        return $this->belongsTo(FlightSchedule::class, 'flight_schedule_id');
    }

    public function segmentPassengers()
    {
        return $this->hasMany(BookingSegmentPassenger::class);
    }

    public function bookingSegmentPassengers()
    {
        return $this->segmentPassengers();
    }
}
