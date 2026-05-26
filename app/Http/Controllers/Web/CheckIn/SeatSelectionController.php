<?php

namespace App\Http\Controllers\Web\CheckIn;

use App\Http\Controllers\Controller;
use App\Models\BookingSegmentPassenger;
use App\Models\FlightScheduleSeat;
use App\Services\Inventory\FlightSeatInventoryService;
use App\Services\Operations\SeatChangeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class SeatSelectionController extends Controller
{
    public function show(Request $request, $pnr, $passenger_id, FlightSeatInventoryService $flightSeatInventoryService)
    {
        if (!$request->hasValidSignature()) {
            abort(401, 'Link sesi Check-in telah kedaluwarsa atau tidak valid.');
        }

        $sp = BookingSegmentPassenger::with(['segment.schedule', 'seat', 'passenger'])
            ->where('id', $passenger_id)
            ->firstOrFail();

        $flightScheduleId = $sp->segment->flight_schedule_id;
        $schedule = $sp->segment->schedule;

        if ($schedule) {
            $flightSeatInventoryService->generate($schedule);
        }

        $seats = FlightScheduleSeat::where('flight_schedule_id', $flightScheduleId)
            ->orderBy('seat_number')
            ->get();

        return view('passenger.checkin.seatmap', compact('sp', 'seats', 'pnr'));
    }

    public function update(Request $request, $pnr, $passenger_id, SeatChangeService $seatChangeService)
    {
        if (!$request->hasValidSignature()) {
            abort(401, 'Link sesi Check-in telah kedaluwarsa atau tidak valid.');
        }

        $request->validate([
            'new_seat' => ['required', 'string', 'max:10'],
        ]);

        try {
            $seatChangeService->changeSeat($passenger_id, $request->string('new_seat')->trim());

            $url = URL::temporarySignedRoute(
                'checkin.portal',
                now()->addHours(2),
                ['pnr' => $pnr]
            );

            return redirect($url)->with('success', 'Kursi berhasil ditukar.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
