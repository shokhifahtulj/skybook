<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'booking_id',
        'payment_reference',
        'gateway',
        'amount',
        'currency',
        'status',
        'paid_at',
        'raw_payload'
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'raw_payload' => 'array',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
