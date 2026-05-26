<?php

namespace App\Http\Controllers\Web\CheckIn;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\Operations\CheckInEligibilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class WebCheckInController extends Controller
{
    public function index()
    {
        return view('passenger.checkin.lookup');
    }

    public function lookup(Request $request)
    {
        $request->validate([
            'pnr' => 'required|string|size:6',
            'last_name' => 'required|string'
        ]);

        $booking = Booking::with('passengers')
            ->where('pnr', strtoupper($request->pnr))
            ->first();

        if (!$booking) {
            return back()->with('error', 'Booking tidak ditemukan.');
        }

        // Validasi last_name
        $lastNameMatch = $booking->passengers->contains(function ($passenger) use ($request) {
            return strtolower($passenger->last_name) === strtolower($request->last_name);
        });

        if (!$lastNameMatch) {
            return back()->with('error', 'Kombinasi PNR dan Nama Belakang tidak sesuai.');
        }

        // Generate temporary signed URL
        $url = URL::temporarySignedRoute(
            'checkin.portal',
            now()->addHours(2), // 2 jam session
            ['pnr' => $booking->pnr]
        );

        return redirect($url);
    }

    public function passengers(Request $request, $pnr)
    {
        if (!$request->hasValidSignature()) {
            abort(401, 'Link sesi Check-in telah kedaluwarsa atau tidak valid.');
        }

        $booking = Booking::with([
            'passengers',
            'segments.schedule.flight.route.origin',
            'segments.schedule.flight.route.destination',
            'segments.segmentPassengers.ticket',
            'segments.segmentPassengers.seat'
        ])->where('pnr', $pnr)->firstOrFail();

        $eligibilityService = app(CheckInEligibilityService::class);
        $passengersStatus = [];

        foreach ($booking->segments as $segment) {
            foreach ($segment->segmentPassengers as $sp) {
                [$isEligible, $reason] = $eligibilityService->validateEligibility($sp);
                $passengersStatus[$sp->id] = [
                    'sp' => $sp,
                    'is_eligible' => $isEligible,
                    'reason' => $reason
                ];
            }
        }

        return view('passenger.checkin.passengers', compact('booking', 'passengersStatus'));
    }
}
