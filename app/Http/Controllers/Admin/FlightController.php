<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FlightStoreRequest;
use App\Http\Requests\Admin\FlightUpdateRequest;
use App\Models\Aircraft;
use App\Models\Airline;
use App\Models\Flight;
use App\Models\Route;
use Illuminate\Http\Request;

class FlightController extends Controller
{
    public function index(Request $request)
    {
        $query = Flight::with(['airline', 'route.origin', 'route.destination', 'aircraft'])
            ->withCount('schedules');

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('flight_number', 'like', "%{$search}%")
                    ->orWhereHas('airline', function ($airline) use ($search) {
                        $airline->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    })
                    ->orWhereHas('route.origin', function ($origin) use ($search) {
                        $origin->where('city', 'like', "%{$search}%")
                            ->orWhere('iata_code', 'like', "%{$search}%");
                    })
                    ->orWhereHas('route.destination', function ($destination) use ($search) {
                        $destination->where('city', 'like', "%{$search}%")
                            ->orWhere('iata_code', 'like', "%{$search}%");
                    });
            });
        }

        $sort = in_array($request->get('sort'), ['created_at', 'flight_number'], true)
            ? $request->get('sort')
            : 'created_at';
        $direction = strtolower((string) $request->get('direction')) === 'asc' ? 'asc' : 'desc';

        $flights = $query->orderBy($sort, $direction)->paginate(10)->withQueryString();

        return view('admin.flights.index', compact('flights'));
    }

    public function create()
    {
        $airlines = Airline::orderBy('name')->get();
        $routes = Route::with(['origin', 'destination'])->get();
        $aircrafts = Aircraft::orderBy('model')->get();

        return view('admin.flights.create', compact('airlines', 'routes', 'aircrafts'));
    }

    public function store(FlightStoreRequest $request)
    {
        Flight::create($request->validated());

        return redirect()->route('admin.flights.index')->with('success', 'Data Flight berhasil ditambahkan.');
    }

    public function edit(Flight $flight)
    {
        $airlines = Airline::orderBy('name')->get();
        $routes = Route::with(['origin', 'destination'])->get();
        $aircrafts = Aircraft::orderBy('model')->get();

        return view('admin.flights.edit', compact('flight', 'airlines', 'routes', 'aircrafts'));
    }

    public function update(FlightUpdateRequest $request, Flight $flight)
    {
        $flight->update($request->validated());

        return redirect()->route('admin.flights.index')->with('success', 'Data Flight berhasil diperbarui.');
    }

    public function destroy(Flight $flight)
    {
        $flight->delete();

        return redirect()->route('admin.flights.index')->with('success', 'Data Flight berhasil dihapus.');
    }
}