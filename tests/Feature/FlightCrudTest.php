<?php

use App\Models\Airline;
use App\Models\Airport;
use App\Models\Flight;
use App\Models\Route;
use App\Models\User;

it('renders the flight create page for admin users', function () {
    $admin = User::factory()->create(['role' => 'admin']);

    $this->actingAs($admin)
        ->get(route('admin.flights.create'))
        ->assertOk()
        ->assertSee('Tambah Penerbangan Baru');
});

it('stores, updates, searches, and deletes flights through the admin CRUD flow', function () {
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

    $route = Route::create([
        'origin_airport_id' => $origin->id,
        'destination_airport_id' => $destination->id,
        'distance' => 1500,
        'estimated_duration' => 180,
    ]);

    $airline = Airline::create([
        'code' => 'SKB',
        'name' => 'SkyBook Air',
    ]);

    $flight = Flight::factory()->create([
        'flight_number' => 'SKB-100',
        'airline_id' => $airline->id,
        'route_id' => $route->id,
    ]);

    $this->actingAs($admin)
        ->post(route('admin.flights.store'), [
            'flight_number' => 'SKB-101',
            'airline_id' => $airline->id,
            'route_id' => $route->id,
            'aircraft_id' => null,
        ])
        ->assertRedirect(route('admin.flights.index'));

    $storedFlight = Flight::query()->where('flight_number', 'SKB-101')->firstOrFail();

    $this->actingAs($admin)
        ->put(route('admin.flights.update', $storedFlight), [
            'flight_number' => 'SKB-202',
            'airline_id' => $airline->id,
            'route_id' => $route->id,
            'aircraft_id' => null,
        ])
        ->assertRedirect(route('admin.flights.index'));

    $this->assertDatabaseHas('flights', [
        'id' => $storedFlight->id,
        'flight_number' => 'SKB-202',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.flights.index', ['search' => 'SKB-202']))
        ->assertOk()
        ->assertSee('SKB-202');

    $this->actingAs($admin)
        ->delete(route('admin.flights.destroy', $storedFlight))
        ->assertRedirect(route('admin.flights.index'));

    $this->assertSoftDeleted('flights', ['id' => $storedFlight->id]);

    $this->actingAs($admin)
        ->delete(route('admin.flights.destroy', $flight))
        ->assertRedirect(route('admin.flights.index'));
});

it('validates flight payloads and blocks non-admin users', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $regular = User::factory()->create(['role' => 'user']);

    $this->actingAs($admin)
        ->post(route('admin.flights.store'), [])
        ->assertSessionHasErrors(['flight_number', 'airline_id', 'route_id']);

    $this->actingAs($regular)
        ->get(route('admin.flights.index'))
        ->assertForbidden();
});
