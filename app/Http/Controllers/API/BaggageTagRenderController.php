<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BaggageTag;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BaggageTagRenderController extends Controller
{
    /**
     * Render baggage tag as PDF (Thermal size 4x6 inches or similar)
     */
    public function render(BaggageTag $tag)
    {
        $tag->load([
            'ancillary.bookingSegmentPassenger.passenger',
            'ancillary.bookingSegmentPassenger.bookingSegment.flightSchedule.flight.route.origin',
            'ancillary.bookingSegmentPassenger.bookingSegment.flightSchedule.flight.route.destination',
        ]);

        $passenger = $tag->ancillary->bookingSegmentPassenger->passenger;
        $flight = $tag->ancillary->bookingSegmentPassenger->bookingSegment->flightSchedule->flight;
        
        $qrCode = base64_encode(QrCode::format('svg')->size(150)->generate($tag->signature));

        $pdf = Pdf::loadView('baggage.tag_pdf', [
            'tag' => $tag,
            'passenger' => $passenger,
            'flight' => $flight,
            'qrCode' => $qrCode,
        ]);

        // Standard thermal shipping label size is usually 4x6 inches (288x432 pts)
        // A long luggage tag might be e.g. 2 x 10 inches or 1.5 x 8 inches.
        // Let's use custom 1.5 x 8 inches -> roughly 108 x 576 pts
        $pdf->setPaper([0, 0, 108, 576], 'landscape');

        return $pdf->stream("baggage_tag_{$tag->tag_number}.pdf");
    }
}
