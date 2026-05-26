<?php

namespace App\Http\Controllers;

use App\Http\Requests\FlightRequest;
use App\Models\Flight;
use Illuminate\Http\Request;

class AdminFlightController extends Controller
{
    public function index()
    {
        $flights = Flight::withCount('schedules')->latest()->paginate(10);

        return view('admin.flights.index', compact('flights'));
    }

    public function create()
    {
        return view('admin.flights.create');
    }

    public function store(FlightRequest $request)
    {
        Flight::create($request->validated());

        return redirect()->route('admin.flights.index')->with('success', 'Penerbangan berhasil ditambahkan.');
    }

    public function edit(Flight $flight)
    {
        return view('admin.flights.edit', compact('flight'));
    }

    public function update(FlightRequest $request, Flight $flight)
    {
        $flight->update($request->validated());

        return redirect()->route('admin.flights.index')->with('success', 'Data penerbangan berhasil diperbarui.');
    }

    public function destroy(Flight $flight)
    {
        $flight->delete();

        return redirect()->route('admin.flights.index')->with('success', 'Penerbangan berhasil dihapus.');
    }
}
