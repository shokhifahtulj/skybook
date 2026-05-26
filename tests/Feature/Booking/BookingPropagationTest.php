<?php

namespace Tests\Feature\Booking;

use App\Events\Irops\PassengerNotified;
use App\Models\BookingSegmentPassenger;
use App\Models\FlightSchedule;
use App\Models\FlightScheduleSeat;
use App\Models\User;
use App\Services\Booking\BookingService;
use App\Services\Irops\DelayManagementService;
use Database\Seeders\TestDatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class BookingPropagationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TestDatabaseSeeder::class);
    }

    public function test_admin_delay_updates_schedule_and_dispatches_passenger_notification(): void
    {
        Event::fake([PassengerNotified::class]);

        $user = User::factory()->create();
        $schedule = FlightSchedule::firstOrFail();
        $seat = FlightScheduleSeat::where('flight_schedule_id', $schedule->id)
            ->where('status', 'available')
            ->firstOrFail();

        $booking = app(BookingService::class)->createBookingDraft(
            [['flight_schedule_id' => $schedule->id, 'cabin_class' => 'economy']],
            [[
                'title' => 'Mr',
                'first_name' => 'Propagation',
                'last_name' => 'Test',
                'identity_type' => 'KTP',
                'identity_number' => '9999999999',
                'date_of_birth' => '1995-01-01',
                'nationality' => 'ID',
                'passenger_type' => 'adult',
                'seat_number' => $seat->seat_number,
                'seats' => [$schedule->id => $seat->seat_number],
            ]],
            session()->getId(),
            $user->id
        );

        $segmentPassenger = BookingSegmentPassenger::whereHas('segment', function ($query) use ($booking) {
            $query->where('booking_id', $booking->id);
        })->firstOrFail();

        $segmentPassenger->update(['operational_status' => 'ticketed']);

        $newDeparture = $schedule->departure_datetime->copy()->addMinutes(45);

        app(DelayManagementService::class)->declareDelay($schedule, $newDeparture, 45, 'manual', 'Admin delay test');

        $schedule->refresh();

        $this->assertSame('delayed', $schedule->status);
        $this->assertSame(45, $schedule->delay_minutes);
        $this->assertTrue($schedule->departure_datetime->equalTo($newDeparture));

        Event::assertDispatched(PassengerNotified::class, function ($event) use ($segmentPassenger) {
            return $event->bsp->is($segmentPassenger) && $event->type === 'DELAY';
        });
    }
}
