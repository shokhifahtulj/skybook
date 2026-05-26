<?php

namespace Tests\Feature\Booking;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Schedule;
use App\Models\User;
use Database\Seeders\TestDatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BookingRelationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TestDatabaseSeeder::class);
    }

    public function test_booking_schema_exposes_expected_columns_and_foreign_keys(): void
    {
        $this->assertTrue(Schema::hasColumn('bookings', 'user_id'));
        $this->assertTrue(Schema::hasColumn('bookings', 'schedule_id'));
        $this->assertTrue(Schema::hasColumn('bookings', 'status_booking'));
        $this->assertTrue(Schema::hasColumn('bookings', 'jumlah_tiket'));
        $this->assertTrue(Schema::hasColumn('bookings', 'total_harga'));

        $foreignKeys = DB::select('PRAGMA foreign_key_list(bookings)');

        $this->assertTrue(collect($foreignKeys)->contains(fn ($foreignKey) => ($foreignKey->from ?? null) === 'user_id'));
        $this->assertTrue(collect($foreignKeys)->contains(fn ($foreignKey) => ($foreignKey->from ?? null) === 'schedule_id'));
    }

    public function test_booking_relation_graph_is_available_for_user_schedule_and_payment(): void
    {
        $user = User::factory()->create();
        $schedule = Schedule::factory()->create();

        $booking = Booking::factory()->create([
            'booked_by' => $user->id,
            'user_id' => $user->id,
            'schedule_id' => $schedule->id,
            'booking_status' => 'ticketed',
            'payment_status' => 'paid',
            'status_booking' => 'confirmed',
        ]);

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'payment_reference' => 'PAY-REL-1',
            'gateway' => 'manual',
            'amount' => 100000,
            'currency' => 'IDR',
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $this->assertTrue($booking->user->is($user));
        $this->assertTrue($booking->schedule->is($schedule));
        $this->assertTrue($booking->payment->is($payment));
    }
}
