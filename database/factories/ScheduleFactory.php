<?php

namespace Database\Factories;

use App\Models\Flight;
use App\Models\Schedule;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScheduleFactory extends Factory
{
    protected $model = Schedule::class;

    public function definition(): array
    {
        $flight = Flight::inRandomOrder()->first() ?? Flight::factory()->create();
        $start = $this->faker->dateTimeBetween('+1 days', '+45 days');
        $durationHours = $this->faker->numberBetween(1, 4);
        $end = (clone $start)->modify("+{$durationHours} hours");

        return [
            'flight_id' => $flight->id,
            'tanggal' => $start->format('Y-m-d'),
            'jam_berangkat' => $start->format('H:i:s'),
            'jam_tiba' => $end->format('H:i:s'),
            'kapasitas' => $this->faker->numberBetween(50, 180),
        ];
    }
}
