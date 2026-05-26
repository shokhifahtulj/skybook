<?php

namespace App\Http\Controllers\Web\CheckIn;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Notification;
use App\Services\Operations\CheckInService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class PassengerCheckInController extends Controller
{
    public function process(Request $request, $pnr, CheckInService $checkInService)
    {
        if (! $request->hasValidSignature()) {
            abort(401, 'Link sesi Check-in telah kedaluwarsa atau tidak valid.');
        }

        $request->validate([
            'passenger_segment_ids' => 'required|array',
            'passenger_segment_ids.*' => 'required|uuid',
        ]);

        $successCount = 0;
        $errors = [];
        $notifiedBookings = [];

        foreach ($request->passenger_segment_ids as $spId) {
            try {
                $segmentPassenger = $checkInService->checkIn($spId);
                $successCount++;

                $booking = $segmentPassenger->segment?->booking;
                $userId = $booking?->user_id ?? $booking?->booked_by;

                if ($userId && ! in_array($booking->id, $notifiedBookings, true)) {
                    Notification::create([
                        'user_id' => $userId,
                        'title' => 'Check-in completed',
                        'message' => 'Your check-in for booking ' . $booking->pnr . ' has been completed successfully.',
                        'type' => 'checkin_completed',
                        'is_read' => false,
                    ]);

                    $notifiedBookings[] = $booking->id;
                }
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        if ($successCount > 0) {
            $url = URL::temporarySignedRoute(
                'checkin.confirmation',
                now()->addHours(1),
                ['pnr' => $pnr]
            );

            return redirect($url)->with('success', "$successCount penumpang berhasil di check-in.");
        }

        return back()->with('error', implode('<br>', $errors));
    }

    public function confirmation(Request $request, $pnr)
    {
        if (! $request->hasValidSignature()) {
            abort(401, 'Link sesi Check-in telah kedaluwarsa atau tidak valid.');
        }

        $booking = Booking::where('pnr', $pnr)->firstOrFail();

        return view('passenger.checkin.confirmation', compact('booking'));
    }
}
