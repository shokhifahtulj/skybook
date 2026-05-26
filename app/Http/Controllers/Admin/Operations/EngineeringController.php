<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Aircraft;
use App\Models\AircraftMaintenanceEvent;
use App\Services\Operations\EngineeringControlService;

class EngineeringController extends Controller
{
    protected $engineeringService;

    public function __construct(EngineeringControlService $engineeringService)
    {
        $this->engineeringService = $engineeringService;
    }

    public function index()
    {
        $aircrafts = Aircraft::with(['airline', 'maintenanceEvents' => function($query) {
            $query->whereIn('status', ['planned', 'in_progress'])->orderBy('start_at', 'asc');
        }])->get();

        $activeMaintenances = AircraftMaintenanceEvent::whereIn('status', ['planned', 'in_progress'])
            ->with('aircraft')
            ->orderBy('start_at', 'asc')
            ->get();

        return view('admin.operations.engineering.index', compact('aircrafts', 'activeMaintenances'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'aircraft_id' => 'required|exists:aircrafts,id',
            'maintenance_type' => 'required|in:scheduled,unscheduled,aog,inspection,line_maintenance,heavy_maintenance',
            'status' => 'required|in:planned,in_progress,completed,deferred',
            'severity' => 'required|in:minor,major,critical',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
            'notes' => 'nullable|string',
        ]);

        $aircraft = Aircraft::findOrFail($validated['aircraft_id']);
        
        $this->engineeringService->groundAircraft($aircraft, $validated, auth()->id());

        return back()->with('success', 'Aircraft maintenance scheduled successfully.');
    }

    public function release(Request $request, AircraftMaintenanceEvent $maintenance)
    {
        $validated = $request->validate([
            'resolution' => 'nullable|string',
        ]);

        $this->engineeringService->releaseAircraft($maintenance, auth()->id(), $validated['resolution']);

        return back()->with('success', 'Aircraft dispatch released from maintenance successfully.');
    }
}
