<?php

namespace App\Services\Operations;

use App\Models\FlightSchedule;
use App\Models\AirportGate;
use App\Events\Operations\GateConflictDetected;
use Illuminate\Support\Carbon;

class GateManagementService
{
    /**
     * Check if assigning a flight to a gate will cause overlap.
     * Departure Gate Buffer: -60m to +15m from departure time
     * Arrival Gate Buffer: +0m to +45m from arrival time
     */
    public function detectOverlap(AirportGate $gate, FlightSchedule $schedule, string $type): ?FlightSchedule
    {
        if ($type === 'departure') {
            $startBuffer = $schedule->departure_datetime->copy()->subMinutes(60);
            $endBuffer = $schedule->departure_datetime->copy()->addMinutes(15);
            $gateColumn = 'departure_gate_id';
        } else {
            $startBuffer = $schedule->arrival_datetime->copy();
            $endBuffer = $schedule->arrival_datetime->copy()->addMinutes(45);
            $gateColumn = 'arrival_gate_id';
        }

        $conflict = FlightSchedule::where($gateColumn, $gate->id)
            ->where('status', '!=', 'cancelled')
            ->where('id', '!=', $schedule->id)
            ->where(function ($query) use ($startBuffer, $endBuffer, $type) {
                if ($type === 'departure') {
                    $query->whereBetween('departure_datetime', [$startBuffer, $endBuffer])
                          ->orWhereRaw('DATE_SUB(departure_datetime, INTERVAL 60 MINUTE) <= ? AND DATE_ADD(departure_datetime, INTERVAL 15 MINUTE) >= ?', [$endBuffer, $startBuffer]);
                } else {
                    $query->whereBetween('arrival_datetime', [$startBuffer, $endBuffer])
                          ->orWhereRaw('arrival_datetime <= ? AND DATE_ADD(arrival_datetime, INTERVAL 45 MINUTE) >= ?', [$endBuffer, $startBuffer]);
                }
            })->first();

        return $conflict;
    }

    public function validateScheduleGates(FlightSchedule $schedule): void
    {
        // Validate departure gate
        if ($schedule->departure_gate_id) {
            $gate = AirportGate::find($schedule->departure_gate_id);
            if ($gate) {
                $conflict = $this->detectOverlap($gate, $schedule, 'departure');
                if ($conflict) {
                    $severity = 'LOW';
                    // Check if it's widebody or if delay is huge (simplified for MVP: just random severity or based on time diff)
                    $overlapMins = $schedule->departure_datetime->diffInMinutes($conflict->departure_datetime);
                    if ($overlapMins < 30) $severity = 'CRITICAL';
                    else if ($overlapMins < 60) $severity = 'MEDIUM';

                    event(new GateConflictDetected(
                        $schedule, 
                        $gate, 
                        $conflict, 
                        "Departure time overlap at {$gate->terminal}-{$gate->gate_number}",
                        $severity
                    ));
                }
            }
        }

        // Validate arrival gate
        if ($schedule->arrival_gate_id) {
            $gate = AirportGate::find($schedule->arrival_gate_id);
            if ($gate) {
                $conflict = $this->detectOverlap($gate, $schedule, 'arrival');
                if ($conflict) {
                    $severity = 'LOW';
                    $overlapMins = $schedule->arrival_datetime->diffInMinutes($conflict->arrival_datetime);
                    if ($overlapMins < 30) $severity = 'CRITICAL';
                    else if ($overlapMins < 60) $severity = 'MEDIUM';

                    event(new GateConflictDetected(
                        $schedule, 
                        $gate, 
                        $conflict, 
                        "Arrival time overlap at {$gate->terminal}-{$gate->gate_number}",
                        $severity
                    ));
                }
            }
        }
    }

    public function assignGate(FlightSchedule $schedule, AirportGate $gate, string $type)
    {
        if ($gate->status !== 'active') {
            throw new \Exception("Gate is not active.");
        }

        $conflict = $this->detectOverlap($gate, $schedule, $type);
        if ($conflict) {
            throw new \Exception("Gate conflict with Flight {$conflict->flight->flight_number}.");
        }

        $oldGateId = $type === 'departure' ? $schedule->departure_gate_id : $schedule->arrival_gate_id;

        if ($type === 'departure') {
            $schedule->update(['departure_gate_id' => $gate->id]);
            $occupiedFrom = $schedule->departure_datetime->copy()->subMinutes(60);
            $occupiedUntil = $schedule->departure_datetime->copy()->addMinutes(15);
        } else {
            $schedule->update(['arrival_gate_id' => $gate->id]);
            $occupiedFrom = $schedule->arrival_datetime->copy();
            $occupiedUntil = $schedule->arrival_datetime->copy()->addMinutes(45);
        }

        // Update or Create Gate Occupancy
        \App\Models\GateOccupancy::updateOrCreate(
            ['flight_schedule_id' => $schedule->id, 'occupancy_type' => $type],
            [
                'gate_id' => $gate->id,
                'occupied_from' => $occupiedFrom,
                'occupied_until' => $occupiedUntil,
            ]
        );

        // Record Gate Swap Log if it was a change
        if ($oldGateId && $oldGateId !== $gate->id) {
            \App\Models\GateSwapLog::create([
                'flight_schedule_id' => $schedule->id,
                'old_gate_id' => $oldGateId,
                'new_gate_id' => $gate->id,
                'swapped_by' => auth()->id(),
                'reason' => 'Recovery / Dispatcher Action',
                'swap_type' => $type
            ]);

            event(new \App\Events\Operations\GateChanged(
                $schedule,
                \App\Models\AirportGate::find($oldGateId),
                $gate,
                $type
            ));
        }
    }
}
