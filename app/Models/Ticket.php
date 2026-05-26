<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'booking_segment_passenger_id',
        'ticket_number',
        'ticket_status',
        'document_path',
        'snapshot_data',
        'issued_at'
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'snapshot_data' => 'array',
    ];

    public function segmentPassenger()
    {
        return $this->belongsTo(BookingSegmentPassenger::class, 'booking_segment_passenger_id');
    }
}
