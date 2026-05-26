<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Operations\OperationsDashboardService;
use Illuminate\Http\Request;

class OperationsPollingController extends Controller
{
    protected $dashboardService;

    public function __construct(OperationsDashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Endpoint untuk polling data dashboard secara asinkron
     */
    public function getDashboardData($scheduleId)
    {
        $data = $this->dashboardService->getDashboardData($scheduleId);
        
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
}
