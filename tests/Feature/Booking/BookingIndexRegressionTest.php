<?php

namespace Tests\Feature\Booking;

use App\Models\Booking;
use App\Models\Flight;
use App\Models\Schedule;
use App\Models\User;
use Database\Seeders\TestDatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingIndexRegressionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TestDatabaseSeeder::class);
    }

    public function test_authenticated_user_can_view_empty_booking_history(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('bookings.index'));

        $response->assertOk()
            ->assertSee('Riwayat Booking')
            ->assertSee('Belum ada booking yang tersimpan.');
    }

    public function test_booking_index_supports_search_and_status_filters(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Booking::factory()->for($user)->create([
            'pnr' => 'SKY-REG-1001',
            'booking_status' => 'ticketed',
            'payment_status' => 'paid',
            'status_booking' => 'confirmed',
        ]);

        Booking::factory()->for($user)->create([
            'pnr' => 'SKY-REG-1002',
            'booking_status' => 'draft',
            'payment_status' => 'unpaid',
            'status_booking' => 'draft',
        ]);

        $this->get(route('bookings.index', ['search' => 'SKY-REG-1001']))
            ->assertOk()
            ->assertSee('SKY-REG-1001')
            ->assertDontSee('SKY-REG-1002');

        $this->get(route('bookings.index', ['status' => 'paid']))
            ->assertOk()
            ->assertSee('SKY-REG-1001')
            ->assertDontSee('SKY-REG-1002');
    }

    public function test_booking_index_formats_legacy_schedule_time_without_raw_datetime(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $flight = Flight::factory()->create();
        $schedule = Schedule::create([
            'flight_id' => $flight->id,
            'tanggal' => '2026-05-26',
            'jam_berangkat' => '2026-05-26 08:00:00',
            'jam_tiba' => '2026-05-26 11:00:00',
            'kapasitas' => 180,
        ]);

        Booking::factory()->create([
            'user_id' => $user->id,
            'booked_by' => $user->id,
            'schedule_id' => $schedule->id,
            'jumlah_tiket' => 2,
            'total_amount' => 400000,
            'total_harga' => 400000,
        ]);

        $this->get(route('bookings.index'))
            ->assertOk()
            ->assertSee('26 May 2026')
            ->assertSee('08:00')
            ->assertDontSee('2026-05-26 08:00:00');
    }
}
