<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SimulationSession;
use App\Models\FlightSchedule;
use App\Models\OperationalLog;
use App\Services\Simulation\IropsSandboxService;

class SimulationController extends Controller
{
    protected $sandbox;

    public function __construct(IropsSandboxService $sandbox)
    {
        $this->sandbox = $sandbox;
    }

    public function index()
    {
        $activeSession = SimulationSession::where('status', 'ACTIVE')->first();
        $sessions = SimulationSession::orderBy('created_at', 'desc')->get();
        
        $heatmapData = FlightSchedule::with(['flight.route.origin', 'flight.route.destination'])->get();

        return view('admin.operations.simulation.index', compact('activeSession', 'sessions', 'heatmapData'));
    }

    public function start(Request $request)
    {
        $request->validate([
            'scenario_seed' => 'required|string',
        ]);

        // Check if an active session exists
        if (SimulationSession::where('status', 'ACTIVE')->exists()) {
            return back()->with('error', 'There is already an active simulation running. Please restore baseline first.');
        }

        $sessionName = 'Sim-' . now()->format('YmdHi') . '-' . $request->scenario_seed;
        $session = $this->sandbox->createBaseline($sessionName, $request->scenario_seed);
        
        $this->sandbox->injectChaos($session);

        return back()->with('success', 'Simulation started! Baseline saved and chaos injected.');
    }

    public function restore(SimulationSession $session)
    {
        if ($session->status !== 'ACTIVE') {
            return back()->with('error', 'Session is already restored.');
        }

        $this->sandbox->restoreBaseline($session);

        return back()->with('success', 'Baseline restored successfully. Audit logs have been retained.');
    }

    public function replay(SimulationSession $session)
    {
        // Get unified logs between started_at and ended_at (or now if active)
        $endTime = $session->ended_at ?? now();

        $logs = OperationalLog::whereBetween('created_at', [$session->started_at, $endTime])
            ->orderBy('sequence', 'asc') // or time
            ->get();

        return view('admin.operations.simulation.replay', compact('session', 'logs'));
    }
}
