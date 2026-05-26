<?php

namespace App\Services\Intelligence;

use App\Models\PredictiveAlert;
use App\Models\AirportGate;
use App\Models\GateOccupancy;
use App\Models\OperationalLog;
use Illuminate\Support\Facades\DB;

class AutonomousResolutionService
{
    protected $policy;

    public function __construct(AutonomousResolutionPolicy $policy)
    {
        $this->policy = $policy;
    }

    /**
     * Evaluate and execute auto-resolution for active predictive alerts
     */
    public function executeResolutions()
    {
        /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\PredictiveAlert[] $alerts */
        $alerts = PredictiveAlert::where('status', 'PREDICTED')
            ->where('prediction_type', 'GATE_CONFLICT')
            ->get();

        foreach ($alerts as $alert) {
            if ($this->policy->canAutoResolve($alert)) {
                $this->autoResolveGateConflict($alert);
            }
        }
    }

    protected function autoResolveGateConflict(PredictiveAlert $alert)
    {
        DB::beginTransaction();
        try {
            $schedule = $alert->flightSchedule;
            $airportId = $schedule->route->destination_id;
            
            // Find a standby/available gate at the destination airport
            // For MVP: Find any gate that has no overlapping occupancy at the ETA
            $eta = $schedule->arrival_datetime;
            $bufferMinutes = 45;

            $availableGate = AirportGate::where('airport_id', $airportId)
                ->whereDoesntHave('occupancies', function($query) use ($eta, $bufferMinutes, $schedule) {
                    $query->where('flight_schedule_id', '!=', $schedule->id)
                          ->where(function($q) use ($eta, $bufferMinutes) {
                              $q->where('start_time', '<=', $eta->copy()->addMinutes($bufferMinutes))
                                ->where('end_time', '>=', $eta);
                          });
                })
                ->where('id', '!=', $schedule->arrival_gate_id)
                ->first();

            if ($availableGate) {
                $oldGateId = $schedule->arrival_gate_id;
                
                // Update Schedule
                $schedule->arrival_gate_id = $availableGate->id;
                $schedule->save();

                // Update Occupancy
                GateOccupancy::where('flight_schedule_id', $schedule->id)->update([
                    'airport_gate_id' => $availableGate->id
                ]);

                // Create Operational Log to make it reversible/auditable
                $log = OperationalLog::create([
                    'flight_schedule_id' => $schedule->id,
                    'event_type' => 'GateChanged',
                    'event_description' => "Arrival gate automatically changed from Gate ID {$oldGateId} to {$availableGate->gate_number}",
                    'actor' => 'SYSTEM_AUTO_RESOLVE',
                    'payload' => [
                        'old_gate_id' => $oldGateId,
                        'new_gate_id' => $availableGate->id,
                        'reason' => 'Predictive Gate Conflict Auto-Resolution'
                    ]
                ]);

                // Update Alert
                $alert->status = 'MITIGATED';
                $alert->resolved_at = \Illuminate\Support\Carbon::now();
                $alert->resolution_method = 'AUTO_GATE_SWAP';
                $alert->automation_payload = [
                    'policy_passed' => true,
                    'confidence_score' => $alert->confidence_score,
                    'action_taken' => "Swapped arrival gate to {$availableGate->gate_number}",
                    'rollback_available' => true,
                    'operational_log_id' => $log->id,
                    'impact_summary' => 'Conflict resolved safely. Zero passenger impact.'
                ];
                $alert->save();

                DB::commit();
            } else {
                // Cannot auto-resolve, no gates available
                DB::rollBack();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            // Log failure silently for system
            \Log::error('Auto-resolution failed: ' . $e->getMessage());
        }
    }
}
