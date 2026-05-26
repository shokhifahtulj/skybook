<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Schedule;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        $schedule = Schedule::factory()->create();
        $jumlah = $this->faker->numberBetween(1, min(4, max(1, $schedule->kapasitas)));

        return [
            'pnr' => 'SKY-' . strtoupper($this->faker->unique()->lexify('????')),
            'booking_status' => 'ticketed',
            'payment_status' => 'paid',
            'total_amount' => 100000 * $jumlah,
            'currency' => 'IDR',
            'booked_by' => null,
            'user_id' => null,
            'schedule_id' => $schedule->id,
            'jumlah_tiket' => $jumlah,
            'total_harga' => 100000 * $jumlah,
            'status_booking' => 'confirmed',
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Booking $booking): void {
            if ($booking->flight_id === null && $booking->schedule_id !== null) {
                $booking->flight_id = $booking->schedule()->value('flight_id');
            }

            if ($booking->user_id === null && $booking->booked_by !== null) {
                $booking->user_id = $booking->booked_by;
            }

            if ($booking->booked_by === null && $booking->user_id !== null) {
                $booking->booked_by = $booking->user_id;
            }

            if ($booking->user_id === null) {
                $booking->user_id = $booking->booked_by ?? $booking->user()->create(['name' => 'Booking User', 'email' => 'booking-' . uniqid() . '@example.com', 'password' => bcrypt('password')])->id;
            }

            if ($booking->booked_by === null) {
                $booking->booked_by = $booking->user_id;
            }

            $booking->saveQuietly();
        });
    }
}
