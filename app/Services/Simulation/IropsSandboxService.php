<?php

namespace App\Services\Simulation;

use App\Models\SimulationSession;
use App\Models\FlightSchedule;
use App\Models\GateOccupancy;
use App\Models\NotificationDelivery;
use App\Models\OperationalLog;
use Carbon\Carbon;

class IropsSandboxService
{
    /**
     * Create baseline snapshot and start a simulation session
     */
    public function createBaseline(string $name, string $scenarioSeed): SimulationSession
    {
        // Snapshot only the relevant operational state
        $flightSchedules = FlightSchedule::all()->map(function ($schedule) {
            return [
                'id' => $schedule->id,
                'departure_datetime' => $schedule->departure_datetime->toIso8601String(),
                'arrival_datetime' => $schedule->arrival_datetime->toIso8601String(),
                'status' => $schedule->status,
                'departure_gate_id' => $schedule->departure_gate_id,
                'arrival_gate_id' => $schedule->arrival_gate_id,
                'available_seats' => $schedule->available_seats,
                'remarks' => $schedule->remarks
            ];
        })->toArray();

        // Note: GateOccupancies are generally recreated, but let's snapshot them to be safe
        $gateOccupancies = GateOccupancy::all()->toArray();

        $snapshot = [
            'flight_schedules' => $flightSchedules,
            'gate_occupancies' => $gateOccupancies,
        ];

        return SimulationSession::create([
            'name' => $name,
            'scenario_type' => 'AIRPORT_CLOSURE',
            'scenario_seed' => $scenarioSeed,
            'baseline_snapshot' => $snapshot,
            'status' => 'ACTIVE',
            'created_by' => auth()->id(),
            'started_at' => now(),
        ]);
    }

    /**
     * Inject chaos scenario (e.g. CGK_STORM_3H)
     */
    public function injectScenario(SimulationSession $session)
    {
        if ($session->scenario_seed === 'CGK_STORM_3H') {
            // Find all flights departing from CGK (let's assume ID 1 or code CGK)
            // For MVP, we'll find flights departing in the next 12 hours from airport ID 1
            $affectedSchedules = FlightSchedule::whereHas('flight.route.origin', function ($q) {
                $q->where('iata_code', 'CGK'); // Replace with generic lookup if needed
            })->where('departure_datetime', '>=', now())
              ->where('departure_datetime', '<=', now()->addHours(12))
              ->get();

            $delayMinutes = 180; // 3 hours

            foreach ($affectedSchedules as $schedule) {
                // Update schedule
                $schedule->update([
                    'departure_datetime' => $schedule->departure_datetime->addMinutes($delayMinutes),
                    'arrival_datetime' => $schedule->arrival_datetime->addMinutes($delayMinutes),
                    'status' => 'delayed',
                    'remarks' => 'Weather Disruption (CGK Storm)'
                ]);

                // Fire event to trigger propagation (e.g. RotationEngine, PassengerRebooked)
                event(new \App\Events\Operations\FlightDisrupted($schedule, 'delayed', 'Weather Disruption (CGK Storm)'));
                
                // Let the rotation engine handle propagation if you have it linked
                // \App\Services\Operations\RotationEngineService::propagateDelay($schedule); 
            }

            // Record Log
            OperationalLog::create([
                'event_type' => 'SCENARIO_INJECTED',
                'entity_type' => 'SimulationSession',
                'entity_id' => $session->id,
                'logged_by' => auth()->id(),
                'payload' => [
                    'message' => "Injected CGK Storm 3H Scenario. Delayed {$affectedSchedules->count()} flights.",
                ]
            ]);

            // Calculate initial KPI impact immediately after chaos
            $this->calculateKpi($session);
        }
    }

    /**
     * Calculate and snapshot KPIs
     */
    public function calculateKpi(SimulationSession $session)
    {
        $kpi = [
            'total_delayed_flights' => FlightSchedule::where('status', 'delayed')->count(),
            'cancellations' => FlightSchedule::where('status', 'cancelled')->count(),
            'stranded_passengers' => 0, // Simplified for now
            'gate_conflicts' => GateOccupancy::where('occupancy_type', 'CONFLICT')->count(),
            'notifications_sent' => NotificationDelivery::where('sent_at', '>=', $session->started_at)->count(),
        ];

        $session->update(['kpi_snapshot' => $kpi]);
    }

    /**
     * Restore Baseline Snapshot
     */
    public function restoreBaseline(SimulationSession $session)
    {
        $snapshot = $session->baseline_snapshot;

        // Restore Flight Schedules
        foreach ($snapshot['flight_schedules'] as $fsSnapshot) {
            $schedule = FlightSchedule::find($fsSnapshot['id']);
            if ($schedule) {
                // We use Carbon to parse back the ISO strings
                $schedule->update([
                    'departure_datetime' => Carbon::parse($fsSnapshot['departure_datetime']),
                    'arrival_datetime' => Carbon::parse($fsSnapshot['arrival_datetime']),
                    'status' => $fsSnapshot['status'],
                    'departure_gate_id' => $fsSnapshot['departure_gate_id'],
                    'arrival_gate_id' => $fsSnapshot['arrival_gate_id'],
                    'available_seats' => $fsSnapshot['available_seats'],
                    'remarks' => $fsSnapshot['remarks']
                ]);
            }
        }

        // Restore Gate Occupancies
        GateOccupancy::truncate();
        foreach ($snapshot['gate_occupancies'] as $goSnapshot) {
            GateOccupancy::insert($goSnapshot);
        }

        $session->update([
            'status' => 'RESTORED',
            'ended_at' => now(),
        ]);

        OperationalLog::create([
            'event_type' => 'BASELINE_RESTORED',
            'entity_type' => 'SimulationSession',
            'entity_id' => $session->id,
            'logged_by' => auth()->id(),
            'payload' => [
                'message' => "Restored baseline for session {$session->name}",
            ]
        ]);
    }
}
