<?php

namespace App\Services\Analytics;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\Payment;
use App\Models\BookingSegmentPassenger;
use App\Models\BookingPassengerAncillary;

class RevenueAnalyticsService
{
    /**
     * Get snapshot of revenue metrics
     */
    public function getSnapshot(): array
    {
        return Cache::remember('analytics_revenue_snapshot', now()->addMinutes(15), function () {
            $totalRevenue = Payment::where('status', 'paid')->sum('amount');

            // For MVP, we estimate ancillary vs flight by calculating ancillary total explicitly
            $ancillaryRevenue = BookingPassengerAncillary::where('status', 'paid')
                ->sum('snapshot_price');

            $flightRevenue = $totalRevenue - $ancillaryRevenue;

            $totalPassengers = BookingSegmentPassenger::count();
            $revenuePerPassenger = $totalPassengers > 0 ? $totalRevenue / $totalPassengers : 0;

            $flightCount = DB::table('flight_schedules')->where('status', '!=', 'cancelled')->count();
            $revenuePerFlight = $flightCount > 0 ? $totalRevenue / $flightCount : 0;

            return [
                'total_revenue' => $totalRevenue,
                'flight_revenue' => $flightRevenue,
                'ancillary_revenue' => $ancillaryRevenue,
                'revenue_per_passenger' => $revenuePerPassenger,
                'revenue_per_flight' => $revenuePerFlight,
            ];
        });
    }
}
