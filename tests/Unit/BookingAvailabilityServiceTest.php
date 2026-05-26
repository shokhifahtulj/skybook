<?php

use App\Models\Schedule;
use App\Services\Booking\BookingAvailabilityService;

it('menghitung seat availability dengan benar', function () {
    $schedule = new Schedule(['kapasitas' => 12]);

    $service = new BookingAvailabilityService();

    expect($service->availableSeats($schedule))->toBe(12)
        ->and($service->canBook($schedule, 5))->toBeTrue()
        ->and($service->canBook($schedule, 13))->toBeFalse();
});

it('mencegah booking ketika kapasitas melebihi batas', function () {
    $schedule = new Schedule(['kapasitas' => 2]);

    $service = new BookingAvailabilityService();

    expect(fn () => $service->reserve($schedule, 3))->toThrow(InvalidArgumentException::class);
});
