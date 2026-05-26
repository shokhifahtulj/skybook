<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            LoginAccountsSeeder::class,
            UserSeeder::class,
            AirportSeeder::class,
            AirlineSeeder::class,
            AircraftSeeder::class,
            RouteSeeder::class,
            FlightSeeder::class,
            ScheduleSeeder::class,
            BookingSeeder::class,
        ]);
    }
}