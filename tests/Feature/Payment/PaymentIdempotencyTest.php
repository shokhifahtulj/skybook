<?php

namespace Tests\Feature\Payment;

use App\Models\Booking;
use App\Models\FlightSchedule;
use Database\Seeders\TestDatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Queue;
use App\Jobs\GenerateTicketPdf;

class PaymentIdempotencyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TestDatabaseSeeder::class);
    }

    public function test_duplicate_payment_callback_does_not_double_process()
    {
        Queue::fake();
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $schedule = FlightSchedule::first();

        // 1. Create Draft
        $response = $this->postJson('/api/bookings/create-draft', [
            'segments' => [
                ['flight_schedule_id' => $schedule->id, 'cabin_class' => 'economy']
            ],
            'passengers' => [
                [
                    'title' => 'Mr', 'first_name' => 'John', 'last_name' => 'Doe', 'date_of_birth' => '1990-01-01',
                    'passenger_type' => 'adult', 'identity_type' => 'KTP', 'identity_number' => '123',
                    'seats' => [$schedule->id => '1A']
                ]
            ]
        ]);

        $pnr = $response->json('data.pnr');

        // Create Payment
        $booking = Booking::where('pnr', $pnr)->first();
        $payment = app(\App\Services\Payment\PaymentService::class)->createPaymentForBooking($booking, 1000);

        // 2. First Webhook Success
        $res1 = $this->postJson("/api/bookings/{$pnr}/pay/success", [
            'payment_reference' => $payment->payment_reference
        ]);
        $res1->assertStatus(200);

        // Assert booking becomes ticketed
        $this->assertDatabaseHas('bookings', [
            'pnr' => $pnr,
            'booking_status' => 'ticketed',
            'payment_status' => 'paid'
        ]);

        // Assert seat is booked
        $this->assertDatabaseHas('flight_schedule_seats', [
            'seat_number' => '1A',
            'status' => 'booked'
        ]);

        // Assert Job dispatched ONCE
        Queue::assertPushed(GenerateTicketPdf::class, 1);

        // 3. Second Webhook Success (Duplicate)
        $res2 = $this->postJson("/api/bookings/{$pnr}/pay/success", [
            'payment_reference' => $payment->payment_reference
        ]);
        $res2->assertStatus(200);

        // Assert Job is STILL only dispatched ONCE
        Queue::assertPushed(GenerateTicketPdf::class, 1);
    }
}
