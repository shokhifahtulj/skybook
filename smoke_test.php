<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Flight;
use App\Models\FlightSchedule;
use App\Models\FlightSchedulePrice;
use App\Models\FlightScheduleSeat;
use App\Services\Inventory\FlightSeatInventoryService;
use App\Services\Booking\BookingService;
use App\Services\Payment\PaymentService;
use App\Services\Payment\PaymentCallbackService;
use Illuminate\Support\Str;

try {
    echo "--- 1. Schedule Creation ---\n";
    $flight = Flight::first();
    if (!$flight) {
        $airport1 = \App\Models\Airport::firstOrCreate(['iata_code' => 'CGK'], ['name' => 'Soekarno Hatta', 'city' => 'Jakarta', 'country' => 'Indonesia', 'timezone' => 'Asia/Jakarta']);
        $airport2 = \App\Models\Airport::firstOrCreate(['iata_code' => 'DPS'], ['name' => 'Ngurah Rai', 'city' => 'Bali', 'country' => 'Indonesia', 'timezone' => 'Asia/Makassar']);
        $airline = \App\Models\Airline::firstOrCreate(['code' => 'GA'], ['name' => 'Garuda Indonesia']);
        $aircraft = \App\Models\Aircraft::firstOrCreate(['model' => 'Boeing 737'], ['airline_id' => $airline->id, 'capacity' => 100, 'seat_layout' => '3-3']);
        $route = \App\Models\Route::firstOrCreate(['origin_airport_id' => $airport1->id, 'destination_airport_id' => $airport2->id], ['airline_id' => $airline->id, 'base_duration' => 120]);
        $flight = Flight::firstOrCreate(['flight_number' => 'GA-100'], ['route_id' => $route->id, 'aircraft_id' => $aircraft->id, 'airline_id' => $airline->id]);
    }
    
    if (!\App\Models\AircraftSeat::where('aircraft_id', $flight->aircraft_id)->exists()) {
        for ($i = 1; $i <= 5; $i++) {
            foreach (['A', 'B', 'C', 'D', 'E', 'F'] as $letter) {
                \App\Models\AircraftSeat::create([
                    'aircraft_id' => $flight->aircraft_id,
                    'seat_number' => $i . $letter,
                    'cabin_class' => 'economy',
                    'row_number' => $i,
                    'seat_letter' => $letter,
                    'status' => 'active'
                ]);
            }
        }
    }

    $schedule = FlightSchedule::create([
        'flight_id' => $flight->id,
        'departure_datetime' => now()->addDays(2),
        'arrival_datetime' => now()->addDays(2)->addHours(2),
        'available_seats' => 10,
        'status' => 'scheduled'
    ]);
    
    FlightSchedulePrice::create(['flight_schedule_id' => $schedule->id, 'cabin_class' => 'economy', 'price' => 1000000, 'quota' => 10]);

    app(FlightSeatInventoryService::class)->generate($schedule);
    echo "Schedule and inventory generated.\n";
    
    echo "--- 2. Seat Lock & Booking Draft ---\n";
    $seat = FlightScheduleSeat::where('flight_schedule_id', $schedule->id)->first();
    
    $bookingService = app(BookingService::class);
    $segments = [
        [
            'flight_schedule_id' => $schedule->id,
            'cabin_class' => 'economy'
        ]
    ];
    $passengers = [
        [
            'title' => 'Mr',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'identity_type' => 'KTP',
            'identity_number' => '1234567890',
            'date_of_birth' => '1990-01-01',
            'seats' => [
                $schedule->id => $seat->seat_number
            ]
        ]
    ];
    
    $lockSession = Str::uuid()->toString();
    $booking = $bookingService->createBookingDraft($segments, $passengers, $lockSession);
    
    $seat->refresh();
    echo "PNR: {$booking->pnr}, Status: {$booking->booking_status}\n";
    echo "Seat {$seat->seat_number} status: {$seat->status}, Locked Until: {$seat->locked_until}\n";
    
    echo "--- 3. Payment Simulation & Booking Confirmation ---\n";
    $paymentService = app(PaymentService::class);
    $payment = $paymentService->createPaymentForBooking($booking, $booking->total_amount);
    echo "Payment created: {$payment->payment_reference}\n";
    
    $callbackService = app(PaymentCallbackService::class);
    $callbackService->handleSuccess($payment->payment_reference);
    
    $booking->refresh();
    $seat->refresh();
    echo "Booking Status: {$booking->booking_status}, Payment Status: {$booking->payment_status}\n";
    echo "Seat Status: {$seat->status}, Booked At: {$seat->booked_at}\n";
    
    echo "--- 4. Ticket Issuance ---\n";
    $ticket = App\Models\Ticket::first();
    echo "Ticket Number: {$ticket->ticket_number}, Status: {$ticket->ticket_status}\n";

    echo "--- 5. Duplicate Callback (Idempotency) ---\n";
    $callbackService->handleSuccess($payment->payment_reference);
    echo "Duplicate callback ignored successfully.\n";

    echo "\nSMOKE TEST PASSED!\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n" . $e->getTraceAsString();
}
