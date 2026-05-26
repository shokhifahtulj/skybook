<?php

namespace App\Services\Operations;

use App\Models\BookingSegmentPassenger;
use App\Models\Booking;
use Carbon\Carbon;

class CheckInEligibilityService
{
    /**
     * Memvalidasi apakah seorang penumpang berhak melakukan check-in
     * 
     * @param BookingSegmentPassenger $segmentPassenger
     * @return array [bool $isEligible, string $reason]
     */
    public function validateEligibility(BookingSegmentPassenger $segmentPassenger): array
    {
        $segment = $segmentPassenger->segment;
        $booking = $segment->booking;
        
        // 1. Booking Status
        if (!in_array($booking->booking_status, ['confirmed', 'ticketed'])) {
            return [false, 'Booking belum terkonfirmasi atau belum ditiketkan.'];
        }

        // 2. Ticket Status
        if (!$segmentPassenger->ticket) {
            return [false, 'Tiket belum diterbitkan untuk penumpang ini.'];
        }

        // 3. Payment Status
        if ($booking->payment_status !== 'paid') {
            return [false, 'Pembayaran belum diselesaikan.'];
        }

        // 4. Seat Assignment
        if (!$segmentPassenger->flight_schedule_seat_id) {
            return [false, 'Kursi belum ditentukan.'];
        }

        // 5. Operational Status Check
        if ($segmentPassenger->operational_status !== 'not_checked_in') {
            return [false, 'Penumpang ini sudah melakukan check-in atau status operasional tidak valid.'];
        }

        // 6. Check-in Window
        $flightSchedule = $segment->schedule;
        $departureTimeUtc = Carbon::parse($flightSchedule->departure_datetime)->timezone('UTC');
        $nowUtc = now()->timezone('UTC');

        $opensHours = config('checkin.opens_hours_before_departure', 24);
        $closesMinutes = config('checkin.closes_minutes_before_departure', 45);

        $checkInOpensAt = $departureTimeUtc->copy()->subHours($opensHours);
        $checkInClosesAt = $departureTimeUtc->copy()->subMinutes($closesMinutes);

        if ($nowUtc->isBefore($checkInOpensAt)) {
            return [false, 'Waktu check-in belum dibuka. Check-in dibuka ' . $opensHours . ' jam sebelum keberangkatan.'];
        }

        if ($nowUtc->isAfter($checkInClosesAt)) {
            return [false, 'Waktu check-in sudah ditutup.'];
        }

        return [true, 'Eligible for check-in.'];
    }
}
