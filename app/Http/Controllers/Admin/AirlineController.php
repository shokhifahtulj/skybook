<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Airline;
use App\Http\Requests\StoreAirlineRequest;
use App\Http\Requests\UpdateAirlineRequest;
use Illuminate\Support\Facades\Storage;

class AirlineController extends Controller
{
    public function index()
    {
        $airlines = Airline::latest()->paginate(10);
        return view('admin.airlines.index', compact('airlines'));
    }

    public function create()
    {
        return view('admin.airlines.create');
    }

    public function store(StoreAirlineRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('airlines', 'public');
        }

        Airline::create($data);

        return redirect()->route('admin.airlines.index')
            ->with('success', 'Maskapai berhasil ditambahkan.');
    }

    public function edit(Airline $airline)
    {
        return view('admin.airlines.edit', compact('airline'));
    }

    public function update(UpdateAirlineRequest $request, Airline $airline)
    {
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            if ($airline->logo) {
                Storage::disk('public')->delete($airline->logo);
            }
            $data['logo'] = $request->file('logo')->store('airlines', 'public');
        }

        $airline->update($data);

        return redirect()->route('admin.airlines.index')
            ->with('success', 'Data maskapai berhasil diperbarui.');
    }

    public function destroy(Airline $airline)
    {
        if ($airline->logo) {
            Storage::disk('public')->delete($airline->logo);
        }
        
        $airline->delete();

        return redirect()->route('admin.airlines.index')
            ->with('success', 'Maskapai berhasil dihapus.');
    }
}