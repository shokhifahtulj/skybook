<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScheduleRequest;
use App\Models\Flight;
use App\Models\Schedule;

class AdminScheduleController extends Controller
{
    public function index()
    {
        $schedules = Schedule::with('flight')->orderBy('tanggal')->orderBy('jam_berangkat')->paginate(12);
        $flights = Flight::orderBy('maskapai')->get();

        return view('admin.schedules.index', compact('schedules', 'flights'));
    }

    public function store(ScheduleRequest $request)
    {
        Schedule::create($request->validated());

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal penerbangan berhasil ditambahkan.');
    }

    public function edit(Schedule $schedule)
    {
        $flights = Flight::orderBy('maskapai')->get();
        return view('admin.schedules.edit', compact('schedule', 'flights'));
    }

    public function update(ScheduleRequest $request, Schedule $schedule)
    {
        $schedule->update($request->validated());

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil dihapus.');
    }
}
