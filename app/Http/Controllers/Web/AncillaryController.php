<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingSegmentPassenger;
use App\Services\Ancillary\AncillaryCatalogService;
use App\Services\Ancillary\AncillaryPaymentService;
use App\Services\Ancillary\AncillaryPurchaseService;
use Illuminate\Http\Request;

class AncillaryController extends Controller
{
    protected $catalogService;
    protected $purchaseService;
    protected $paymentService;

    public function __construct(
        AncillaryCatalogService $catalogService,
        AncillaryPurchaseService $purchaseService,
        AncillaryPaymentService $paymentService
    ) {
        $this->catalogService = $catalogService;
        $this->purchaseService = $purchaseService;
        $this->paymentService = $paymentService;
    }

    public function catalog($pnr, Request $request)
    {
        // Simple verification
        if (!$request->hasValidSignature()) {
            abort(401);
        }

        $booking = Booking::where('pnr', $pnr)->firstOrFail();
        $catalog = $this->catalogService->getAvailableCatalog();

        return view('manage-booking.ancillary.catalog', compact('booking', 'catalog'));
    }

    public function store($pnr, Request $request)
    {
        if (!$request->hasValidSignature()) {
            abort(401);
        }

        $request->validate([
            'passenger_id' => 'required|uuid',
            'service_code' => 'required|string',
        ]);

        $booking = Booking::where('pnr', $pnr)->firstOrFail();
        
        // Find segment passenger
        $segmentPassenger = BookingSegmentPassenger::whereHas('bookingSegment', function($q) use ($booking) {
            $q->where('booking_id', $booking->id);
        })->where('passenger_id', $request->passenger_id)->firstOrFail();

        $service = $this->catalogService->getServiceByCode($request->service_code);

        // Add Ancillary
        $ancillary = $this->purchaseService->addAncillary($segmentPassenger, $service);

        // Process Payment (Simulated MVP)
        $this->paymentService->processPayment($ancillary);

        return redirect()->route('manage-booking.portal', ['pnr' => $booking->pnr])->with('success', 'Layanan tambahan berhasil ditambahkan dan dibayar!');
    }
}
