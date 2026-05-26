<?php

namespace Database\Seeders;

use App\Models\Flight;
use App\Models\FlightSchedule;
use App\Models\FlightSchedulePrice;
use App\Models\Schedule;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $flights = Flight::query()->get();

        if ($flights->isEmpty()) {
            $this->call(FlightSeeder::class);
            $flights = Flight::query()->get();
        }

        $baseDate = now()->startOfDay()->addDays(1);

        foreach ($flights as $index => $flight) {
            $existingSchedules = $flight->schedules()->count();
            $desiredSchedules = max(2, $existingSchedules);

            for ($offset = 0; $offset < $desiredSchedules - $existingSchedules; $offset++) {
                $departure = $baseDate->copy()->addDays($index + $offset)->setTime(8 + ($offset * 3), 0);
                $arrival = $departure->copy()->addHours(2 + $offset);

                $schedule = FlightSchedule::updateOrCreate(
                    ['flight_id' => $flight->id, 'departure_datetime' => $departure],
                    [
                        'arrival_datetime' => $arrival,
                        'status' => 'scheduled',
                        'available_seats' => 180,
                    ]
                );

                FlightSchedulePrice::updateOrCreate(
                    [
                        'flight_schedule_id' => $schedule->id,
                        'cabin_class' => 'economy',
                    ],
                    [
                        'price' => 1000000,
                        'quota' => max(1, (int) ($schedule->available_seats ?? 100)),
                    ]
                );

                Schedule::updateOrCreate(
                    [
                        'flight_id' => $flight->id,
                        'tanggal' => $departure->toDateString(),
                        'jam_berangkat' => $departure->toTimeString(),
                    ],
                    [
                        'jam_tiba' => $arrival->toTimeString(),
                        'kapasitas' => 180,
                    ]
                );
            }
        }
    }
}
