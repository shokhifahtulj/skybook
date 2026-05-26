<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'flight_id',
        'tanggal',
        'jam_berangkat',
        'jam_tiba',
        'kapasitas',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function flight()
    {
        return $this->belongsTo(Flight::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
