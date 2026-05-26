<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Route extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = ['origin_airport_id', 'destination_airport_id', 'distance', 'estimated_duration'];

    public function origin()
    {
        return $this->belongsTo(Airport::class, 'origin_airport_id');
    }

    public function destination()
    {
        return $this->belongsTo(Airport::class, 'destination_airport_id');
    }

    public function flights()
    {
        return $this->hasMany(Flight::class);
    }
}