<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Passenger extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'booking_id',
        'title',
        'first_name',
        'last_name',
        'identity_type',
        'identity_number',
        'date_of_birth',
        'nationality',
        'passenger_type'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function segmentPassengers()
    {
        return $this->hasMany(BookingSegmentPassenger::class);
    }
}
