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
        if (! $this->canBook($schedule, $requestedSeats)) {
            throw new \InvalidArgumentException('Jumlah tiket melebihi kapasitas yang tersedia.');
        }

        $schedule->decrement('kapasitas', $requestedSeats);

        return $this->availableSeats($schedule->fresh());
    }

    public function release(Schedule $schedule, int $requestedSeats): int
    {
        $schedule->increment('kapasitas', $requestedSeats);

        return $this->availableSeats($schedule->fresh());
    }
}
