<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class BookingPassengerAncillary extends Model
{
    use HasUuids;

    protected $fillable = [
        'booking_segment_passenger_id',
        'ancillary_service_id',
        'type',
        'snapshot_name',
        'snapshot_price',
        'metadata',
        'status',
        'operational_status',
        'payment_id',
    ];

    protected $casts = [
        'snapshot_price' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function bookingSegmentPassenger()
    {
        return $this->belongsTo(BookingSegmentPassenger::class);
    }

    public function ancillaryService()
    {
        return $this->belongsTo(AncillaryService::class);
    }
}
