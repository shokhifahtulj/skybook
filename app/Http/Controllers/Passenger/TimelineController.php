<?php

namespace App\Http\Controllers\Passenger;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BookingSegment;
use App\Models\NotificationDelivery;

class TimelineController extends Controller
{
    public function show($id)
    {
        $segment = BookingSegment::with([
            'schedule.flight.route.origin',
            'schedule.flight.route.destination'
        ])->findOrFail($id);

        $segmentPassengerIds = $segment->segmentPassengers()->pluck('id');
        
        $notifications = NotificationDelivery::whereIn('booking_segment_passenger_id', $segmentPassengerIds)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('passenger.timeline.show', compact('segment', 'notifications'));
    }
}
