<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationDelivery extends Model
{
    protected $fillable = [
        'booking_segment_passenger_id',
        'flight_schedule_id',
        'event_type',
        'channel',
        'recipient',
        'idempotency_key',
        'message_version',
        'payload_snapshot',
        'priority_level',
        'delivery_status',
        'sent_at',
        'acknowledged_at',
    ];

    protected $casts = [
        'payload_snapshot' => 'array',
        'sent_at' => 'datetime',
        'acknowledged_at' => 'datetime',
    ];

    public function passengerSegment()
    {
        return $this->belongsTo(\App\Models\BookingSegmentPassenger::class, 'booking_segment_passenger_id');
    }
}
