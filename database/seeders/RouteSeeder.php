<?php

namespace Database\Seeders;

use App\Models\Airport;
use App\Models\Route;
use Illuminate\Database\Seeder;

class RouteSeeder extends Seeder
{
    public function run(): void
    {
        $cgk = Airport::where('iata_code', 'CGK')->first();
        $dps = Airport::where('iata_code', 'DPS')->first();
        $sub = Airport::where('iata_code', 'SUB')->first();

        if ($cgk && $dps) {
            Route::firstOrCreate([
                'origin_airport_id' => $cgk->id,
                'destination_airport_id' => $dps->id,
            ], [
                'distance' => 983,
                'estimated_duration' => 120, // minutes
            ]);
            
            Route::firstOrCreate([
                'origin_airport_id' => $dps->id,
                'destination_airport_id' => $cgk->id,
            ], [
                'distance' => 983,
                'estimated_duration' => 120,
            ]);
        }
    }
}