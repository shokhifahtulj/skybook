<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Flight;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }

        // Fetch user's upcoming bookings
        $bookings = Booking::where('booked_by', $user->id)
            ->whereIn('booking_status', ['CONFIRMED', 'PARTIAL_PAID', 'PENDING'])
            ->with(['segments.schedule.flight.route.origin', 'segments.schedule.flight.route.destination', 'segments.schedule.flight.airline', 'passengers.segmentPassengers'])
            ->orderBy('created_at', 'desc')
            ->get();

        $upcomingFlights = collect();
        $notifications = collect();
        
        foreach ($bookings as $booking) {
            foreach ($booking->segments as $segment) {
                if ($segment->schedule && $segment->schedule->departure_time > now()->subDays(1)) {
                    $upcomingFlights->push($segment);
                    
                    // Fetch notifications for the passengers in this segment
                    $segmentPassengers = $segment->segmentPassengers()->pluck('id');
                    $segmentNotifications = \App\Models\NotificationDelivery::whereIn('booking_segment_passenger_id', $segmentPassengers)
                        ->orderBy('created_at', 'desc')
                        ->get();
                    $notifications = $notifications->merge($segmentNotifications);
                }
            }
        }

        $upcomingFlights = $upcomingFlights->sortBy('schedule.departure_time');

        return view('dashboard', compact('bookings', 'upcomingFlights', 'notifications'));
    }

    public function admin()
    {
        $stats = [
            'totalAirlines' => \App\Models\Airline::count(),
            'totalAirports' => \App\Models\Airport::count(),
            'totalRoutes' => \App\Models\Route::count(),
            'totalFlights' => \App\Models\Flight::count(),
        ];

        $latestBookings = []; // Phase 2 Booking Flow
        $recentFlights = \App\Models\Flight::with(['airline', 'route.origin', 'route.destination'])->latest()->take(5)->get();
        $recentAirports = \App\Models\Airport::latest()->take(5)->get();
        $predictiveAlerts = \App\Models\PredictiveAlert::whereIn('status', ['PREDICTED', 'CONFIRMED'])
            ->orderBy('severity', 'asc') // CRITICAL, HIGH, MEDIUM, LOW - Wait, alphabetical?
            ->orderBy('predicted_at', 'desc')
            ->take(5)
            ->get()
            ->sortByDesc(function ($alert) {
                // Map severity to weight
                $weights = ['CRITICAL' => 4, 'HIGH' => 3, 'MEDIUM' => 2, 'LOW' => 1];
                return $weights[$alert->severity] ?? 0;
            });

        return view('admin.dashboard', compact('stats', 'latestBookings', 'recentFlights', 'recentAirports', 'predictiveAlerts'));
    }
}
