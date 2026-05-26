<?php

namespace App\Http\Controllers;

use App\Models\Flight;
use Illuminate\Http\Request;

class FlightController extends Controller
{
    public function index(Request $request)
    {
        $filters = array_filter($request->only(['origin', 'destination', 'departure_date', 'airline']), function ($value) {
            return $value !== null && $value !== '';
        });

        $flights = Flight::query()
            ->with([
                'route.origin',
                'route.destination',
                'airline',
                'schedules' => function ($query) {
                    $query->where('departure_datetime', '>=', now())
                        ->orderBy('departure_datetime');
                },
            ])
            ->when($filters['origin'] ?? null, function ($query, $origin) {
                $query->whereHas('route.origin', function ($airportQuery) use ($origin) {
                    $airportQuery->where('city', 'like', "%{$origin}%")
                        ->orWhere('iata_code', 'like', "%{$origin}%")
                        ->orWhere('name', 'like', "%{$origin}%");
                });
            })
            ->when($filters['destination'] ?? null, function ($query, $destination) {
                $query->whereHas('route.destination', function ($airportQuery) use ($destination) {
                    $airportQuery->where('city', 'like', "%{$destination}%")
                        ->orWhere('iata_code', 'like', "%{$destination}%")
                        ->orWhere('name', 'like', "%{$destination}%");
                });
            })
            ->when($filters['departure_date'] ?? null, function ($query, $departureDate) {
                $query->whereHas('schedules', function ($scheduleQuery) use ($departureDate) {
                    $scheduleQuery->whereDate('departure_datetime', $departureDate);
                });
            })
            ->when($filters['airline'] ?? null, function ($query, $airline) {
                $query->whereHas('airline', function ($airlineQuery) use ($airline) {
                    $airlineQuery->where('name', 'like', "%{$airline}%")
                        ->orWhere('code', 'like', "%{$airline}%");
                });
            })
            ->paginate(10)
            ->withQueryString();

        return view('flights.index', compact('flights', 'filters'));
    }

    public function show(Flight $flight)
    {
        $flight->load([
            'route.origin',
            'route.destination',
            'airline',
            'schedules' => function ($query) {
                $query->where('departure_datetime', '>=', now())
                    ->orderBy('departure_datetime');
            },
        ]);

        return view('flights.show', compact('flight'));
    }
}
