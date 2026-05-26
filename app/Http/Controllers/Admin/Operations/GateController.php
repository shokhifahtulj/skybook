<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Airport;
use App\Models\AirportGate;
use App\Models\FlightSchedule;
use App\Services\Operations\GateManagementService;

class GateController extends Controller
{
    protected $gateService;

    public function __construct(GateManagementService $gateService)
    {
        $this->gateService = $gateService;
    }

    public function index(Request $request)
    {
        // For MVP, just get the first airport or the selected one
        $airports = Airport::all();
        $selectedAirport = $request->airport_id ? Airport::find($request->airport_id) : $airports->first();

        $gates = collect();
        if ($selectedAirport) {
            $gates = AirportGate::where('airport_id', $selectedAirport->id)
                ->with(['departureSchedules' => function ($query) {
                    $query->whereDate('departure_datetime', now()->toDateString())
                          ->orderBy('departure_datetime', 'asc')
                          ->with('flight.route.destination');
                }, 'arrivalSchedules' => function ($query) {
                    $query->whereDate('arrival_datetime', now()->toDateString())
                          ->orderBy('arrival_datetime', 'asc')
                          ->with('flight.route.origin');
                }])
                ->get();
        }

        return view('admin.operations.gates.index', compact('airports', 'selectedAirport', 'gates'));
    }

    public function swap(Request $request, FlightSchedule $schedule)
    {
        $request->validate([
            'gate_id' => 'required|exists:airport_gates,id',
            'type' => 'required|in:departure,arrival'
        ]);

        $gate = AirportGate::findOrFail($request->gate_id);

        try {
            $this->gateService->assignGate($schedule, $gate, $request->type);
            return back()->with('success', "Gate assigned successfully.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
