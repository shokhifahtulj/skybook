<?php

use App\Models\Airport;
use App\Models\Route;
use App\Models\User;

it('renders the route create page for admin users', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get(route('admin.routes.create'))
        ->assertOk()
        ->assertSee('Tambah Rute Baru');
});

it('stores, updates, searches, and deletes routes through the admin CRUD flow', function () {
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

    $route = Route::query()->latest('id')->firstOrFail();

    $this->actingAs($admin)
        ->put(route('admin.routes.update', $route), [
            'origin_airport_id' => $origin->id,
            'destination_airport_id' => $destination->id,
            'distance' => 1600,
            'estimated_duration' => 190,
        ])
        ->assertRedirect(route('admin.routes.index'));

    $this->assertDatabaseHas('routes', [
        'id' => $route->id,
        'distance' => 1600,
        'estimated_duration' => 190,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.routes.index', ['search' => 'Denpasar']))
        ->assertOk()
        ->assertSee('Denpasar');

    $this->actingAs($admin)
        ->delete(route('admin.routes.destroy', $route))
        ->assertRedirect(route('admin.routes.index'));

    $this->assertSoftDeleted('routes', ['id' => $route->id]);
});

it('validates route payloads and blocks non-admin users', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $regular = User::factory()->create(['role' => 'user']);

    $this->actingAs($admin)
        ->post(route('admin.routes.store'), [])
        ->assertSessionHasErrors(['origin_airport_id', 'destination_airport_id', 'distance', 'estimated_duration']);

    $this->actingAs($regular)
        ->get(route('admin.routes.index'))
        ->assertForbidden();
});
