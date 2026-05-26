<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'pnr',
        'booking_status',
        'payment_status',
        'total_amount',
        'currency',
        'booked_by',
        'user_id',
        'schedule_id',
        'flight_id',
        'jumlah_tiket',
        'total_harga',
        'status_booking',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'total_amount' => 'decimal:2',
        'total_harga' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bookedBy()
    {
        return $this->belongsTo(User::class, 'booked_by');
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function flight()
    {
        return $this->belongsTo(Flight::class);
    }

    public function segments()
    {
        return $this->hasMany(BookingSegment::class)->orderBy('segment_order');
    }

    public function passengers()
    {
        return $this->hasMany(Passenger::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function baggages()
    {
        return BookingPassengerAncillary::query()->whereHas('bookingSegmentPassenger.segment', function ($query) {
            $query->where('booking_id', $this->id);
        });
    }
}
