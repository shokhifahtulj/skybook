<?php

namespace App\Services\Intelligence;

use App\Models\PredictiveAlert;

class AutonomousResolutionPolicy
{
    /**
     * Determine if a predictive alert qualifies for auto-resolution
     */
    public function canAutoResolve(PredictiveAlert $alert): bool
    {
        // 1. Only auto-resolve gate conflicts for now
        if ($alert->prediction_type !== 'GATE_CONFLICT') {
            return false;
        }

        // 2. High confidence requirement
        if ($alert->confidence_score < 90) {
            return false;
        }

        // 3. Must not be in boarding status
        if (in_array($alert->flightSchedule->status, ['boarding', 'final_call', 'departed', 'arrived'])) {
            return false;
        }

        // 4. Must not have already been resolved
        if ($alert->status === 'MITIGATED' || $alert->status === 'RESOLVED') {
            return false;
        }

        return true;
    }
}
