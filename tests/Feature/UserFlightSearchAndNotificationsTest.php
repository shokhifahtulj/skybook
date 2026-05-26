<?php

use App\Models\Airline;
use App\Models\Airport;
use App\Models\Flight;
use App\Models\FlightSchedule;
use App\Models\Notification;
use App\Models\Route;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('filters search flights by origin, destination, departure date, and airline', function () {
    $user = User::factory()->create();

    $jakarta = Airport::query()->firstOrCreate([
        'iata_code' => 'CGK',
    ], [
        'name' => 'Soekarno-Hatta',
        'city' => 'Jakarta',
        'country' => 'Indonesia',
        'timezone' => 'Asia/Jakarta',
    ]);

    $denpasar = Airport::query()->firstOrCreate([
        'iata_code' => 'DPS',
    ], [
        'name' => 'Ngurah Rai',
        'city' => 'Denpasar',
        'country' => 'Indonesia',
        'timezone' => 'Asia/Makassar',
    ]);

    $surabaya = Airport::query()->firstOrCreate([
        'iata_code' => 'SUB',
    ], [
        'name' => 'Juanda',
        'city' => 'Surabaya',
        'country' => 'Indonesia',
        'timezone' => 'Asia/Jakarta',
    ]);

    $skybook = Airline::query()->firstOrCreate([
        'code' => 'SKB',
    ], [
        'name' => 'SkyBook Air',
    ]);

    $lion = Airline::query()->firstOrCreate([
        'code' => 'LNI',
    ], [
        'name' => 'Lion Air',
    ]);

    $routeOne = Route::query()->firstOrCreate([
        'origin_airport_id' => $jakarta->id,
        'destination_airport_id' => $denpasar->id,
    ], [
        'estimated_duration' => 120,
    ]);

    $routeTwo = Route::query()->firstOrCreate([
        'origin_airport_id' => $surabaya->id,
        'destination_airport_id' => $denpasar->id,
    ], [
        'estimated_duration' => 145,
    ]);

    $matchFlight = Flight::query()->create([
        'flight_number' => 'SKB101',
        'airline_id' => $skybook->id,
        'route_id' => $routeOne->id,
    ]);

    $otherFlight = Flight::query()->create([
        'flight_number' => 'LNI909',
        'airline_id' => $lion->id,
        'route_id' => $routeTwo->id,
    ]);

    FlightSchedule::query()->create([
        'flight_id' => $matchFlight->id,
        'departure_datetime' => now()->addDay()->setTime(8, 0),
        'arrival_datetime' => now()->addDay()->setTime(11, 0),
        'status' => 'scheduled',
        'available_seats' => 24,
    ]);

    FlightSchedule::query()->create([
        'flight_id' => $otherFlight->id,
        'departure_datetime' => now()->addDay()->setTime(10, 0),
        'arrival_datetime' => now()->addDay()->setTime(13, 0),
        'status' => 'scheduled',
        'available_seats' => 18,
    ]);

    $response = $this->actingAs($user)->get(route('flights.index', [
        'origin' => 'Jakarta',
        'destination' => 'Denpasar',
        'departure_date' => now()->addDay()->toDateString(),
        'airline' => 'SkyBook Air',
    ]));

    $response->assertOk();
    $response->assertSee('SKB101');
    $response->assertSee('Book Now');
    $response->assertDontSee('LNI909');
});

it('shows no flights found when the search returns no results', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('flights.index', [
        'origin' => 'Bali',
        'destination' => 'Tokyo',
        'departure_date' => now()->addDay()->toDateString(),
        'airline' => 'No Airline',
    ]));

    $response->assertOk();
    $response->assertSee('No flights found');
});

it('shows notifications and marks a notification as read', function () {
    $user = User::factory()->create();

    $notification = Notification::query()->create([
        'user_id' => $user->id,
        'title' => 'Booking Created',
        'message' => 'Your booking has been created successfully.',
        'type' => 'info',
        'is_read' => false,
    ]);

    $response = $this->actingAs($user)->get(route('notifications.index'));

    $response->assertOk();
    $response->assertSee('Booking Created');
    $response->assertSee('1 unread');

    $markResponse = $this->actingAs($user)->post(route('notifications.read', $notification));

    $markResponse->assertRedirect();
    $this->assertDatabaseHas('notifications', [
        'id' => $notification->id,
        'is_read' => true,
    ]);
});
