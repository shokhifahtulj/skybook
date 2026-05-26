<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aircraft;
use App\Services\SeatMapGeneratorService;
use Illuminate\Http\Request;

class AircraftSeatController extends Controller
{
    protected $generatorService;

    public function __construct(SeatMapGeneratorService $generatorService)
    {
        $this->generatorService = $generatorService;
    }

    public function index(Aircraft $aircraft)
    {
        $seats = $aircraft->seats()->orderBy('row_number')->orderBy('seat_letter')->get();
        $presets = config('seatmap.presets', []);
        
        return view('admin.aircrafts.seats', compact('aircraft', 'seats', 'presets'));
    }

    public function generate(Request $request, Aircraft $aircraft)
    {
        $request->validate([
            'preset' => 'required|string'
        ]);

        try {
            $this->generatorService->generateForAircraft($aircraft, $request->preset);
            return redirect()->route('admin.aircrafts.seats.index', $aircraft)
                ->with('success', 'Seat map berhasil digenerate berdasarkan preset ' . $request->preset);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membuat seat map: ' . $e->getMessage());
        }
    }

    public function destroyAll(Aircraft $aircraft)
    {
        $aircraft->seats()->delete();
        return back()->with('success', 'Semua layout kursi berhasil dihapus.');
    }
}
