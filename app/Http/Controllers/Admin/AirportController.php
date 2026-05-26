<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Airport;
use App\Http\Requests\StoreAirportRequest;
use App\Http\Requests\UpdateAirportRequest;

class AirportController extends Controller
{
    public function index()
    {
        $airports = Airport::latest()->paginate(10);
        return view('admin.airports.index', compact('airports'));
    }

    public function create()
    {
        return view('admin.airports.create');
    }

    public function store(StoreAirportRequest $request)
    {
        Airport::create($request->validated());

        return redirect()->route('admin.airports.index')
            ->with('success', 'Bandara berhasil ditambahkan.');
    }

    public function edit(Airport $airport)
    {
        return view('admin.airports.edit', compact('airport'));
    }

    public function update(UpdateAirportRequest $request, Airport $airport)
    {
        $airport->update($request->validated());

        return redirect()->route('admin.airports.index')
            ->with('success', 'Data bandara berhasil diperbarui.');
    }

    public function destroy(Airport $airport)
    {
        $airport->delete();

        return redirect()->route('admin.airports.index')
            ->with('success', 'Bandara berhasil dihapus.');
    }
}