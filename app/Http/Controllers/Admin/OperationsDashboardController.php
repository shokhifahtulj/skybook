<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FlightSchedule;
use App\Services\Operations\OperationsDashboardService;
use Illuminate\Http\Request;

class OperationsDashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(OperationsDashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Tampilkan halaman utama Operations Dashboard
     */
    public function index()
    {
        // Untuk MVP, ambil jadwal hari ini atau yang sedang aktif. 
        // Idealnya admin memilih dari list penerbangan aktif.
        // Mari kita ambil jadwal terdekat yang statusnya bukan 'arrived' atau 'cancelled'
        $activeSchedules = FlightSchedule::with(['flight.route.origin', 'flight.route.destination'])
            ->whereNotIn('status', ['arrived', 'cancelled'])
            ->orderBy('departure_datetime', 'asc')
            ->get();
            
        return view('admin.operations.index', compact('activeSchedules'));
    }

    /**
     * Menampilkan Command Center spesifik untuk satu Flight Schedule
     */
    public function show(string $scheduleId)
    {
        $data = $this->dashboardService->getDashboardData($scheduleId);
        
        return view('admin.operations.dashboard', $data);
    }
}
