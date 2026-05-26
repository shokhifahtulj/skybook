<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Operations\BoardingScanService;
use Illuminate\Http\Request;

class BoardingPassValidationController extends Controller
{
    /**
     * Memvalidasi QR Boarding Pass secara atomic
     */
    public function validateScan(Request $request, BoardingScanService $scanService)
    {
        $request->validate([
            'uuid' => 'required|uuid',
            'signature' => 'required|string'
        ]);

        try {
            $result = $scanService->scan($request->uuid, $request->signature);
            
            if ($result['success']) {
                return response()->json($result);
            } else {
                return response()->json($result, 422);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
