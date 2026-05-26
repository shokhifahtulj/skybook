<?php

namespace App\Services\Inventory;

use App\Models\Aircraft;
use App\Models\AircraftSeat;
use App\Models\FlightSchedule;
use App\Models\FlightScheduleSeat;
use Illuminate\Support\Facades\DB;

class FlightSeatInventoryService
{
    public function generate(FlightSchedule $schedule): bool
    {
        $schedule->loadMissing(['flight.aircraft', 'aircraft']);

        $aircraft = $schedule->aircraft
            ?? $schedule->flight?->aircraft
            ?? $this->resolveFallbackAircraft($schedule);

        if (! $aircraft) {
            return false;
        }

        $this->ensureAircraftSeats($aircraft);

        if ($schedule->seats()->exists()) {
            $this->syncAvailabilityCount($schedule);

            return true;
        }

        $this->purgeExistingSeats($schedule);

        $masterSeats = $aircraft->fresh()->seats()
            ->orderBy('row_number')
            ->orderBy('seat_letter')
            ->get();

        if ($masterSeats->isEmpty()) {
            return false;
        }

        DB::transaction(function () use ($schedule, $masterSeats) {
            $rows = [];

            foreach ($masterSeats as $seat) {
                $rows[] = [
                    'flight_schedule_id' => $schedule->id,
                    'aircraft_seat_id' => $seat->id,
                    'seat_number' => $seat->seat_number,
                    'cabin_class' => $seat->cabin_class,
                    'is_window' => $seat->is_window,
                    'is_aisle' => $seat->is_aisle,
                    'is_exit_row' => $seat->is_exit_row,
                    'status' => 'available',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            FlightScheduleSeat::insert($rows);
        });

        $this->syncAvailabilityCount($schedule);

        return true;
    }

    public function regenerate(FlightSchedule $schedule): bool
    {
        $this->purgeExistingSeats($schedule);

        return $this->generate($schedule);
    }

    private function purgeExistingSeats(FlightSchedule $schedule): void
    {
        FlightScheduleSeat::withTrashed()
            ->where('flight_schedule_id', $schedule->id)
            ->forceDelete();
    }

    private function resolveFallbackAircraft(FlightSchedule $schedule): ?Aircraft
    {
        $airlineId = $schedule->flight?->airline_id;

        $a320 = Aircraft::query()
            ->where('model', 'Airbus A320-200')
            ->when($airlineId, fn ($query) => $query->where('airline_id', $airlineId))
            ->first();

        if ($a320) {
            return $a320;
        }

        $candidate = Aircraft::query()
            ->when($airlineId, fn ($query) => $query->where('airline_id', $airlineId))
            ->orderByDesc('capacity')
            ->first();

        if ($candidate) {
            return $candidate;
        }

        $attributes = [
            'model' => 'Airbus A320-200',
            'capacity' => 180,
            'seat_layout' => '3-3',
        ];

        if ($airlineId) {
            $attributes['airline_id'] = $airlineId;
        }

        return Aircraft::firstOrCreate($attributes, $attributes);
    }

    private function ensureAircraftSeats(Aircraft $aircraft): void
    {
        if ($aircraft->seats()->count() > 0) {
            return;
        }

        $rows = [];

        for ($row = 1; $row <= 30; $row++) {

            foreach (['A', 'B', 'C', 'D', 'E', 'F'] as $letter) {

                $rows[] = [

                    'aircraft_id' => $aircraft->id,

                    'row_number' => $row,
                    'seat_letter' => $letter,

                    'seat_number' => $row . $letter,

                    'cabin_class' => 'economy',

                    'is_window' => in_array($letter, ['A', 'F']),
                    'is_aisle' => in_array($letter, ['C', 'D']),
                    'is_exit_row' => false,

                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
        }

        AircraftSeat::insert($rows);
    }

    private function syncAvailabilityCount(
        FlightSchedule $schedule
    ): void {

        $available = FlightScheduleSeat::where(
            'flight_schedule_id',
            $schedule->id
        )
        ->where('status', 'available')
        ->count();

        $schedule->update([
            'available_seats' => $available
        ]);
    }
}