<?php

namespace Tests\Feature\Booking;

use App\Models\Booking;
use App\Models\BookingSegmentPassenger;
use App\Models\FlightSchedule;
use App\Models\FlightScheduleSeat;
use App\Models\Payment;
use App\Models\Ticket;
use App\Models\User;
use Database\Seeders\TestDatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingWizardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TestDatabaseSeeder::class);
    }

    public function test_user_can_complete_web_booking_wizard_and_receive_ticketed_booking(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $schedule = FlightSchedule::firstOrFail();
        $initialAvailableSeats = (int) $schedule->fresh()->available_seats;
        $seat = FlightScheduleSeat::where('flight_schedule_id', $schedule->id)
            ->where('status', 'available')
            ->firstOrFail();

        $this->post(route('bookings.create.select'), [
            'schedule_id' => $schedule->id,
        ])->assertRedirect(route('bookings.passengers'));

        $this->post(route('bookings.passengers.save'), [
            'jumlah_tiket' => 1,
            'passengers' => [[
                'first_name' => 'John',
                'last_name' => 'Doe',
                'identity_type' => 'KTP',
                'identity_number' => '1234567890',
                'date_of_birth' => '1990-01-01',
                'nationality' => 'ID',
                'seat_number' => $seat->seat_number,
            ]],
        ])->assertRedirect(route('bookings.confirm'));

        $this->post(route('bookings.store'), [
            'schedule_id' => $schedule->id,
            'jumlah_tiket' => 1,
            'wizard' => true,
            'passengers' => [[
                'first_name' => 'John',
                'last_name' => 'Doe',
                'identity_type' => 'KTP',
                'identity_number' => '1234567890',
                'date_of_birth' => '1990-01-01',
                'nationality' => 'ID',
                'seat_number' => $seat->seat_number,
            ]],
        ])->assertRedirect(route('bookings.index'));

        $booking = Booking::latest('created_at')->firstOrFail();

        $this->assertSame('ticketed', $booking->booking_status);
        $this->assertSame('paid', $booking->payment_status);
        $this->assertTrue(Payment::where('booking_id', $booking->id)->where('status', 'paid')->exists());
        $this->assertTrue(Ticket::whereHas('segmentPassenger.segment', function ($query) use ($booking) {
            $query->where('booking_id', $booking->id);
        })->exists());

        $this->assertSame($initialAvailableSeats - 1, (int) $schedule->fresh()->available_seats);
        $this->assertDatabaseHas('flight_schedule_seats', [
            'id' => $seat->id,
            'status' => 'booked',
            'booking_id' => $booking->id,
        ]);

        $this->assertTrue(BookingSegmentPassenger::whereHas('segment', function ($query) use ($booking) {
            $query->where('booking_id', $booking->id);
        })->exists());
    }

    public function test_cancelling_a_paid_booking_releases_the_reserved_seat(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $schedule = FlightSchedule::firstOrFail();
        $seat = FlightScheduleSeat::where('flight_schedule_id', $schedule->id)
            ->where('status', 'available')
            ->firstOrFail();

        $this->post(route('bookings.create.select'), [
            'schedule_id' => $schedule->id,
        ])->assertRedirect(route('bookings.passengers'));

        $this->post(route('bookings.passengers.save'), [
            'jumlah_tiket' => 1,
            'passengers' => [[
                'first_name' => 'Jane',
                'last_name' => 'Roe',
                'identity_type' => 'KTP',
                'identity_number' => '2233445566',
                'date_of_birth' => '1995-05-05',
                'nationality' => 'ID',
                'seat_number' => $seat->seat_number,
            ]],
        ])->assertRedirect(route('bookings.confirm'));

        $this->post(route('bookings.store'), [
            'schedule_id' => $schedule->id,
            'jumlah_tiket' => 1,
            'wizard' => true,
            'passengers' => [[
                'first_name' => 'Jane',
                'last_name' => 'Roe',
                'identity_type' => 'KTP',
                'identity_number' => '2233445566',
                'date_of_birth' => '1995-05-05',
                'nationality' => 'ID',
                'seat_number' => $seat->seat_number,
            ]],
        ])->assertRedirect(route('bookings.index'));

        $booking = Booking::latest('created_at')->firstOrFail();
        $initialAvailableSeats = (int) $schedule->fresh()->available_seats;

        session([
            'booking_wizard.schedule_id' => $schedule->id,
            'booking_wizard.jumlah_tiket' => 1,
            'booking_wizard.passengers' => [[
                'first_name' => 'Jane',
                'last_name' => 'Roe',
                'seat_number' => $seat->seat_number,
            ]],
        ]);

        $this->delete(route('bookings.destroy', $booking))
            ->assertRedirect();

        $this->assertSoftDeleted('bookings', [
            'id' => $booking->id,
        ]);

        $this->assertSame($initialAvailableSeats, (int) $schedule->fresh()->available_seats);
        $this->assertDatabaseHas('flight_schedule_seats', [
            'id' => $seat->id,
            'status' => 'available',
            'booking_id' => null,
        ]);
        $this->assertFalse(session()->has('booking_wizard.schedule_id'));
        $this->assertFalse(session()->has('booking_wizard.jumlah_tiket'));
        $this->assertFalse(session()->has('booking_wizard.passengers'));
    }

    public function test_passenger_step_displays_flight_context_and_seat_summary(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $schedule = FlightSchedule::firstOrFail();
        $seat = FlightScheduleSeat::where('flight_schedule_id', $schedule->id)
            ->where('status', 'available')
            ->firstOrFail();

        $this->post(route('bookings.create.select'), [
            'schedule_id' => $schedule->id,
        ])->assertRedirect(route('bookings.passengers'));

        $this->get(route('bookings.passengers'))
            ->assertOk()
            ->assertSee('Flight summary')
            ->assertSee($schedule->flight->flight_number)
            ->assertSee('Traveler details')
            ->assertSee('Available seats')
            ->assertSee('Selected seats')
            ->assertSee('Continue');
    }

    public function test_passenger_step_generates_missing_runtime_seats_before_rendering_grid(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $schedule = FlightSchedule::firstOrFail();
        $schedule->seats()->delete();

        $this->post(route('bookings.create.select'), [
            'schedule_id' => $schedule->id,
        ])->assertRedirect(route('bookings.passengers'));

        $this->get(route('bookings.passengers'))
            ->assertOk()
            ->assertSee('data-seat-button', false)
            ->assertSee('Selected: <span id="seat-selected-0"', false);

        $this->assertGreaterThan(0, $schedule->fresh()->seats()->count());
    }

    public function test_passenger_step_generates_runtime_seats_when_flight_has_no_assigned_aircraft(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $airline = \App\Models\Airline::firstOrFail();
        $aircraft = \App\Models\Aircraft::create([
            'airline_id' => $airline->id,
            'model' => 'Airbus A320-200',
            'capacity' => 180,
            'seat_layout' => '3-3',
        ]);

        app(\App\Services\SeatMapGeneratorService::class)->generateForAircraft($aircraft, 'A320');

        $flight = \App\Models\Flight::factory()->create([
            'airline_id' => $airline->id,
            'aircraft_id' => null,
        ]);

        $schedule = FlightSchedule::create([
            'flight_id' => $flight->id,
            'departure_datetime' => now()->addDay()->setTime(9, 0),
            'arrival_datetime' => now()->addDay()->setTime(11, 0),
            'available_seats' => 180,
            'status' => 'scheduled',
        ]);

        $this->post(route('bookings.create.select'), [
            'schedule_id' => $schedule->id,
        ])->assertRedirect(route('bookings.passengers'));

        $this->get(route('bookings.passengers'))
            ->assertOk()
            ->assertSee('data-seat-button', false)
            ->assertSee('Available seats')
            ->assertSee('Occupied seats');

        $this->assertGreaterThan(0, $schedule->fresh()->seats()->count());
        $this->assertSame(180, $schedule->fresh()->seats()->count());
    }

    public function test_passenger_step_renders_clickable_seat_map_and_blocks_occupied_seats(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $schedule = FlightSchedule::firstOrFail();
        $availableSeat = FlightScheduleSeat::where('flight_schedule_id', $schedule->id)
            ->where('status', 'available')
            ->firstOrFail();
        $occupiedSeat = FlightScheduleSeat::where('flight_schedule_id', $schedule->id)
            ->where('status', 'available')
            ->skip(1)
            ->firstOrFail();

        $occupiedSeat->update(['status' => 'booked']);

        $this->post(route('bookings.create.select'), [
            'schedule_id' => $schedule->id,
        ])->assertRedirect(route('bookings.passengers'));

        $this->get(route('bookings.passengers'))
            ->assertOk()
            ->assertSee('data-seat-button', false)
            ->assertSee('name="passengers[0][seat_number]"', false)
            ->assertSee('id="continueBooking"', false)
            ->assertSee('data-seat-number="' . $availableSeat->seat_number . '"', false)
            ->assertSee('data-seat-number="' . $occupiedSeat->seat_number . '"', false)
            ->assertSee('data-seat-status="booked"', false)
            ->assertSee('aria-disabled="true"', false);
    }

    public function test_passenger_selection_is_persisted_and_rejected_when_seat_is_unavailable(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $schedule = FlightSchedule::firstOrFail();
        $availableSeat = FlightScheduleSeat::where('flight_schedule_id', $schedule->id)
            ->where('status', 'available')
            ->firstOrFail();
        $unavailableSeat = FlightScheduleSeat::where('flight_schedule_id', $schedule->id)
            ->where('status', 'available')
            ->skip(1)
            ->firstOrFail();

        $unavailableSeat->update(['status' => 'booked']);

        $this->post(route('bookings.create.select'), [
            'schedule_id' => $schedule->id,
        ])->assertRedirect(route('bookings.passengers'));

        $this->post(route('bookings.passengers.save'), [
            'jumlah_tiket' => 1,
            'passengers' => [[
                'first_name' => 'John',
                'last_name' => 'Doe',
                'identity_type' => 'KTP',
                'identity_number' => '1234567890',
                'date_of_birth' => '1990-01-01',
                'nationality' => 'ID',
                'seat_number' => $availableSeat->seat_number,
            ]],
        ])->assertRedirect(route('bookings.confirm'));

        $this->assertSame($availableSeat->seat_number, session('booking_wizard.passengers.0.seat_number'));

        $this->get(route('bookings.confirm'))
            ->assertOk()
            ->assertSee('Seat: ' . $availableSeat->seat_number);

        $this->post(route('bookings.passengers.save'), [
            'jumlah_tiket' => 1,
            'passengers' => [[
                'first_name' => 'John',
                'last_name' => 'Doe',
                'identity_type' => 'KTP',
                'identity_number' => '1234567890',
                'date_of_birth' => '1990-01-01',
                'nationality' => 'ID',
                'seat_number' => $unavailableSeat->seat_number,
            ]],
        ])->assertSessionHasErrors('passengers');
    }

    public function test_passenger_step_rejects_duplicate_seat_selection(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $schedule = FlightSchedule::firstOrFail();
        $firstSeat = FlightScheduleSeat::where('flight_schedule_id', $schedule->id)
            ->where('status', 'available')
            ->firstOrFail();
        $secondSeat = FlightScheduleSeat::where('flight_schedule_id', $schedule->id)
            ->where('status', 'available')
            ->skip(1)
            ->firstOrFail();

        $this->post(route('bookings.create.select'), [
            'schedule_id' => $schedule->id,
        ])->assertRedirect(route('bookings.passengers'));

        $this->post(route('bookings.passengers.save'), [
            'jumlah_tiket' => 2,
            'passengers' => [
                [
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'identity_type' => 'KTP',
                    'identity_number' => '1234567890',
                    'date_of_birth' => '1990-01-01',
                    'nationality' => 'ID',
                    'seat_number' => $firstSeat->seat_number,
                ],
                [
                    'first_name' => 'Jane',
                    'last_name' => 'Roe',
                    'identity_type' => 'KTP',
                    'identity_number' => '2233445566',
                    'date_of_birth' => '1995-05-05',
                    'nationality' => 'ID',
                    'seat_number' => $firstSeat->seat_number,
                ],
            ],
        ])->assertSessionHasErrors('passengers');
    }
}
