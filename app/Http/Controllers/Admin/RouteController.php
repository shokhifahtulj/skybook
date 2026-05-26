<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RouteStoreRequest;
use App\Http\Requests\Admin\RouteUpdateRequest;
use App\Models\Airport;
use App\Models\Route;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    public function index(Request $request)
    {
        $query = Route::with(['origin', 'destination']);

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->whereHas('origin', function ($origin) use ($search) {
                    $origin->where('city', 'like', "%{$search}%")
                        ->orWhere('iata_code', 'like', "%{$search}%");
                })
                    ->orWhereHas('destination', function ($destination) use ($search) {
                        $destination->where('city', 'like', "%{$search}%")
                            ->orWhere('iata_code', 'like', "%{$search}%");
                    });
            });
        }

        $sort = in_array($request->get('sort'), ['created_at', 'distance', 'estimated_duration'], true)
            ? $request->get('sort')
            : 'created_at';
        $direction = strtolower((string) $request->get('direction')) === 'asc' ? 'asc' : 'desc';

        $routes = $query->orderBy($sort, $direction)->paginate(10)->withQueryString();

        return view('admin.routes.index', compact('routes'));
    }

    public function create()
    {
        $airports = Airport::orderBy('city')->get();

        return view('admin.routes.create', compact('airports'));
    }

    public function store(RouteStoreRequest $request)
    {
        Route::create($request->validated());

        return redirect()->route('admin.routes.index')->with('success', 'Data Rute berhasil ditambahkan.');
    }

    public function edit(Route $route)
    {
        $airports = Airport::orderBy('city')->get();

        return view('admin.routes.edit', compact('route', 'airports'));
    }

    public function update(RouteUpdateRequest $request, Route $route)
    {
        $route->update($request->validated());

        return redirect()->route('admin.routes.index')->with('success', 'Data Rute berhasil diperbarui.');
    }

    public function destroy(Route $route)
    {
        $route->delete();

        return redirect()->route('admin.routes.index')->with('success', 'Data Rute berhasil dihapus.');
    }
}