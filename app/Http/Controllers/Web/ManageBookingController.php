<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\Booking\BookingLookupService;
use App\Models\Booking;
use Illuminate\Http\Request;
use Exception;

class ManageBookingController extends Controller
{
    protected $lookupService;

    public function __construct(BookingLookupService $lookupService)
    {
        $this->lookupService = $lookupService;
    }

    /**
     * Show the lookup form.
     */
    public function index()
    {
        return view('manage-booking.index');
    }

    /**
     * Process lookup and redirect to signed portal URL.
     */
    public function lookup(Request $request)
    {
        $request->validate([
            'pnr' => 'required|string|size:6',
            'last_name' => 'required|string|max:255',
        ]);

        try {
            $url = $this->lookupService->generatePortalAccess($request->pnr, $request->last_name);
            return redirect()->to($url);
        } catch (Exception $e) {
            return back()->withInput()->withErrors(['lookup' => $e->getMessage()]);
        }
    }

    /**
     * Show the passenger portal. (Must be accessed via signed URL).
     */
    public function portal(Request $request, $pnr)
    {
        if (!$request->hasValidSignature()) {
            abort(401, 'Sesi akses Manage Booking Anda tidak valid atau sudah kadaluarsa.');
        }

        $booking = Booking::with([
            'segments.schedule.flight.route.origin',
            'segments.schedule.flight.route.destination',
            'segments.schedule.flight.airline',
            'passengers',
            'segments.segmentPassengers.ticket'
        ])->where('pnr', strtoupper($pnr))->firstOrFail();

        return view('manage-booking.portal', compact('booking'));
    }
}
