<?php

namespace Database\Factories;

use App\Models\Airline;
use App\Models\Airport;
use App\Models\Flight;
use App\Models\Route;
use Illuminate\Database\Eloquent\Factories\Factory;

class FlightFactory extends Factory
{
    protected $model = Flight::class;

    public function definition(): array
    {
        $airline = Airline::firstOrCreate([
            'code' => 'SKB',
        ], [
            'name' => 'SkyBook Air',
        ]);

        $origin = Airport::firstOrCreate([
            'iata_code' => 'CGK',
        ], [
            'name' => 'Soekarno-Hatta',
            'city' => 'Jakarta',
            'country' => 'Indonesia',
            'timezone' => 'Asia/Jakarta',
        ]);

        $destination = Airport::firstOrCreate([
            'iata_code' => 'DPS',
        ], [
            'name' => 'Ngurah Rai',
            'city' => 'Denpasar',
            'country' => 'Indonesia',
            'timezone' => 'Asia/Makassar',
        ]);

        $route = Route::firstOrCreate([
            'origin_airport_id' => $origin->id,
            'destination_airport_id' => $destination->id,
        ], [
            'estimated_duration' => 120,
        ]);

        return [
            'flight_number' => strtoupper($this->faker->bothify('??###')),
            'airline_id' => $airline->id,
            'route_id' => $route->id,
            'aircraft_id' => null,
        ];
    }
}
