<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FlightSchedule;
use App\Models\Aircraft;
use App\Models\CrewMember;
use App\Models\FlightCrewAssignment;
use App\Services\Fleet\FleetAssignmentService;
use App\Services\Crew\CrewAssignmentService;
use App\Services\Operations\RecoveryEngineService;

class AssignmentController extends Controller
{
    protected $fleetService;
    protected $crewService;
    protected $recoveryService;

    public function __construct(FleetAssignmentService $fleetService, CrewAssignmentService $crewService, RecoveryEngineService $recoveryService)
    {
        $this->fleetService = $fleetService;
        $this->crewService = $crewService;
        $this->recoveryService = $recoveryService;
    }

    public function index(FlightSchedule $schedule)
    {
        $schedule->load(['flight.route', 'assignedAircraft', 'crewAssignments.crewMember', 'crewAssignments.role']);
        $availableAircrafts = Aircraft::where('operational_status', 'available')->get();
        // Just grab all available crew for simple MVP
        $availableCrews = CrewMember::where('operational_status', 'available')->with('role')->get();

        $suggestedAircrafts = collect();
        if (!$schedule->assignedAircraft) {
            $suggestedAircrafts = $this->recoveryService->suggestAircraftSwap($schedule);
        }

        $suggestedCrews = collect();
        // Check if we need crew (very basic check)
        $hasPic = $schedule->crewAssignments->where('role.name', 'PIC')->count() > 0;
        $hasCabin = $schedule->crewAssignments->where('role.name', 'Cabin Crew')->count() > 0;
        
        if (!$hasPic || !$hasCabin) {
            // Suggest crew regardless of role, but maybe we can just get a general pool
            $suggestedCrews = $this->recoveryService->suggestReserveCrew($schedule);
        }

        return view('admin.operations.assignment', compact('schedule', 'availableAircrafts', 'availableCrews', 'suggestedAircrafts', 'suggestedCrews'));
    }

    public function assignAircraft(Request $request, FlightSchedule $schedule)
    {
        $request->validate(['aircraft_id' => 'required|exists:aircrafts,id']);
        $aircraft = Aircraft::findOrFail($request->aircraft_id);

        try {
            $this->fleetService->assignAircraft($schedule, $aircraft, auth()->id());
            return back()->with('success', "Aircraft {$aircraft->model} assigned successfully.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function assignCrew(Request $request, FlightSchedule $schedule)
    {
        $request->validate(['crew_member_id' => 'required|exists:crew_members,id']);
        $crewMember = CrewMember::findOrFail($request->crew_member_id);

        try {
            $this->crewService->assignCrew($schedule, $crewMember, auth()->id());
            return back()->with('success', "Crew {$crewMember->crew_code} assigned successfully.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function unassignCrew(Request $request, FlightSchedule $schedule, FlightCrewAssignment $assignment)
    {
        try {
            $this->crewService->unassignCrew($assignment, auth()->id());
            return back()->with('success', 'Crew unassigned successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
