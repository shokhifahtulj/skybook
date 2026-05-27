<?php

use App\Models\Flight;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('sensitive operational endpoints require authentication', function () {
    $schedule = Schedule::factory()->create();

    $this->postJson('/api/boarding-pass/validate', [
        'uuid' => '550e8400-e29b-41d4-a716-446655440000',
        'signature' => 'invalid-signature',
    ])->assertUnauthorized();

    $this->getJson('/api/operations/'.$schedule->id.'/poll')->assertUnauthorized();
});

test('schedule management api is restricted to admin users', function () {
    $flight = Flight::factory()->create();
    $user = User::factory()->create(['role' => 'user']);

    $this->actingAs($user, 'sanctum');

    $this->postJson('/api/schedules', [
        'flight_id' => $flight->id,
        'tanggal' => now()->addDay()->toDateString(),
        'jam_berangkat' => '10:00',
        'jam_tiba' => '12:00',
        'kapasitas' => 120,
    ])->assertForbidden();
});
