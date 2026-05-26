<?php

namespace App\Services\Operations;

use App\Models\BoardingPass;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BoardingPassPdfService
{
    /**
     * Men-generate dan menyimpan PDF Boarding Pass ke private storage
     */
    public function generatePdf(BoardingPass $boardingPass): string
    {
        $boardingPass->load(['segmentPassenger.passenger', 'segmentPassenger.seat', 'segmentPassenger.segment.schedule.flight.route.origin', 'segmentPassenger.segment.schedule.flight.route.destination']);

        // Generate barcode payload
        $qrPayload = config('app.url') . '/boarding-pass/verify/' . $boardingPass->id . '?signature=' . $boardingPass->qr_signature;

        $pdf = Pdf::loadView('pdf.boarding_pass', compact('boardingPass', 'qrPayload'))
                  ->setPaper('a5', 'portrait');

        $fileName = 'boarding_passes/' . $boardingPass->boarding_pass_number . '_' . Str::random(8) . '.pdf';
        
        Storage::disk('local')->put($fileName, $pdf->output());

        $boardingPass->update(['pdf_path' => $fileName]);

        return $fileName;
    }
}
