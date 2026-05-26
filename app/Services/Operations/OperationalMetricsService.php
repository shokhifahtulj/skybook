<?php

namespace App\Services\Operations;

use App\Models\FlightSchedule;
use App\Models\FlightScheduleSeat;
use Illuminate\Support\Facades\DB;

class OperationalMetricsService
{
    /**
     * Get real-time boarding metrics for a specific flight schedule
     */
    public function getBoardingMetrics(string|int $scheduleId): array
    {
        $schedule = FlightSchedule::with('aircraft')->findOrFail($scheduleId);

        // Use the snapshot table (FlightScheduleSeat) for accurate runtime capacity
        $totalCapacity = FlightScheduleSeat::where('flight_schedule_id', $scheduleId)->count();

        $metrics = FlightScheduleSeat::where('flight_schedule_id', $scheduleId)
            ->select(
                DB::raw('count(*) as total_seats'),
                DB::raw('sum(case when status != \'available\' then 1 else 0 end) as booked_seats'),
                DB::raw('sum(case when status = \'checked_in\' then 1 else 0 end) as checked_in_seats'),
                DB::raw('sum(case when status = \'boarded\' then 1 else 0 end) as boarded_seats')
            )
            ->first();

        // Calculate no-shows (checked in but not boarded) - simplistic version
        $noShows = ($metrics->checked_in_seats ?? 0) - ($metrics->boarded_seats ?? 0);
        // If gate is closed, actual no-shows might also include people who didn't even check in 
        // depending on airline policy, but checked_in_seats vs boarded is a good proxy.

        // Ancillary Metrics
        $ancillaries = \App\Models\BookingPassengerAncillary::whereHas('bookingSegmentPassenger.segment', function($q) use ($scheduleId) {
            $q->where('flight_schedule_id', $scheduleId);
        })->where('status', 'paid')->get();

        $checkedBagsCount = $ancillaries->where('type', 'baggage')->count();
        $priorityBoardingCount = $ancillaries->where('type', 'priority_boarding')->count();
        $ancillaryRevenue = $ancillaries->sum('snapshot_price');

        return [
            'total_capacity' => $totalCapacity,
            'booked_count' => (int) $metrics->booked_seats,
            'checked_in_count' => (int) $metrics->checked_in_seats,
            'boarded_count' => (int) $metrics->boarded_seats,
            'remaining_to_board' => (int) $metrics->checked_in_seats - (int) $metrics->boarded_seats,
            'no_show_count' => max(0, $noShows),
            'checked_bags_count' => $checkedBagsCount,
            'priority_boarding_count' => $priorityBoardingCount,
            'ancillary_revenue' => $ancillaryRevenue,
        ];
    }
}
