<?php

use App\Models\Flight;
use App\Models\FlightSchedule;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create([
        'role' => 'admin',
    ]);
});

test('admin dashboard renders successfully', function () {
    $this->actingAs($this->admin)
        ->get('/admin/dashboard')
        ->assertOk()
        ->assertSee('OTA Dashboard');
});

test('admin command center renders successfully for a schedule', function () {
    $flight = Flight::factory()->create();
    $schedule = FlightSchedule::create([
        'flight_id' => $flight->id,
        'departure_datetime' => now()->addDay(),
        'arrival_datetime' => now()->addDay()->addHours(2),
        'status' => 'scheduled',
        'available_seats' => 100,
    ]);

    $this->actingAs($this->admin)
        ->get('/admin/operations/' . $schedule->id)
        ->assertOk();
});

test('admin operations gate management page renders successfully', function () {
    $this->actingAs($this->admin)
        ->get('/admin/operations/gates')
        ->assertOk();
});

test('admin operations simulation page renders successfully', function () {
    $this->actingAs($this->admin)
        ->get('/admin/operations/simulation')
        ->assertOk();
});

test('admin operations reaccommodation page renders successfully', function () {
    $this->actingAs($this->admin)
        ->get('/admin/operations/reaccommodation')
        ->assertOk();
});
