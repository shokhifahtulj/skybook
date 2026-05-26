<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Http\Controllers\Controller;
use App\Models\FlightSchedule;
use App\Services\Irops\DelayManagementService;
use App\Services\Irops\FlightCancellationService;
use App\Services\Irops\GateChangeService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class IropsController extends Controller
{
    protected $delayService;
    protected $gateService;
    protected $cancellationService;

    public function __construct(
        DelayManagementService $delayService,
        GateChangeService $gateService,
        FlightCancellationService $cancellationService
    ) {
        $this->delayService = $delayService;
        $this->gateService = $gateService;
        $this->cancellationService = $cancellationService;
    }

    public function delay(Request $request, FlightSchedule $schedule)
    {
        $request->validate([
            'delay_minutes' => 'required|integer|min:5|max:1440',
            'reason' => 'nullable|string|max:255',
        ]);

        try {
            $newDeparture = Carbon::parse($schedule->departure_datetime)->addMinutes($request->delay_minutes);
            $this->delayService->declareDelay($schedule, $newDeparture, $request->delay_minutes, 'manual', $request->reason);
            return back()->with('success', "Flight delayed by {$request->delay_minutes} minutes successfully.");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delay flight: ' . $e->getMessage());
        }
    }

    public function changeGate(Request $request, FlightSchedule $schedule)
    {
        $request->validate([
            'new_gate' => 'required|string|max:10',
            'reason' => 'nullable|string|max:255',
        ]);

        try {
            $this->gateService->changeGate($schedule, $request->new_gate, $request->reason);
            return back()->with('success', "Gate changed to {$request->new_gate} successfully.");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to change gate: ' . $e->getMessage());
        }
    }

    public function cancel(Request $request, FlightSchedule $schedule)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        try {
            $this->cancellationService->cancelFlight($schedule, $request->reason);
            return back()->with('success', "Flight cancelled successfully.");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to cancel flight: ' . $e->getMessage());
        }
    }
}
