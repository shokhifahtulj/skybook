<?php

use App\Models\Flight;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('allows only one booking when capacity is 1 for two sequential requests', function () {
    $flight = Flight::factory()->create();
    $schedule = Schedule::factory()->create(['flight_id' => $flight->id, 'kapasitas' => 1]);

    $userA = User::factory()->create();
    $userB = User::factory()->create();

    // first user books successfully
    $this->actingAs($userA, 'sanctum');

    $res1 = $this->postJson('/api/bookings', [
        'schedule_id' => $schedule->id,
        'jumlah_tiket' => 1,
    ]);

    $res1->assertStatus(200)->assertJsonPath('success', true);

    // second user attempts to book the same last seat
    $this->actingAs($userB, 'sanctum');

    $res2 = $this->postJson('/api/bookings', [
        'schedule_id' => $schedule->id,
        'jumlah_tiket' => 1,
    ]);

    $res2->assertStatus(422)->assertJsonPath('success', false);
});
