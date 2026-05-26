<?php

namespace Database\Seeders;

use App\Models\Aircraft;
use App\Models\AircraftSeat;
use App\Models\Airline;
use App\Models\Airport;
use App\Models\Flight;
use App\Models\FlightSchedule;
use App\Models\Route;
use App\Services\Inventory\FlightSeatInventoryService;
use Illuminate\Database\Seeder;

class TestDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $airport1 = Airport::firstOrCreate(['iata_code' => 'CGK'], ['name' => 'Soekarno Hatta', 'city' => 'Jakarta', 'country' => 'Indonesia', 'timezone' => 'Asia/Jakarta']);
        $airport2 = Airport::firstOrCreate(['iata_code' => 'DPS'], ['name' => 'Ngurah Rai', 'city' => 'Bali', 'country' => 'Indonesia', 'timezone' => 'Asia/Makassar']);
        $airline = Airline::firstOrCreate(['code' => 'GA'], ['name' => 'Garuda Indonesia']);
        $aircraft = Aircraft::firstOrCreate(['model' => 'Boeing 737'], ['airline_id' => $airline->id, 'capacity' => 100, 'seat_layout' => '3-3']);
        $route = Route::firstOrCreate(['origin_airport_id' => $airport1->id, 'destination_airport_id' => $airport2->id], ['estimated_duration' => 120]);
        $flight = Flight::firstOrCreate(['flight_number' => 'GA-100'], ['route_id' => $route->id, 'aircraft_id' => $aircraft->id, 'airline_id' => $airline->id]);

        if (!AircraftSeat::where('aircraft_id', $flight->aircraft_id)->exists()) {
            for ($i = 1; $i <= 5; $i++) {
                foreach (['A', 'B', 'C', 'D', 'E', 'F'] as $letter) {
                    AircraftSeat::create([
                        'aircraft_id' => $flight->aircraft_id,
                        'seat_number' => $i . $letter,
                        'cabin_class' => 'economy',
                        'row_number' => $i,
                        'seat_letter' => $letter
                    ]);
                }
            }
        }

        $schedule = FlightSchedule::create([
            'flight_id' => $flight->id,
            'departure_datetime' => now()->addDays(2),
            'arrival_datetime' => now()->addDays(2)->addHours(2),
            'available_seats' => 10,
            'status' => 'scheduled'
        ]);

        \App\Models\FlightSchedulePrice::create([
            'flight_schedule_id' => $schedule->id,
            'cabin_class' => 'economy',
            'price' => 1000000,
            'quota' => 100
        ]);

        app(FlightSeatInventoryService::class)->generate($schedule);
    }
}
