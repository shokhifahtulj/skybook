<?php

namespace App\Http\Controllers\Admin\Analytics;

use App\Http\Controllers\Controller;
use App\Services\Analytics\ExecutiveDashboardService;

class ExecutiveDashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(ExecutiveDashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        $snapshot = $this->dashboardService->getDailySnapshot();
        
        return view('admin.analytics.dashboard', compact('snapshot'));
    }
}
