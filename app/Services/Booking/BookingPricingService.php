<?php

namespace App\Services\Booking;

use App\Models\FlightSchedule;
use App\Models\FlightSchedulePrice;
use Exception;
use Illuminate\Support\Facades\Log;

class BookingPricingService
{
    private const DEFAULT_ECONOMY_FARE = 1000000;

    /**
     * Get the fare snapshot for a specific schedule and cabin class.
     * Returns an array with fare_price and tax amount.
     */
    public function getFareSnapshot($scheduleId, $cabinClass): array
    {
        $priceObj = FlightSchedulePrice::where('flight_schedule_id', $scheduleId)
            ->where('cabin_class', $cabinClass)
            ->first();

        if (!$priceObj) {
            if ($cabinClass !== 'economy') {
                throw new Exception("Harga untuk kelas $cabinClass tidak ditemukan.");
            }

            $schedule = FlightSchedule::find($scheduleId);

            if (!$schedule) {
                throw new Exception("Jadwal dengan ID $scheduleId tidak ditemukan.");
            }

            Log::warning('Missing economy fare for flight schedule; creating a default fare snapshot.', [
                'flight_schedule_id' => $scheduleId,
                'cabin_class' => $cabinClass,
                'default_price' => self::DEFAULT_ECONOMY_FARE,
            ]);

            $priceObj = FlightSchedulePrice::create([
                'flight_schedule_id' => $schedule->id,
                'cabin_class' => 'economy',
                'price' => self::DEFAULT_ECONOMY_FARE,
                'quota' => max(1, (int) ($schedule->available_seats ?? 100)),
            ]);
        }

        // Example tax calculation (e.g. 11% VAT)
        $tax = $priceObj->price * 0.11;

        return [
            'fare_price' => $priceObj->price,
            'tax_snapshot' => $tax,
            'total' => $priceObj->price + $tax
        ];
    }
}
