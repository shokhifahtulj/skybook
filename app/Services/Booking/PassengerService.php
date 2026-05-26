<?php

namespace App\Services\Booking;

use App\Models\Passenger;

class PassengerService
{
    public function createForBooking($bookingId, array $passengerData): Passenger
    {
        return Passenger::create([
            'booking_id' => $bookingId,
            'title' => $passengerData['title'],
            'first_name' => $passengerData['first_name'],
            'last_name' => $passengerData['last_name'] ?? null,
            'identity_type' => $passengerData['identity_type'],
            'identity_number' => $passengerData['identity_number'],
            'date_of_birth' => $passengerData['date_of_birth'],
            'nationality' => $passengerData['nationality'] ?? 'ID',
            'passenger_type' => $passengerData['passenger_type'] ?? 'adult',
        ]);
    }
}
