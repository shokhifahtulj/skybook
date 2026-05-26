<?php

namespace Database\Seeders;

use App\Models\Flight;
use Illuminate\Database\Seeder;

class FlightSeeder extends Seeder
{
    public function run(): void
    {
        $targetCount = 4;
        $currentCount = Flight::count();

        if ($currentCount < $targetCount) {
            Flight::factory()->count($targetCount - $currentCount)->create();
        }
    }
}