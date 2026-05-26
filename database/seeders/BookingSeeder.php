<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'user@demo.com')->first();
        $schedule = Schedule::query()->orderByDesc('tanggal')->orderByDesc('jam_berangkat')->first();

        if (! $user || ! $schedule) {
            return;
        }

        Booking::updateOrCreate(
            ['user_id' => $user->id, 'schedule_id' => $schedule->id],
            [
                'pnr' => strtoupper(substr(md5($user->id . $schedule->id), 0, 6)),
                'booking_status' => 'ticketed',
                'payment_status' => 'paid',
                'total_amount' => 400000,
                'currency' => 'IDR',
                'booked_by' => $user->id,
                'flight_id' => $schedule->flight_id,
                'jumlah_tiket' => 2,
                'total_harga' => 400000,
                'status_booking' => 'confirmed',
                'expires_at' => now()->addDays(3),
            ]
        );
    }
}
