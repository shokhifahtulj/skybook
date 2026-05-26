<?php

namespace Database\Seeders;

use App\Models\Aircraft;
use App\Models\Airline;
use App\Services\SeatMapGeneratorService;
use Illuminate\Database\Seeder;

class AircraftSeeder extends Seeder
{
    public function run(): void
    {
        $garuda = Airline::where('code', 'GA')->first();
        $citilink = Airline::where('code', 'QG')->first();

        if ($garuda) {
            Aircraft::firstOrCreate(['airline_id' => $garuda->id, 'model' => 'Boeing 737-800'], ['capacity' => 162, 'seat_layout' => '3-3']);
            Aircraft::firstOrCreate(['airline_id' => $garuda->id, 'model' => 'Airbus A330-300'], ['capacity' => 287, 'seat_layout' => '2-4-2']);
        }

        if ($citilink) {
            $a320 = Aircraft::firstOrCreate(['airline_id' => $citilink->id, 'model' => 'Airbus A320-200'], ['capacity' => 180, 'seat_layout' => '3-3']);

            if ($a320->seats()->count() !== (int) $a320->capacity) {
                app(SeatMapGeneratorService::class)->generateForAircraft($a320, 'A320');
            }
        }
    }
}