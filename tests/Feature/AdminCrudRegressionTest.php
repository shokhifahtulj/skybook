<?php

use App\Models\Airport;
use App\Models\User;

it('renders admin route and schedule create pages without component errors', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get(route('admin.routes.create'))
        ->assertOk();

    $this->actingAs($admin)
        ->get(route('admin.schedules.create'))
        ->assertOk();
});

it('stores routes using the schema-aligned columns', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $origin = Airport::create([
        'iata_code' => 'CGK',
        'name' => 'Soekarno-Hatta International Airport',
        'city' => 'Jakarta',
        'country' => 'Indonesia',
        'timezone' => 'Asia/Jakarta',
    ]);
    $destination = Airport::create([
        'iata_code' => 'DPS',
        'name' => 'Ngurah Rai International Airport',
        'city' => 'Denpasar',
        'country' => 'Indonesia',
        'timezone' => 'Asia/Makassar',
    ]);

    $this->actingAs($admin)
        ->post(route('admin.routes.store'), [
            'origin_airport_id' => $origin->id,
            'destination_airport_id' => $destination->id,
            'distance' => 1500,
            'estimated_duration' => 180,
        ])
        ->assertRedirect(route('admin.routes.index'));

    $this->assertDatabaseHas('routes', [
        'origin_airport_id' => $origin->id,
        'destination_airport_id' => $destination->id,
        'distance' => 1500,
        'estimated_duration' => 180,
    ]);
});
