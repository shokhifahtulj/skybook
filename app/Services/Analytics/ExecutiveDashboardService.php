<?php

namespace App\Services\Analytics;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ExecutiveDashboardService
{
    protected $revenueService;
    protected $operationalService;
    protected $passengerService;

    public function __construct(
        RevenueAnalyticsService $revenueService,
        OperationalAnalyticsService $operationalService,
        PassengerAnalyticsService $passengerService
    ) {
        $this->revenueService = $revenueService;
        $this->operationalService = $operationalService;
        $this->passengerService = $passengerService;
    }

    /**
     * Get aggregate snapshot for the executive dashboard
     */
    public function getDailySnapshot(): array
    {
        // This acts as the facade/aggregator for the UI
        return [
            'revenue' => $this->revenueService->getSnapshot(),
            'operations' => $this->operationalService->getSnapshot(),
            'passenger' => $this->passengerService->getSnapshot(),
            'security_alerts' => $this->getSecurityAlertsSnapshot(),
        ];
    }

    /**
     * Fetch security alerts (uncached for now to be real-time for security)
     */
    private function getSecurityAlertsSnapshot(): array
    {
        return Cache::remember('analytics_security_snapshot', now()->addMinutes(1), function () {
            $invalidScans = DB::table('operational_logs')
                ->where('event_type', 'signature_invalid')
                ->count();
                
            $duplicateScans = DB::table('operational_logs')
                ->where('event_type', 'duplicate_scan_attempt')
                ->count();
                
            $supersededScans = DB::table('operational_logs')
                ->where('event_type', 'superseded_scan_attempt')
                ->count();

            return [
                'invalid_scans' => $invalidScans,
                'duplicate_scans' => $duplicateScans,
                'superseded_scans' => $supersededScans,
                'total_security_events' => $invalidScans + $duplicateScans + $supersededScans,
            ];
        });
    }
}
