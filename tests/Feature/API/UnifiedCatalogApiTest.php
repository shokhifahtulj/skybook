<?php

use App\Models\Booking;
use App\Models\Flight;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('supports search, pagination, and sort on flights', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin, 'sanctum');

    Flight::factory()->create(['flight_number' => 'SKA111']);
    Flight::factory()->create(['flight_number' => 'SKB222']);
    Flight::factory()->create(['flight_number' => 'TEST999']);

    $response = $this->getJson('/api/flights?search=SKB&per_page=1&page=1&sort=flight_number&direction=desc');

    $response->assertOk()
        ->assertJsonPath('meta.total', 1)
        ->assertJsonPath('meta.per_page', 1)
        ->assertJsonPath('meta.current_page', 1)
        ->assertJsonPath('data.0.flight_number', 'SKB222');
});

it('supports search filtering on bookings for the authenticated user', function () {
    $user = User::factory()->create();
    $this->actingAs($user, 'sanctum');

    Booking::factory()->for($user)->create([
        'pnr' => 'ZZZ999',
        'booking_status' => 'confirmed',
        'payment_status' => 'paid',
    ]);

    $response = $this->getJson('/api/bookings?search=NOPE');

    $response->assertOk()
        ->assertJsonPath('meta.total', 0)
        ->assertJsonPath('data', []);
});

it('supports pagination and sorting for authenticated booking queries', function () {
    $user = User::factory()->create();
    $this->actingAs($user, 'sanctum');

    Booking::factory()->for($user)->count(2)->create([
        'booking_status' => 'ticketed',
        'payment_status' => 'paid',
    ]);

    $response = $this->getJson('/api/bookings?per_page=1&page=1&sort=created_at&direction=desc');

    $response->assertOk()
        ->assertJsonPath('meta.total', 2)
        ->assertJsonPath('meta.per_page', 1)
        ->assertJsonPath('meta.current_page', 1)
        ->assertJsonCount(1, 'data');
});

it('filters bookings by date range for the authenticated user', function () {
    $user = User::factory()->create();
    $this->actingAs($user, 'sanctum');

    $targetSchedule = Schedule::factory()->create(['tanggal' => '2026-06-15']);
    $otherSchedule = Schedule::factory()->create(['tanggal' => '2026-06-16']);

    Booking::factory()->for($user)->create([
        'pnr' => 'DATE-ONE',
        'schedule_id' => $targetSchedule->id,
    ]);

    Booking::factory()->for($user)->create([
        'pnr' => 'DATE-TWO',
        'schedule_id' => $otherSchedule->id,
    ]);

    $response = $this->getJson('/api/bookings?date_from=2026-06-15&date_to=2026-06-15');

    $response->assertOk()
        ->assertJsonPath('meta.total', 1)
        ->assertJsonPath('data.0.pnr', 'DATE-ONE');
});

it('isolates bookings across users', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $this->actingAs($owner, 'sanctum');

    Booking::factory()->for($otherUser)->create([
        'pnr' => 'OTHER-BOOKING',
    ]);

    $response = $this->getJson('/api/bookings');

    $response->assertOk()
        ->assertJsonPath('meta.total', 0)
        ->assertJsonPath('data', []);
});

it('ignores invalid filters and keeps the api response stable', function () {
    $user = User::factory()->create();
    $this->actingAs($user, 'sanctum');

    Booking::factory()->for($user)->create([
        'pnr' => 'SAFE-BOOKING',
    ]);

    $response = $this->getJson('/api/bookings?per_page=0&sort=not-a-column&direction=up&date_from=bogus&date_to=also-bad');

    $response->assertOk()
        ->assertJsonStructure(['success', 'message', 'data', 'meta'])
        ->assertJsonPath('meta.per_page', 1)
        ->assertJsonPath('meta.total', 1);
});

it('rejects unauthenticated access to the bookings index endpoint', function () {
    $this->getJson('/api/bookings')
        ->assertStatus(401);
});
