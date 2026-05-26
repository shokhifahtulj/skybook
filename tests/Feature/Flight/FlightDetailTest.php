<?php

namespace Tests\Feature\Flight;

use App\Models\Flight;
use App\Models\User;
use Database\Seeders\TestDatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FlightDetailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TestDatabaseSeeder::class);
    }

    public function test_flight_detail_page_renders_for_a_valid_flight(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $flight = Flight::firstOrFail();

        $this->get(route('flights.show', $flight))
            ->assertOk()
            ->assertSee($flight->flight_number)
            ->assertSee($flight->route->origin->city)
            ->assertSee($flight->route->destination->city);
    }
}
