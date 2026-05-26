<?php

namespace App\Services\Intelligence;

use App\Models\FlightSchedule;
use App\Models\RecoveryRecommendation;

class RecoveryIntelligenceService
{
    /**
     * Generate heuristic recommendations for a disrupted schedule
     */
    public function generateRecommendations(FlightSchedule $schedule, ?int $sessionId = null): RecoveryRecommendation
    {
        $strandedPaxCount = $schedule->seats->sum(function($seat) {
            return $seat->bookingSegmentPassengers()->doesntHave('reassignments')->count();
        });

        // Heuristic Strategy 1: Delay + Gate Swap + Rebook
        // Assumptions: Accepting a delay is cheaper but has pax impact. Rebooking some pax helps.
        $strat1 = [
            'id' => 'STRAT_DELAY_REBOOK',
            'title' => 'Delay + Rebook Passengers',
            'description' => 'Delay flight by 45m and rebook critical passengers to next available flight.',
            'breakdown' => [
                'passenger_impact' => - ($strandedPaxCount * 15), // Less penalty because some rebooked
                'crew_risk' => -30,
                'gate_availability' => +20,
                'operational_cost' => -25,
                'notification_stability' => +15,
            ],
            'reasoning' => [
                '[+] Keeps aircraft in rotation',
                '[+] Resolves gate conflict by waiting',
                '[-] High passenger inconvenience',
                '[-] Moderate crew legality risk due to extension'
            ]
        ];
        $strat1['total_score'] = array_sum($strat1['breakdown']);

        // Heuristic Strategy 2: Aircraft Swap
        $strat2 = [
            'id' => 'STRAT_AIRCRAFT_SWAP',
            'title' => 'Aircraft Swap',
            'description' => 'Swap to standby aircraft. Minimizes delay but incurs operational repositioning cost.',
            'breakdown' => [
                'passenger_impact' => -5,
                'crew_risk' => -10,
                'gate_availability' => -15, // Might need towing
                'operational_cost' => -80, // Expensive
                'notification_stability' => +40,
            ],
            'reasoning' => [
                '[+] Lowest passenger disruption',
                '[+] Low crew risk',
                '[-] Very high operational cost',
                '[-] Requires towing / gate coordination'
            ]
        ];
        $strat2['total_score'] = array_sum($strat2['breakdown']);

        // Heuristic Strategy 3: Full Cancellation
        $strat3 = [
            'id' => 'STRAT_CANCELLATION',
            'title' => 'Full Cancellation',
            'description' => 'Cancel flight and mass rebook all passengers. Use only as last resort.',
            'breakdown' => [
                'passenger_impact' => - ($strandedPaxCount * 40),
                'crew_risk' => +50, // Resets crew duty
                'gate_availability' => +40, // Frees up gate
                'operational_cost' => -100, // Lost revenue & compensation
                'notification_stability' => -50,
            ],
            'reasoning' => [
                '[+] Solves crew duty limit issues entirely',
                '[+] Frees up gate capacity immediately',
                '[-] Massive passenger disruption and compensation costs',
                '[-] Negative brand impact'
            ]
        ];
        $strat3['total_score'] = array_sum($strat3['breakdown']);

        $strategies = collect([$strat1, $strat2, $strat3])->sortByDesc('total_score')->values()->toArray();

        // Record it
        return RecoveryRecommendation::create([
            'flight_schedule_id' => $schedule->id,
            'simulation_session_id' => $sessionId,
            'recommendation_payload' => $strategies,
        ]);
    }
}
