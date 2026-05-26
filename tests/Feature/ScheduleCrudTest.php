<?php

use App\Models\Flight;
use App\Models\FlightSchedule;
use App\Models\User;

it('renders the schedule create page and allows an admin to delete a schedule', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $flight = Flight::factory()->create();

    $schedule = FlightSchedule::create([
        'flight_id' => $flight->id,
        'departure_datetime' => now()->addDay()->setTime(8, 0),
        'arrival_datetime' => now()->addDay()->setTime(10, 0),
        'status' => 'scheduled',
        'available_seats' => 100,
        'created_by' => $admin->id,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.schedules.create'))
        ->assertOk()
        ->assertSee('Tambah Jadwal');

    $this->actingAs($admin)
        ->delete(route('admin.schedules.destroy', $schedule))
        ->assertRedirect(route('admin.schedules.index'));

    $this->assertSoftDeleted('flight_schedules', ['id' => $schedule->id]);
});

it('blocks non-admin users from accessing schedule administration', function () {
    $regular = User::factory()->create(['role' => 'user']);

    $this->actingAs($regular)
        ->get(route('admin.schedules.index'))
        ->assertForbidden();
});
