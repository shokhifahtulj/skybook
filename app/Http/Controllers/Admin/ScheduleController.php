<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ScheduleRequest;
use App\Models\Flight;
use App\Models\FlightSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = FlightSchedule::with([
            'flight.airline',
            'flight.route.origin',
            'flight.route.destination',
            'flight.aircraft',
            'prices'
        ]);

        if ($request->filled('date')) {
            $query->whereDate('departure_datetime', $request->date);
        }

        if ($request->filled('airline_id')) {
            $query->whereHas('flight', function($q) use ($request) {
                $q->where('airline_id', $request->airline_id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->whereHas('flight', function($q) use ($request) {
                $q->where('flight_number', 'like', '%' . $request->search . '%');
            });
        }

        $schedules = $query->orderBy('departure_datetime')->paginate(15)->withQueryString();
        $airlines = \App\Models\Airline::orderBy('name')->get();

        return view('admin.schedules.index', compact('schedules', 'airlines'));
    }

    public function create()
    {
        $flights = Flight::with(['airline', 'route.origin', 'route.destination', 'aircraft'])->get();
        return view('admin.schedules.create', compact('flights'));
    }

    public function store(ScheduleRequest $request, \App\Services\Inventory\FlightSeatInventoryService $inventoryService)
    {
        DB::beginTransaction();
        try {
            $scheduleData = $request->validated();
            
            $totalQuota = 0;
            foreach ($scheduleData['prices'] as $priceData) {
                $totalQuota += (int) $priceData['quota'];
            }
            $scheduleData['available_seats'] = $totalQuota;
            $scheduleData['created_by'] = auth()->id();

            $schedule = FlightSchedule::create($scheduleData);

            foreach ($scheduleData['prices'] as $priceData) {
                $priceData['created_by'] = auth()->id();
                $schedule->prices()->create($priceData);
            }
            
            $inventoryService->generate($schedule);

            DB::commit();
            return redirect()->route('admin.schedules.index')->with('success', 'Jadwal penerbangan berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit(FlightSchedule $schedule)
    {
        $schedule->load('prices');
        $flights = Flight::with(['airline', 'route.origin', 'route.destination', 'aircraft'])->get();
        return view('admin.schedules.edit', compact('schedule', 'flights'));
    }

    public function update(ScheduleRequest $request, FlightSchedule $schedule, \App\Services\Inventory\FlightSeatInventoryService $inventoryService)
    {
        DB::beginTransaction();
        try {
            $scheduleData = $request->validated();
            
            $oldFlightId = $schedule->flight_id;
            
            $totalQuota = 0;
            foreach ($scheduleData['prices'] as $priceData) {
                $totalQuota += (int) $priceData['quota'];
            }
            $scheduleData['available_seats'] = $totalQuota;
            $scheduleData['updated_by'] = auth()->id();

            $schedule->update($scheduleData);

            $schedule->prices()->delete();
            foreach ($scheduleData['prices'] as $priceData) {
                $priceData['created_by'] = $schedule->created_by;
                $priceData['updated_by'] = auth()->id();
                $schedule->prices()->create($priceData);
            }
            
            if ($oldFlightId !== $schedule->flight_id) {
                $inventoryService->regenerate($schedule);
            }

            DB::commit();
            return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(FlightSchedule $schedule)
    {
        $schedule->delete();
        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil dihapus.');
    }
}
