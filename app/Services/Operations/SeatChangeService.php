<?php

namespace App\Services\Operations;

use App\Models\BookingSegmentPassenger;
use App\Models\FlightScheduleSeat;
use App\Events\SeatChanged;
use Exception;
use Illuminate\Support\Facades\DB;

class SeatChangeService
{
    /**
     * Melakukan pertukaran kursi secara transaksional untuk satu penumpang.
     *
     * @param string $bookingSegmentPassengerId
     * @param string $newSeatNumber
     * @return BookingSegmentPassenger
     * @throws Exception
     */
    public function changeSeat(string $bookingSegmentPassengerId, string $newSeatNumber): BookingSegmentPassenger
    {
        return DB::transaction(function () use ($bookingSegmentPassengerId, $newSeatNumber) {
            $segmentPassenger = BookingSegmentPassenger::with(['segment'])
                ->lockForUpdate()
                ->findOrFail($bookingSegmentPassengerId);

            $flightScheduleId = $segmentPassenger->segment->flight_schedule_id;
            $oldSeatId = $segmentPassenger->flight_schedule_seat_id;

            // Lock the new seat row
            $newSeat = FlightScheduleSeat::where('flight_schedule_id', $flightScheduleId)
                ->where('seat_number', $newSeatNumber)
                ->lockForUpdate()
                ->first();

            if (!$newSeat) {
                throw new Exception("Kursi $newSeatNumber tidak ditemukan pada penerbangan ini.");
            }

            if ($newSeat->id === $oldSeatId) {
                return $segmentPassenger;
            }

            if ($newSeat->status !== 'available' && $newSeat->status !== 'active') {
                throw new Exception("Kursi $newSeatNumber tidak tersedia.");
            }

            // Release old seat
            if ($oldSeatId) {
                FlightScheduleSeat::where('id', $oldSeatId)->update([
                    'status' => 'active',
                    'booking_id' => null
                ]);
            }

            // Assign new seat
            $newSeat->update([
                'status' => 'booked',
                'booking_id' => $segmentPassenger->segment->booking_id
            ]);

            $segmentPassenger->update([
                'flight_schedule_seat_id' => $newSeat->id
            ]);

            // Dispatch event
            SeatChanged::dispatch($segmentPassenger, $oldSeatId, $newSeat->id);

            return $segmentPassenger;
        });
    }
}
