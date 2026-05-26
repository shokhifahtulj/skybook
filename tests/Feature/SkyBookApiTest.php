<?php

use App\Models\Flight;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('registers a new user via api', function () {
    $response = $this->postJson('/api/register', [
        'name' => 'Api User',
        'email' => 'apiuser@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => ['token', 'user'],
        ]);
});

it('logs in a user via api', function () {
    $user = User::factory()->create(['email' => 'login@example.com', 'password' => bcrypt('password')]);

    $response = $this->postJson('/api/login', [
        'email' => 'login@example.com',
        'password' => 'password',
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('success', true)
        ->assertJsonStructure([
            'data' => ['token', 'user'],
        ]);
});

it('allows admin to create flight through api', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin, 'sanctum');

    $response = $this->postJson('/api/flights', [
        'kode_penerbangan' => 'TEST123',
        'maskapai' => 'SkyBook Air',
        'asal' => 'Jakarta',
        'tujuan' => 'Bali',
        'harga' => 1200000,
    ]);

    $response->assertStatus(200)
        ->assertJson(['success' => true])
        ->assertJsonPath('data.kode_penerbangan', 'TEST123');
});

it('creates a booking and reduces schedule capacity', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $flight = Flight::factory()->create();
    $schedule = Schedule::factory()->create(['flight_id' => $flight->id, 'kapasitas' => 5]);
    $user = User::factory()->create();

    $this->actingAs($user, 'sanctum');

    $response = $this->postJson('/api/bookings', [
        'schedule_id' => $schedule->id,
        'jumlah_tiket' => 3,
    ]);

    $response->assertStatus(200)
        ->assertJson(['success' => true])
        ->assertJsonPath('data.jumlah_tiket', 3);

    expect($schedule->fresh()->kapasitas)->toBe(2);
});
