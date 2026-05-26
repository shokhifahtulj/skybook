<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BoardingPass extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'booking_segment_passenger_id',
        'boarding_pass_number',
        'status',
        'gate_snapshot',
        'boarding_group_snapshot',
        'boarding_time_snapshot',
        'qr_signature',
        'pdf_path',
        'issued_at',
        'revoked_at'
    ];

    protected $casts = [
        'boarding_time_snapshot' => 'datetime',
        'issued_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public function segmentPassenger()
    {
        return $this->belongsTo(BookingSegmentPassenger::class, 'booking_segment_passenger_id');
    }

    public function bookingSegmentPassenger()
    {
        return $this->segmentPassenger();
    }
}
