<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Http\Controllers\Controller;
use App\Models\BookingPassengerAncillary;
use App\Services\Operations\BaggageTagService;
use Illuminate\Http\Request;

class BaggageController extends Controller
{
    protected $tagService;

    public function __construct(BaggageTagService $tagService)
    {
        $this->tagService = $tagService;
    }

    public function dropCounter(Request $request)
    {
        $search = $request->input('search');
        $ancillaries = collect();

        if ($search) {
            $ancillaries = BookingPassengerAncillary::with([
                    'bookingSegmentPassenger.passenger',
                    'bookingSegmentPassenger.bookingSegment.flightSchedule.flight.route.origin',
                    'bookingSegmentPassenger.bookingSegment.flightSchedule.flight.route.destination',
                    'bookingSegmentPassenger.booking',
                    'baggageTags'
                ])
                ->where('type', 'baggage')
                ->where('status', 'paid')
                ->whereHas('bookingSegmentPassenger.booking', function ($q) use ($search) {
                    $q->where('pnr', strtoupper($search));
                })
                ->get();
        }

        return view('admin.operations.baggage_drop', compact('ancillaries', 'search'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'ancillary_id' => 'required|uuid',
            'weight_kg' => 'required|numeric|min:1',
        ]);

        $ancillary = BookingPassengerAncillary::findOrFail($request->ancillary_id);

        try {
            $tag = $this->tagService->generateTag($ancillary, $request->weight_kg);
            
            // Auto open the PDF in new tab
            return redirect()->back()->with('success', 'Baggage tag generated successfully.')->with('open_pdf', route('api.baggage-tags.render', $tag->id));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
