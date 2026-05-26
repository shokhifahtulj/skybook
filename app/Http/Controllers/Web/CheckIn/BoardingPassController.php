<?php

namespace App\Http\Controllers\Web\CheckIn;

use App\Http\Controllers\Controller;
use App\Models\BoardingPass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BoardingPassController extends Controller
{
    /**
     * Mengunduh file PDF Boarding Pass (dilindungi oleh middleware auth atau signature di masa depan)
     */
    public function download($uuid)
    {
        $boardingPass = BoardingPass::where('id', $uuid)->firstOrFail();

        if (!$boardingPass->pdf_path || !Storage::disk('local')->exists($boardingPass->pdf_path)) {
            return back()->with('error', 'Dokumen Boarding Pass belum siap. Silakan muat ulang halaman ini dalam beberapa saat.');
        }

        return Storage::disk('local')->download($boardingPass->pdf_path);
    }

    /**
     * Menampilkan antarmuka verifikasi/scan untuk Gate Agent
     */
    public function verify(Request $request, $uuid)
    {
        $signature = $request->query('signature');
        $boardingPass = BoardingPass::with([
            'segmentPassenger.passenger', 
            'segmentPassenger.seat', 
            'segmentPassenger.segment.schedule.flight.route.origin', 
            'segmentPassenger.segment.schedule.flight.route.destination'
        ])->where('id', $uuid)->firstOrFail();

        return view('passenger.checkin.boarding_pass_verify', compact('boardingPass', 'signature'));
    }
}
