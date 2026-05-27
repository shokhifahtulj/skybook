<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AdminFlightController;
use App\Http\Controllers\AdminScheduleController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FlightController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    if (auth()->user()->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified', 'role:user'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Phase 12: Passenger UI Finalization
    Route::get('/passenger/flight/{segment}/timeline', [\App\Http\Controllers\Passenger\TimelineController::class, 'show'])->name('passenger.timeline');
    Route::get('/passenger/flight/{segment}/boarding-pass', [\App\Http\Controllers\Passenger\BoardingPassController::class, 'show'])->name('passenger.boarding-pass');

    Route::get('/flights', [FlightController::class, 'index'])->name('flights.index');
    Route::get('/search-flights', [FlightController::class, 'index'])->name('search.flights');
    Route::get('/flights/{flight}', [FlightController::class, 'show'])->name('flights.show');

    Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings/create', [BookingController::class, 'selectSchedule'])->name('bookings.create.select');
    Route::get('/bookings/passengers', [BookingController::class, 'passengers'])->name('bookings.passengers');
    Route::post('/bookings/passengers', [BookingController::class, 'savePassengers'])->name('bookings.passengers.save');
    Route::get('/bookings/confirm', [BookingController::class, 'confirm'])->name('bookings.confirm');
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{booking}/ticket', [BookingController::class, 'ticket'])->name('bookings.ticket');
    Route::delete('/bookings/{booking}', [BookingController::class, 'destroy'])->name('bookings.destroy');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
});

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
    
    // Core Master Data Routes (Phase 1)
    Route::resource('airlines', \App\Http\Controllers\Admin\AirlineController::class);
    Route::resource('airports', \App\Http\Controllers\Admin\AirportController::class);
    Route::resource('aircrafts', \App\Http\Controllers\Admin\AircraftController::class);
    Route::get('aircrafts/{aircraft}/seats', [\App\Http\Controllers\Admin\AircraftSeatController::class, 'index'])->name('aircrafts.seats.index');
    Route::post('aircrafts/{aircraft}/seats/generate', [\App\Http\Controllers\Admin\AircraftSeatController::class, 'generate'])->name('aircrafts.seats.generate');
    Route::delete('aircrafts/{aircraft}/seats', [\App\Http\Controllers\Admin\AircraftSeatController::class, 'destroyAll'])->name('aircrafts.seats.destroyAll');
    
    Route::resource('routes', \App\Http\Controllers\Admin\RouteController::class);
    Route::resource('flights', \App\Http\Controllers\Admin\FlightController::class);
    Route::resource('users', UserController::class);
    
    // Core Operations (Phase 2)
    Route::resource('schedules', \App\Http\Controllers\Admin\ScheduleController::class);
    
    // Phase 6A: Operations Dashboard
    Route::get('operations', [\App\Http\Controllers\Admin\OperationsDashboardController::class, 'index'])->name('operations.index');
    
    // Phase 7B: Executive Analytics
    Route::get('analytics/executive', [\App\Http\Controllers\Admin\Analytics\ExecutiveDashboardController::class, 'index'])->name('analytics.executive');
    
    // Phase 7A: IROPS Operations
    Route::post('operations/{schedule}/irops/delay', [\App\Http\Controllers\Admin\Operations\IropsController::class, 'delay'])->name('operations.irops.delay');
    Route::post('operations/{schedule}/irops/gate', [\App\Http\Controllers\Admin\Operations\IropsController::class, 'changeGate'])->name('operations.irops.gate');
    Route::post('operations/{schedule}/irops/cancel', [\App\Http\Controllers\Admin\Operations\IropsController::class, 'cancel'])->name('operations.irops.cancel');
    
    // Phase 8A: Fleet & Crew Assignment
    Route::get('operations/{schedule}/assignment', [\App\Http\Controllers\Admin\Operations\AssignmentController::class, 'index'])->name('operations.assignment');
    Route::post('operations/{schedule}/assignment/aircraft', [\App\Http\Controllers\Admin\Operations\AssignmentController::class, 'assignAircraft'])->name('operations.assignment.aircraft');
    Route::post('operations/{schedule}/assignment/crew', [\App\Http\Controllers\Admin\Operations\AssignmentController::class, 'assignCrew'])->name('operations.assignment.crew');
    Route::delete('operations/{schedule}/assignment/crew/{assignment}', [\App\Http\Controllers\Admin\Operations\AssignmentController::class, 'unassignCrew'])->name('operations.assignment.crew.remove');
    
    // Phase 9C: Hub & Gate Management
    Route::get('operations/gates', [\App\Http\Controllers\Admin\Operations\GateController::class, 'index'])->name('operations.gates');
    Route::post('operations/gates/{schedule}/swap', [\App\Http\Controllers\Admin\Operations\GateController::class, 'swap'])->name('operations.gates.swap');

    // Phase 10A, 10B, & 11A: Passenger Reaccommodation & Intelligence
    Route::get('operations/reaccommodation', [\App\Http\Controllers\Admin\Operations\ReaccommodationController::class, 'index'])->name('operations.reaccommodation');
    Route::post('operations/reaccommodation/{schedule}/rebook', [\App\Http\Controllers\Admin\Operations\ReaccommodationController::class, 'rebook'])->name('operations.reaccommodation.rebook');
    Route::get('operations/reaccommodation/{schedule}/intelligence', [\App\Http\Controllers\Admin\Operations\ReaccommodationController::class, 'intelligence'])->name('operations.reaccommodation.intelligence');
    Route::get('operations/passengers/{id}/timeline', [\App\Http\Controllers\Admin\Operations\PassengerTimelineController::class, 'show'])->name('operations.passengers.timeline');

    // Phase 10C: Massive IROPS Simulator
    Route::get('operations/simulation', [\App\Http\Controllers\Admin\Operations\SimulationController::class, 'index'])->name('operations.simulation');
    Route::post('operations/simulation/start', [\App\Http\Controllers\Admin\Operations\SimulationController::class, 'start'])->name('operations.simulation.start');
    Route::post('operations/simulation/{session}/restore', [\App\Http\Controllers\Admin\Operations\SimulationController::class, 'restore'])->name('operations.simulation.restore');
    Route::get('operations/simulation/{session}/replay', [\App\Http\Controllers\Admin\Operations\SimulationController::class, 'replay'])->name('operations.simulation.replay');
    Route::get('operations/engineering', [\App\Http\Controllers\Admin\Operations\EngineeringController::class, 'index'])->name('operations.engineering');
    Route::post('operations/engineering/store', [\App\Http\Controllers\Admin\Operations\EngineeringController::class, 'store'])->name('operations.engineering.store');
    Route::post('operations/engineering/{maintenance}/release', [\App\Http\Controllers\Admin\Operations\EngineeringController::class, 'release'])->name('operations.engineering.release');

    // Phase 6C: Baggage Drop Counter
    Route::get('operations-baggage/drop', [\App\Http\Controllers\Admin\Operations\BaggageController::class, 'dropCounter'])->name('operations.baggage.drop');
    Route::post('operations-baggage/generate', [\App\Http\Controllers\Admin\Operations\BaggageController::class, 'generate'])->name('operations.baggage.generate');

    // Phase 6A: Command Center Detail
    Route::get('operations/{schedule}', [\App\Http\Controllers\Admin\OperationsDashboardController::class, 'show'])->name('operations.show');
});

