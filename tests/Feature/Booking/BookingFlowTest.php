<?php

namespace Tests\Feature\Booking;

use App\Models\FlightSchedule;
use Database\Seeders\TestDatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TestDatabaseSeeder::class);
    }

    public function test_user_can_create_booking_draft_and_lock_seats()
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $schedule = FlightSchedule::first();

        $response = $this->postJson('/api/bookings/create-draft', [
            'segments' => [
                [
                    'flight_schedule_id' => $schedule->id,
                    'cabin_class' => 'economy'
                ]
            ],
            'passengers' => [
                [
                    'title' => 'Mr',
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'date_of_birth' => '1990-01-01',
                    'passenger_type' => 'adult',
                    'identity_type' => 'KTP',
                    'identity_number' => '1234567890',
                    'seats' => [
                        $schedule->id => '1A'
                    ]
                ]
            ]
        ]);


        $response->assertStatus(201);
        $this->assertDatabaseHas('bookings', [
            'booking_status' => 'draft',
            'payment_status' => 'unpaid'
        ]);

        $this->assertDatabaseHas('flight_schedule_seats', [
            'flight_schedule_id' => $schedule->id,
            'seat_number' => '1A',
            'status' => 'locked'
        ]);
    }

    public function test_user_can_create_booking_draft_when_schedule_has_no_economy_price()
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $schedule = FlightSchedule::first();
        $schedule->prices()->delete();

        $response = $this->postJson('/api/bookings/create-draft', [
            'segments' => [
                [
                    'flight_schedule_id' => $schedule->id,
                    'cabin_class' => 'economy'
                ]
            ],
            'passengers' => [
                [
                    'title' => 'Mr',
                    'first_name' => 'Jane',
                    'last_name' => 'Smith',
                    'date_of_birth' => '1992-02-02',
                    'passenger_type' => 'adult',
                    'identity_type' => 'KTP',
                    'identity_number' => '1234567891',
                    'seats' => [
                        $schedule->id => '1B'
                    ]
                ]
            ]
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('flight_schedule_prices', [
            'flight_schedule_id' => $schedule->id,
            'cabin_class' => 'economy',
            'price' => 1000000,
        ]);
    }
}
