<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$schedule = App\Models\FlightSchedule::first();
if (!$schedule) {
    echo "No schedule found.";
    exit;
}
$pnrService = app(App\Services\Booking\PnrGeneratorService::class);
$pnr = $pnrService->generate();
$booking = App\Models\Booking::create(['pnr' => $pnr, 'booking_status' => 'ticketed', 'payment_status' => 'paid', 'total_amount' => 1100000, 'currency' => 'IDR', 'expires_at' => now()->addHour()]);
$passenger = App\Models\Passenger::create(['booking_id' => $booking->id, 'title' => 'Mr', 'first_name' => 'John', 'last_name' => 'Doe', 'passenger_type' => 'adult', 'date_of_birth' => '1990-01-01', 'identity_type' => 'KTP', 'identity_number' => '12345']);
$segment = App\Models\BookingSegment::create(['booking_id' => $booking->id, 'flight_schedule_id' => $schedule->id, 'segment_order' => 1, 'cabin_class' => 'economy', 'segment_status' => 'scheduled', 'fare_snapshot' => 1000000, 'tax_snapshot' => 100000]);
$seat = App\Models\FlightScheduleSeat::where('flight_schedule_id', $schedule->id)->first();
if ($seat) { $seat->update(['status' => 'booked', 'booking_id' => $booking->id]); }
$sp = App\Models\BookingSegmentPassenger::create(['booking_segment_id' => $segment->id, 'passenger_id' => $passenger->id, 'flight_schedule_seat_id' => $seat ? $seat->id : null]);
$ticket = App\Models\Ticket::create(['booking_segment_passenger_id' => $sp->id, 'ticket_number' => 'TKT-123', 'ticket_status' => 'issued', 'issued_at' => now(), 'document_path' => 'dummy', 'snapshot_data' => []]);

echo $pnr;
