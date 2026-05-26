<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FlightScheduleSeat extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'flight_schedule_id',
        'aircraft_seat_id',
        'seat_number',
        'cabin_class',
        'is_window',
        'is_aisle',
        'is_exit_row',
        'status',
        'booking_id',
        'locked_by',
        'lock_session',
        'locked_until',
        'reserved_at',
        'booked_at'
    ];

    protected $casts = [
        'is_window' => 'boolean',
        'is_aisle' => 'boolean',
        'is_exit_row' => 'boolean',
        'locked_until' => 'datetime',
        'reserved_at' => 'datetime',
        'booked_at' => 'datetime',
    ];

    public function getRowNumberAttribute(): int
    {
        if (array_key_exists('row_number', $this->attributes) && $this->attributes['row_number'] !== null) {
            return (int) $this->attributes['row_number'];
        }

        preg_match('/^(\d+)/', (string) $this->seat_number, $matches);

        return isset($matches[1]) ? (int) $matches[1] : 0;
    }

    public function getSeatLetterAttribute(): string
    {
        if (array_key_exists('seat_letter', $this->attributes) && $this->attributes['seat_letter'] !== null) {
            return (string) $this->attributes['seat_letter'];
        }

        preg_match('/([A-Z])$/', strtoupper((string) $this->seat_number), $matches);

        return $matches[1] ?? '';
    }

    public function schedule()
    {
        return $this->belongsTo(FlightSchedule::class, 'flight_schedule_id');
    }

    public function aircraftSeat()
    {
        return $this->belongsTo(AircraftSeat::class, 'aircraft_seat_id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function bookingSegmentPassengers()
    {
        return $this->hasMany(BookingSegmentPassenger::class, 'flight_schedule_seat_id');
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeBooked($query)
    {
        return $query->where('status', 'booked');
    }

    public function scopeLocked($query)
    {
        return $query->where('status', 'locked');
    }
}
