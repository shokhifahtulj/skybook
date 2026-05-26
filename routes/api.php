
<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BookingApiController;
use App\Http\Controllers\API\FlightApiController;
use App\Http\Controllers\API\ScheduleApiController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::apiResource('flights', FlightApiController::class)->names('api.flights');
});

Route::middleware(['auth:sanctum', 'role:user'])->group(function () {
    Route::post('/bookings', [BookingApiController::class, 'store']);
    Route::put('/bookings/{booking}', [BookingApiController::class, 'update']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/bookings', [BookingApiController::class, 'index']);
    Route::get('/bookings/{booking}', [BookingApiController::class, 'show']);
    Route::delete('/bookings/{booking}', [BookingApiController::class, 'destroy']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('schedules', ScheduleApiController::class)->except(['show', 'create', 'edit']);

    // Booking Core Phase 4
    Route::post('/bookings/create-draft', [\App\Http\Controllers\Api\BookingController::class, 'store']);
    Route::post('/bookings/{pnr}/pay/success', [\App\Http\Controllers\Api\PaymentController::class, 'simulateSuccess']);
    Route::post('/bookings/{pnr}/pay/failed', [\App\Http\Controllers\Api\PaymentController::class, 'simulateFailed']);
    Route::post('/webhooks/payment/callback', [\App\Http\Controllers\Api\PaymentWebhookController::class, 'handleCallback']);

    Route::post('/notifications/{id}/acknowledge', [\App\Http\Controllers\Api\PassengerNotificationController::class, 'acknowledge']);

    // Document Rendering Phase 4C
    Route::get('/tickets/{uuid}/download', [\App\Http\Controllers\Api\TicketDownloadController::class, 'download']);
});

// Phase 5B: Operational Boarding Scanner API
Route::post('/boarding-pass/validate', [\App\Http\Controllers\Api\BoardingPassValidationController::class, 'validateScan']);

// Phase 6A: Operational Dashboard Polling
Route::get('/operations/{schedule}/poll', [\App\Http\Controllers\Api\OperationsPollingController::class, 'getDashboardData']);

// Public Verification Endpoint
Route::get('/tickets/verify/{uuid}', [\App\Http\Controllers\Api\TicketVerificationController::class, 'verify']);
