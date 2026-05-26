<?php

namespace Tests\Feature\PassengerPortal;

use App\Models\Booking;
use App\Models\BookingSegmentPassenger;
use App\Models\FlightSchedule;
use App\Models\FlightScheduleSeat;
use App\Models\User;
use Database\Seeders\TestDatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class CheckInLayoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TestDatabaseSeeder::class);
    }

    private function createBooking(string $firstName): array
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
                'first_name' => $firstName,
                'last_name' => 'Example',
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
                'first_name' => $firstName,
                'last_name' => 'Example',
                'identity_type' => 'KTP',
                'identity_number' => '1234567890',
                'date_of_birth' => '1990-01-01',
                'nationality' => 'ID',
                'seat_number' => $seat->seat_number,
            ]],
        ])->assertRedirect(route('bookings.index'));

        $booking = Booking::latest('created_at')->firstOrFail();
        $segmentPassenger = BookingSegmentPassenger::whereHas('segment', function ($query) use ($booking) {
            $query->where('booking_id', $booking->id);
        })->firstOrFail();

        return compact('user', 'schedule', 'seat', 'booking', 'segmentPassenger');
    }

    public function test_check_in_pages_use_the_shared_workspace_shell(): void
    {
        $created = $this->createBooking('Alice');

        $portalUrl = URL::temporarySignedRoute('checkin.portal', now()->addHours(2), [
            'pnr' => $created['booking']->pnr,
        ]);

        $response = $this->get($portalUrl);

        $response->assertStatus(200);
        $response->assertSee('Select Passengers');
        $response->assertSee('Passenger workspace');
    }

    public function test_check_in_seatmap_allows_reselecting_the_current_seat_and_disables_other_booked_seats(): void
    {
        $firstBooking = $this->createBooking('Alice');
        $secondBooking = $this->createBooking('Bob');

        $firstSeat = $firstBooking['segmentPassenger']->seat;
        $otherBookedSeat = FlightScheduleSeat::where('flight_schedule_id', $firstBooking['schedule']->id)
            ->where('status', 'booked')
            ->where('booking_id', '!=', $firstBooking['booking']->id)
            ->firstOrFail();

        $seatmapUrl = URL::temporarySignedRoute('checkin.seatmap', now()->addHours(2), [
            'pnr' => $firstBooking['booking']->pnr,
            'passenger_id' => $firstBooking['segmentPassenger']->id,
        ]);

        $seatmapResponse = $this->get($seatmapUrl);

        $seatmapResponse->assertStatus(200);
        $seatmapResponse->assertSee('Passenger workspace');
        $seatmapResponse->assertSee('value="' . $firstSeat->seat_number . '"', false);
        $seatmapResponse->assertSee('disabled', false);
        $seatmapResponse->assertSee('value="' . $otherBookedSeat->seat_number . '"', false);

        $updateUrl = URL::temporarySignedRoute('checkin.seatmap.update', now()->addHours(2), [
            'pnr' => $firstBooking['booking']->pnr,
            'passenger_id' => $firstBooking['segmentPassenger']->id,
        ]);

        $updateResponse = $this->post($updateUrl, [
            'new_seat' => $firstSeat->seat_number,
        ]);

        $updateResponse->assertRedirect();
        $updateResponse->assertSessionHas('success', 'Kursi berhasil ditukar.');
    }
}
