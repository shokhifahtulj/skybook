<?php

namespace App\Services\Operations;

use App\Models\OperationalLog;
use Carbon\Carbon;

class FraudDetectionService
{
    /**
     * Check if a boarding pass scan attempt looks suspicious.
     * For example, multiple failed scans within a short timeframe.
     */
    public function detectScanAnomalies(string $boardingPassId): array
    {
        $recentFailedScans = OperationalLog::where('entity_id', $boardingPassId)
            ->where('event_type', 'boarding_rejected')
            ->where('created_at', '>=', Carbon::now()->subMinutes(5))
            ->count();

        $anomalies = [];
        
        if ($recentFailedScans >= 3) {
            $anomalies[] = 'Multiple invalid scan attempts detected within 5 minutes.';
        }

        return [
            'is_fraudulent' => count($anomalies) > 0,
            'anomalies' => $anomalies
        ];
    }
    
    /**
     * Get recent fraud alerts for the dashboard
     */
    public function getRecentAlerts(string|int $scheduleId, int $limit = 5)
    {
        return OperationalLog::where('flight_schedule_id', $scheduleId)
            ->where('level', 'danger')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
