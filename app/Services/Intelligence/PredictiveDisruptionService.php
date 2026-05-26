<?php

namespace App\Services\Intelligence;

use App\Models\FlightSchedule;
use App\Models\GateOccupancy;
use App\Models\PredictiveAlert;
use Carbon\Carbon;

class PredictiveDisruptionService
{
    /**
     * Scan the network for potential future gate conflicts
     */
    public function scanForGateConflicts()
    {
        // 1. Find all delayed flights that haven't arrived yet
        $delayedSchedules = FlightSchedule::where('status', 'delayed')
            ->where('departure_datetime', '<=', now()->addHours(12))
            ->get();

        foreach ($delayedSchedules as $schedule) {
            if (!$schedule->arrival_gate_id) {
                continue;
            }

            // Estimate new arrival time
            $eta = $schedule->arrival_datetime;
            $bufferMinutes = 30; // Buffer needed at the gate

            // Check if another flight occupies that gate at the new ETA
            $overlappingOccupancy = GateOccupancy::where('airport_gate_id', $schedule->arrival_gate_id)
                ->where('flight_schedule_id', '!=', $schedule->id)
                ->where(function($query) use ($eta, $bufferMinutes) {
                    $query->where('start_time', '<=', $eta->copy()->addMinutes($bufferMinutes))
                          ->where('end_time', '>=', $eta);
                })
                ->first();

            if ($overlappingOccupancy) {
                // Determine severity and forecast window
                $timeToImpact = now()->diffInMinutes($eta, false);
                if ($timeToImpact < 0) continue; // Already arrived

                $severity = 'MEDIUM';
                if ($timeToImpact <= 120) $severity = 'HIGH';
                if ($timeToImpact <= 60) $severity = 'CRITICAL';

                // Check if alert already exists and is active
                $existingAlert = PredictiveAlert::where('flight_schedule_id', $schedule->id)
                    ->where('prediction_type', 'GATE_CONFLICT')
                    ->whereIn('status', ['PREDICTED', 'CONFIRMED'])
                    ->first();

                if (!$existingAlert) {
                    PredictiveAlert::create([
                        'flight_schedule_id' => $schedule->id,
                        'prediction_type' => 'GATE_CONFLICT',
                        'severity' => $severity,
                        'description' => "Arrival overlaps with Flight {$overlappingOccupancy->flightSchedule->flight->flight_number} at Gate {$overlappingOccupancy->gate->gate_number}.",
                        'confidence_score' => 85, // High confidence for gate conflicts
                        'forecast_window_minutes' => $timeToImpact,
                        'predicted_at' => now(),
                    ]);
                }
            }
        }
    }

    /**
     * Scan the network for potential future cascading rotation delays
     */
    public function scanForRotationDelays()
    {
        // For MVP: Simply check if a delayed flight's turnaround overlaps with its NEXT sector's scheduled departure
        $delayedSchedules = FlightSchedule::where('status', 'delayed')
            ->where('departure_datetime', '<=', now()->addHours(12))
            ->get();

        foreach ($delayedSchedules as $schedule) {
            // Find the next schedule for this aircraft
            $nextSchedule = FlightSchedule::whereHas('flight', function($q) use ($schedule) {
                $q->where('aircraft_id', $schedule->flight->aircraft_id);
            })
            ->where('departure_datetime', '>', $schedule->departure_datetime)
            ->orderBy('departure_datetime', 'asc')
            ->first();

            if ($nextSchedule) {
                $requiredTurnaround = 45; // Minimum TAT in minutes
                $eta = $schedule->arrival_datetime;
                $projectedReadyTime = $eta->copy()->addMinutes($requiredTurnaround);

                if ($projectedReadyTime->greaterThan($nextSchedule->departure_datetime)) {
                    $timeToImpact = now()->diffInMinutes($nextSchedule->departure_datetime, false);
                    if ($timeToImpact < 0) continue;

                    $severity = 'HIGH';
                    if ($timeToImpact <= 60) $severity = 'CRITICAL';

                    // Check if alert already exists
                    $existingAlert = PredictiveAlert::where('flight_schedule_id', $nextSchedule->id) // Note: alert goes to NEXT schedule
                        ->where('prediction_type', 'ROTATION_DELAY')
                        ->whereIn('status', ['PREDICTED', 'CONFIRMED'])
                        ->first();

                    if (!$existingAlert) {
                        PredictiveAlert::create([
                            'flight_schedule_id' => $nextSchedule->id,
                            'prediction_type' => 'ROTATION_DELAY',
                            'severity' => $severity,
                            'description' => "Inbound Flight {$schedule->flight->flight_number} delayed. Turnaround time insufficient.",
                            'confidence_score' => 92, // Mathematical certainty
                            'forecast_window_minutes' => $timeToImpact,
                            'predicted_at' => now(),
                        ]);
                    }
                }
            }
        }
    }
}
