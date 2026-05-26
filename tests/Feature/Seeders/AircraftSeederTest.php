<?php

namespace Tests\Feature\Seeders;

use App\Models\Aircraft;
use App\Models\AircraftSeat;
use App\Models\Airline;
use Database\Seeders\AircraftSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AircraftSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_aircraft_seeder_generates_180_master_seats_for_a320(): void
    {
        Airline::create([
            'code' => 'GA',
            'name' => 'Garuda Indonesia',
        ]);

        Airline::create([
            'code' => 'QG',
            'name' => 'Citilink',
        ]);

        $this->seed(AircraftSeeder::class);

        $aircraft = Aircraft::where('model', 'Airbus A320-200')->firstOrFail();

        $this->assertSame(180, $aircraft->capacity);
        $this->assertSame(180, AircraftSeat::where('aircraft_id', $aircraft->id)->count());
        $this->assertSame(180, $aircraft->seats()->count());
    }
}