// Phase 6C: Baggage Tag PDF Render (Protected by middleware in full prod, open for MVP)
Route::get('/api/baggage-tags/{tag}/render', [\App\Http\Controllers\Api\BaggageTagRenderController::class, 'render'])->name('api.baggage-tags.render');

// Phase 4D: Passenger Portal & Booking Lookup (Public/Guest Access)
Route::middleware('throttle:10,1')->group(function () {
    Route::get('manage-booking', [App\Http\Controllers\Web\ManageBookingController::class, 'index'])->name('manage-booking.index');
    Route::post('manage-booking', [App\Http\Controllers\Web\ManageBookingController::class, 'lookup'])->name('manage-booking.lookup');
    Route::post('manage-booking/lookup', [App\Http\Controllers\Web\ManageBookingController::class, 'lookup'])->name('manage-booking.lookup.alias');
    Route::get('manage-booking/portal/{pnr}', [App\Http\Controllers\Web\ManageBookingController::class, 'portal'])->name('manage-booking.portal');

    // Phase 6B: Ancillary Purchase
    Route::get('manage-booking/portal/{pnr}/ancillary', [App\Http\Controllers\Web\AncillaryController::class, 'catalog'])->name('manage-booking.ancillary.catalog');
    Route::post('manage-booking/portal/{pnr}/ancillary', [App\Http\Controllers\Web\AncillaryController::class, 'store'])->name('manage-booking.ancillary.store');
});

// Phase 4C/4D: Download E-Ticket (Can be accessed directly if you have the Ticket ID, or add signed middleware)
Route::get('/tickets/{uuid}/download', [\App\Http\Controllers\Api\TicketDownloadController::class, 'download'])->name('tickets.download');

// Phase 4D: Verification Endpoint
Route::get('/verify/ticket/{uuid}', function($uuid) {
    $ticket = \App\Models\Ticket::find($uuid);
    if (!$ticket) return view('tickets.verify', ['error' => 'Ticket tidak ditemukan atau tidak valid.']);
    return view('tickets.verify', ['ticket' => $ticket]);
})->name('tickets.verify');

require __DIR__.'/auth.php';

// Phase 5A: Web Check-In UI
Route::middleware('throttle:10,1')->group(function () {
    Route::get('/checkin', [\App\Http\Controllers\Web\CheckIn\WebCheckInController::class, 'index'])->name('checkin.index');
    Route::post('/checkin', [\App\Http\Controllers\Web\CheckIn\WebCheckInController::class, 'lookup'])->name('checkin.lookup');
});

Route::middleware('throttle:30,1')->group(function () {
    Route::get('/checkin/portal/{pnr}', [\App\Http\Controllers\Web\CheckIn\WebCheckInController::class, 'passengers'])->name('checkin.portal');
    Route::post('/checkin/portal/{pnr}', [\App\Http\Controllers\Web\CheckIn\PassengerCheckInController::class, 'process'])->name('checkin.process');
    Route::get('/checkin/portal/{pnr}/seatmap/{passenger_id}', [\App\Http\Controllers\Web\CheckIn\SeatSelectionController::class, 'show'])->name('checkin.seatmap');
    Route::post('/checkin/portal/{pnr}/seatmap/{passenger_id}', [\App\Http\Controllers\Web\CheckIn\SeatSelectionController::class, 'update'])->name('checkin.seatmap.update');
    Route::get('/checkin/portal/{pnr}/confirmation', [\App\Http\Controllers\Web\CheckIn\PassengerCheckInController::class, 'confirmation'])->name('checkin.confirmation');
});

// Phase 5B: Boarding Pass System (Web)
Route::get('/boarding-pass/{uuid}/download', [\App\Http\Controllers\Web\CheckIn\BoardingPassController::class, 'download'])->name('boarding-pass.download');
Route::get('/boarding-pass/verify/{uuid}', [\App\Http\Controllers\Web\CheckIn\BoardingPassController::class, 'verify'])->name('boarding-pass.verify');
