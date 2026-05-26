<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Payment\PaymentCallbackService;
use Illuminate\Http\Request;
use Exception;

class PaymentController extends Controller
{
    protected $callbackService;

    public function __construct(PaymentCallbackService $callbackService)
    {
        $this->callbackService = $callbackService;
    }

    public function simulateSuccess(Request $request, $pnr)
    {
        $request->validate([
            'payment_reference' => 'required|string',
        ]);

        try {
            $payment = $this->callbackService->handleSuccess(
                $request->payment_reference,
                ['source' => 'mock_success', 'timestamp' => now()->toDateTimeString()]
            );

            return response()->json([
                'success' => true,
                'message' => 'Payment processed successfully',
                'data' => [
                    'status' => $payment->status,
                    'booking_status' => $payment->booking->booking_status,
                ]
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
    
    public function simulateFailed(Request $request, $pnr)
    {
        $request->validate([
            'payment_reference' => 'required|string',
        ]);

        try {
            $payment = $this->callbackService->handleFailed(
                $request->payment_reference,
                ['source' => 'mock_failed', 'timestamp' => now()->toDateTimeString()]
            );

            return response()->json([
                'success' => true,
                'message' => 'Payment marked as failed',
                'data' => [
                    'status' => $payment->status,
                ]
            ]);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
