<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('menolak akses api tanpa autentikasi', function () {
    $this->getJson('/api/flights')
        ->assertStatus(401);
});

it('menolak user biasa mengakses endpoint flight admin', function () {
    $user = User::factory()->create(['role' => 'user']);

    $this->actingAs($user, 'sanctum');

    $this->getJson('/api/flights')
        ->assertStatus(403);
});

it('menolak admin mengakses endpoint booking khusus user', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin, 'sanctum');

    $this->postJson('/api/bookings', [
        'schedule_id' => 1,
        'jumlah_tiket' => 1,
    ])->assertStatus(403);
});

it('mengizinkan user terautentikasi mengakses index booking', function () {
    $user = User::factory()->create(['role' => 'user']);

    $this->actingAs($user, 'sanctum');

    $this->getJson('/api/bookings')
        ->assertStatus(200)
        ->assertJson(['success' => true]);
});
