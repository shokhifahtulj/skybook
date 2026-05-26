<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BaggageTag extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'booking_passenger_ancillary_id',
        'tag_number',
        'weight_kg',
        'destination_airport_code',
        'status',
        'signature',
        'issued_at',
        'loaded_at',
        'delivered_at'
    ];

    protected $casts = [
        'weight_kg' => 'decimal:2',
        'issued_at' => 'datetime',
        'loaded_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function ancillary()
    {
        return $this->belongsTo(BookingPassengerAncillary::class, 'booking_passenger_ancillary_id');
    }
}
