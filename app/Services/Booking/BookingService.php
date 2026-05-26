<?php

namespace App\Services\Booking;

use App\Models\Booking;
use App\Models\BookingSegment;
use App\Models\BookingSegmentPassenger;
use App\Models\FlightSchedule;
use App\Models\Schedule;
use App\Services\Inventory\SeatLockService;
use Illuminate\Support\Facades\DB;

class BookingService
{
    protected $pnrService;
    protected $pricingService;
    protected $seatLockService;
    protected $passengerService;

    public function __construct(
        PnrGeneratorService $pnrService,
        BookingPricingService $pricingService,
        SeatLockService $seatLockService,
        PassengerService $passengerService
    ) {
        $this->pnrService = $pnrService;
        $this->pricingService = $pricingService;
        $this->seatLockService = $seatLockService;
        $this->passengerService = $passengerService;
    }

    /**
     * Create a new booking draft.
     */
    public function createBookingDraft(array $segments, array $passengers, string $lockSession, $userId = null)
    {
        \Illuminate\Support\Facades\Log::channel('booking')->info('Creating booking draft initiated', ['passengers_count' => count($passengers)]);

        return DB::transaction(function () use ($segments, $passengers, $lockSession, $userId) {
            $primarySegment = $segments[0] ?? [];
            $primaryScheduleId = $primarySegment['flight_schedule_id'] ?? null;
            $primaryFlightId = null;
            $primaryLegacyScheduleId = null;

            if ($primaryScheduleId) {
                $primarySchedule = FlightSchedule::query()->find($primaryScheduleId);
                $primaryFlightId = $primarySchedule?->flight_id;

                if ($primarySchedule && $primarySchedule->departure_datetime) {
                    $primaryLegacyScheduleId = Schedule::updateOrCreate(
                        [
                            'flight_id' => $primaryFlightId,
                            'tanggal' => $primarySchedule->departure_datetime->toDateString(),
                            'jam_berangkat' => $primarySchedule->departure_datetime->toTimeString(),
                        ],
                        [
                            'jam_tiba' => $primarySchedule->arrival_datetime?->toTimeString(),
                            'kapasitas' => $primarySchedule->available_seats ?? 180,
                        ]
                    )->id;
                }
            }

            // 1. Create Booking container
            $booking = Booking::create([
                'pnr' => $this->pnrService->generate(),
                'booking_status' => 'draft',
                'payment_status' => 'unpaid',
                'currency' => 'IDR',
                'booked_by' => $userId,
                'user_id' => $userId,
                'schedule_id' => $primaryLegacyScheduleId,
                'flight_id' => $primaryFlightId,
                'expires_at' => now()->addMinutes(config('seat_lock.duration_minutes', 15)),
                'total_amount' => 0
            ]);

            $totalBookingAmount = 0;

            // 2. Process Passengers
            $createdPassengers = [];
            foreach ($passengers as $idx => $pData) {
                $createdPassengers[$idx] = $this->passengerService->createForBooking($booking->id, $pData);
            }

            // 3. Process Segments
            foreach ($segments as $sIndex => $segmentData) {
                $scheduleId = $segmentData['flight_schedule_id'];
                $cabinClass = $segmentData['cabin_class'];

                // Snapshot fare for this segment
                $fare = $this->pricingService->getFareSnapshot($scheduleId, $cabinClass);

                $segment = BookingSegment::create([
                    'booking_id' => $booking->id,
                    'flight_schedule_id' => $scheduleId,
                    'segment_order' => $sIndex + 1,
                    'cabin_class' => $cabinClass,
                    'segment_status' => 'scheduled',
                    'fare_snapshot' => $fare['fare_price'],
                    'tax_snapshot' => $fare['tax_snapshot']
                ]);

                // 4. Process Seats per passenger for this segment
                foreach ($passengers as $pIndex => $pData) {
                    $seatNumber = $pData['seats'][$scheduleId] ?? null;
                    $lockedSeatId = null;

                    if ($seatNumber) {
                        // Lock the seat
                        $seat = $this->seatLockService->lockSeat($scheduleId, $seatNumber, $lockSession, $userId);
                        $lockedSeatId = $seat->id;
                        
                        // Associate seat with booking
                        $seat->booking_id = $booking->id;
                        $seat->save();
                    }

                    BookingSegmentPassenger::create([
                        'booking_segment_id' => $segment->id,
                        'passenger_id' => $createdPassengers[$pIndex]->id,
                        'flight_schedule_seat_id' => $lockedSeatId,
                    ]);

                    $totalBookingAmount += $fare['total'];
                }
            }

            // 5. Update total amount
            $booking->update(['total_amount' => $totalBookingAmount]);

            return $booking;
        });
    }
}
