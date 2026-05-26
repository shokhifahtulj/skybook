<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AircraftSeat extends Model
{
    use HasFactory;

    protected $fillable = [
        'aircraft_id',
        'seat_number',
        'cabin_class',
        'row_number',
        'seat_letter',
        'is_window',
        'is_aisle',
        'is_exit_row'
    ];

    protected $casts = [
        'is_window' => 'boolean',
        'is_aisle' => 'boolean',
        'is_exit_row' => 'boolean',
    ];

    public function aircraft()
    {
        return $this->belongsTo(Aircraft::class);
    }
}
