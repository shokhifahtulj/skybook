<?php

namespace App\Services\Operations;

use App\Models\FlightSchedule;
use App\Models\OperationalLog;

class OperationsDashboardService
{
    protected $metricsService;
    protected $fraudService;

    public function __construct(
        OperationalMetricsService $metricsService,
        FraudDetectionService $fraudService
    ) {
        $this->metricsService = $metricsService;
        $this->fraudService = $fraudService;
    }

    /**
     * Get aggregate dashboard data for a specific schedule
     */
    public function getDashboardData(string|int $scheduleId): array
    {
        $schedule = FlightSchedule::with(['flight.route.origin', 'flight.route.destination', 'aircraft'])->findOrFail($scheduleId);

        $metrics = $this->metricsService->getBoardingMetrics($scheduleId);

        // Get recent operational feed (last 15 items)
        $feed = OperationalLog::where('flight_schedule_id', $scheduleId)
            ->orderBy('created_at', 'desc')
            ->limit(15)
            ->get();

        $fraudAlerts = $this->fraudService->getRecentAlerts($scheduleId);

        return [
            'schedule' => $schedule,
            'metrics' => $metrics,
            'feed' => $feed,
            'alerts' => $fraudAlerts,
            'flight_status' => $schedule->status // e.g. 'scheduled', 'checkin_open', 'boarding', 'departed'
        ];
    }
}
