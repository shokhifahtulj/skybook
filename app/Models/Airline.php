<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Airline extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = ['code', 'name', 'logo', 'status'];

    public function aircrafts()
    {
        return $this->hasMany(Aircraft::class);
    }

    public function flights()
    {
        return $this->hasMany(Flight::class);
    }
}