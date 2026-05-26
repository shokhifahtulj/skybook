<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aircraft;
use App\Models\Airline;
use Illuminate\Http\Request;

class AircraftController extends Controller
{
    public function index()
    {
        $aircrafts = Aircraft::with('airline')->latest()->paginate(10);
        return view('admin.aircrafts.index', compact('aircrafts'));
    }

    public function create()
    {
        $airlines = Airline::orderBy('name')->get();
        return view('admin.aircrafts.create', compact('airlines'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'airline_id' => 'required|exists:airlines,id',
            'model' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'seat_layout' => 'required|string|max:20',
        ]);

        Aircraft::create($validated);

        return redirect()->route('admin.aircrafts.index')->with('success', 'Data Pesawat berhasil ditambahkan.');
    }

    public function edit(Aircraft $aircraft)
    {
        $airlines = Airline::orderBy('name')->get();
        return view('admin.aircrafts.edit', compact('aircraft', 'airlines'));
    }

    public function update(Request $request, Aircraft $aircraft)
    {
        $validated = $request->validate([
            'airline_id' => 'required|exists:airlines,id',
            'model' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'seat_layout' => 'required|string|max:20',
        ]);

        $aircraft->update($validated);

        return redirect()->route('admin.aircrafts.index')->with('success', 'Data Pesawat berhasil diperbarui.');
    }

    public function destroy(Aircraft $aircraft)
    {
        $aircraft->delete();
        return redirect()->route('admin.aircrafts.index')->with('success', 'Data Pesawat berhasil dihapus.');
    }
}