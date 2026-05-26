<?php

namespace App\Services\Analytics;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\FlightSchedule;

class OperationalAnalyticsService
{
    /**
     * Get snapshot of operational metrics
     */
    public function getSnapshot(): array
    {
        return Cache::remember('analytics_operational_snapshot', now()->addMinutes(1), function () {
            $totalFlights = FlightSchedule::count();
            if ($totalFlights === 0) {
                return [
                    'otp_percentage' => 100,
                    'delay_ratio' => 0,
                    'cancellation_ratio' => 0,
                    'total_flights' => 0,
                    'delayed_flights' => 0,
                    'cancelled_flights' => 0,
                ];
            }

            $delayedFlights = FlightSchedule::where('status', 'delayed')->count();
            $cancelledFlights = FlightSchedule::where('status', 'cancelled')->count();
            
            // Assume departed, arrived, scheduled (if not delayed) are on-time for MVP
            $onTimeFlights = $totalFlights - $delayedFlights - $cancelledFlights;

            $otpPercentage = round(($onTimeFlights / $totalFlights) * 100, 1);
            $delayRatio = round(($delayedFlights / $totalFlights) * 100, 1);
            $cancellationRatio = round(($cancelledFlights / $totalFlights) * 100, 1);

            return [
                'otp_percentage' => $otpPercentage,
                'delay_ratio' => $delayRatio,
                'cancellation_ratio' => $cancellationRatio,
                'total_flights' => $totalFlights,
                'delayed_flights' => $delayedFlights,
                'cancelled_flights' => $cancelledFlights,
            ];
        });
    }
}
