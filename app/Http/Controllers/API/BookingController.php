<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Booking\BookingService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    protected $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function store(Request $request)
    {
        $request->validate([
            'segments' => 'required|array',
            'segments.*.flight_schedule_id' => 'required|uuid',
            'segments.*.cabin_class' => 'required|string',
            'passengers' => 'required|array',
            'passengers.*.title' => 'required|string',
            'passengers.*.first_name' => 'required|string',
            'passengers.*.identity_type' => 'required|string',
            'passengers.*.identity_number' => 'required|string',
            'passengers.*.date_of_birth' => 'required|date',
            'passengers.*.seats' => 'nullable|array', // key: flight_schedule_id, value: seat_number
        ]);

        try {
            $lockSession = $request->header('X-Session-ID', Str::uuid()->toString());
            $userId = auth('sanctum')->id();

            $booking = $this->bookingService->createBookingDraft(
                $request->segments,
                $request->passengers,
                $lockSession,
                $userId
            );

            return response()->json([
                'success' => true,
                'message' => 'Booking draft created successfully.',
                'data' => [
                    'pnr' => $booking->pnr,
                    'total_amount' => $booking->total_amount,
                    'expires_at' => $booking->expires_at,
                    'lock_session' => $lockSession
                ]
            ], 201);
            
        } catch (\App\Exceptions\SeatUnavailableException | \App\Exceptions\SeatAlreadyLockedException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 409); // Conflict
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses booking: ' . $e->getMessage()
            ], 500);
        }
    }
}
