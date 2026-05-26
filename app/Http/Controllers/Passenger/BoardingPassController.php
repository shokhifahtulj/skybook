<?php

namespace App\Http\Controllers\Passenger;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BookingSegment;

class BoardingPassController extends Controller
{
    public function show($id)
    {
        $segment = BookingSegment::with([
            'schedule.flight.route.origin',
            'schedule.flight.route.destination',
            'schedule.flight.airline',
            'schedule.flight.aircraft',
            'schedule.gate',
            'segmentPassengers.passenger',
            'segmentPassengers.ticket',
            'segmentPassengers.seat'
        ])->findOrFail($id);

        return view('passenger.boarding-pass.show', compact('segment'));
    }
}
