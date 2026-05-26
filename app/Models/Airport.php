<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Airport extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = ['iata_code', 'name', 'city', 'country', 'timezone'];

    public function routesAsOrigin()
    {
        return $this->hasMany(Route::class, 'origin_airport_id');
    }

    public function routesAsDestination()
    {
        return $this->hasMany(Route::class, 'destination_airport_id');
    }
}