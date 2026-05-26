<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlightSchedulePrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'flight_schedule_id',
        'cabin_class',
        'price',
        'quota',
        'created_by',
        'updated_by'
    ];

    public function schedule()
    {
        return $this->belongsTo(FlightSchedule::class, 'flight_schedule_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
