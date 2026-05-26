<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FlightSchedule;
use App\Services\Operations\PassengerReaccommodationService;

class ReaccommodationController extends Controller
{
    protected $reaccommodationService;

    public function __construct(PassengerReaccommodationService $reaccommodationService)
    {
        $this->reaccommodationService = $reaccommodationService;
    }

    public function index()
    {
        // Get disrupted flights (cancelled or severely delayed > 120 mins)
        // For MVP, we'll just look for cancelled schedules that still have passengers booked
        $disruptedSchedules = FlightSchedule::where('status', 'cancelled')
            ->whereHas('seats.bookingSegmentPassengers', function ($q) {
                // Not reassigned
                $q->whereDoesntHave('reassignments');
            })
            ->with(['flight.route', 'seats.bookingSegmentPassengers' => function ($q) {
                $q->whereDoesntHave('reassignments');
            }])
            ->get();

        foreach ($disruptedSchedules as $schedule) {
            $schedule->affected_passengers_count = $schedule->seats->sum(function($seat) {
                return $seat->bookingSegmentPassengers->count();
            });
            $schedule->rebooking_candidates = $this->reaccommodationService->getRebookingCandidates($schedule);
        }

        return view('admin.operations.reaccommodation.index', compact('disruptedSchedules'));
    }

    public function rebook(Request $request, FlightSchedule $disruptedSchedule)
    {
        $request->validate([
            'new_schedule_id' => 'required|exists:flight_schedules,id'
        ]);

        $newSchedule = FlightSchedule::findOrFail($request->new_schedule_id);

        try {
            $count = $this->reaccommodationService->rebookPassengers($disruptedSchedule, $newSchedule, 'IROPS Rebooking (Auto)');
            return back()->with('success', "Successfully rebooked {$count} passengers.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function intelligence(FlightSchedule $schedule, \App\Services\Intelligence\RecoveryIntelligenceService $aiService)
    {
        // Get or generate recommendations
        $recommendation = \App\Models\RecoveryRecommendation::where('flight_schedule_id', $schedule->id)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$recommendation) {
            $recommendation = $aiService->generateRecommendations($schedule);
        }

        return view('admin.operations.reaccommodation.intelligence_partial', compact('schedule', 'recommendation'));
    }
}
