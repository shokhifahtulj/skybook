<?php

namespace App\Services\Booking;

use App\Models\Schedule;

class BookingAvailabilityService
{
    public function availableSeats(Schedule $schedule): int
    {
        return max(0, (int) $schedule->kapasitas);
    }

    public function canBook(Schedule $schedule, int $requestedSeats): bool
    {
        return $requestedSeats > 0 && $requestedSeats <= $this->availableSeats($schedule);
    }

    public function reserve(Schedule $schedule, int $requestedSeats): int
    {
        // If schedule is not persisted yet (used by unit tests), operate on the object directly
        if (empty($schedule->getKey())) {
            if (! $this->canBook($schedule, $requestedSeats)) {
                throw new \InvalidArgumentException('Jumlah tiket melebihi kapasitas yang tersedia.');
            }

            $schedule->kapasitas = max(0, (int) $schedule->kapasitas - $requestedSeats);

            return $this->availableSeats($schedule);
        }

        return \DB::transaction(function () use ($schedule, $requestedSeats) {
            // Reload schedule with lock
            $locked = Schedule::where('id', $schedule->id)->lockForUpdate()->first();

            if (! $locked) {
                throw new \RuntimeException('Schedule not found for reservation.');
            }

            if (! $this->canBook($locked, $requestedSeats)) {
                throw new \InvalidArgumentException('Jumlah tiket melebihi kapasitas yang tersedia.');
            }

            // Ensure capacity never goes negative
            $new = max(0, (int) $locked->kapasitas - $requestedSeats);
            $locked->kapasitas = $new;
            $locked->save();

            return $this->availableSeats($locked->fresh());
        });
    }

    public function release(Schedule $schedule, int $requestedSeats): int
    {
        if (empty($schedule->getKey())) {
            $schedule->kapasitas = (int) $schedule->kapasitas + $requestedSeats;

            return $this->availableSeats($schedule);
        }

        return \DB::transaction(function () use ($schedule, $requestedSeats) {
            $locked = Schedule::where('id', $schedule->id)->lockForUpdate()->first();

            if (! $locked) {
                throw new \RuntimeException('Schedule not found for release.');
            }

            $locked->kapasitas = (int) $locked->kapasitas + $requestedSeats;
            $locked->save();

            return $this->availableSeats($locked->fresh());
        });
    }
}
