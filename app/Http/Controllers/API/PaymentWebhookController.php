<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Payment\PaymentCallbackService;
use Illuminate\Http\Request;

class PaymentWebhookController extends Controller
{
    public function __construct(protected PaymentCallbackService $callbackService)
    {
    }

    public function handleCallback(Request $request)
    {
        $payload = $request->validate([
            'payment_reference' => 'required|string',
            'status' => 'required|in:paid,failed',
        ]);

        try {
            $payment = $payload['status'] === 'paid'
                ? $this->callbackService->handleSuccess($payload['payment_reference'], $request->all())
                : $this->callbackService->handleFailed($payload['payment_reference'], $request->all());

            return response()->json([
                'success' => true,
                'data' => [
                    'status' => $payment->status,
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
