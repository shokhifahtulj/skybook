<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BookingSegmentPassenger;
use App\Models\NotificationDelivery;
use App\Models\BookingReassignment;

class PassengerTimelineController extends Controller
{
    public function show($id)
    {
        $bsp = BookingSegmentPassenger::with(['passenger', 'segment.flightSchedule.flight', 'reassignments.toSchedule.flight'])->findOrFail($id);

        $events = collect();

        // 1. Initial Booking
        $events->push([
            'time' => $bsp->created_at,
            'type' => 'booking',
            'title' => 'Passenger Booked',
            'description' => "Initial booking for {$bsp->segment->flightSchedule->flight->flight_number}.",
            'icon' => 'ticket'
        ]);

        // 2. Notifications
        $notifications = NotificationDelivery::where('booking_segment_passenger_id', $bsp->id)->get();
        foreach ($notifications as $notif) {
            $events->push([
                'time' => $notif->sent_at,
                'type' => 'notification',
                'title' => "Notification Sent ({$notif->channel})",
                'description' => $notif->payload_snapshot['title'] ?? $notif->event_type,
                'icon' => 'bell'
            ]);

            if ($notif->acknowledged_at) {
                $events->push([
                    'time' => $notif->acknowledged_at,
                    'type' => 'acknowledgement',
                    'title' => 'Passenger Acknowledged',
                    'description' => "Read receipt for {$notif->event_type}",
                    'icon' => 'check'
                ]);
            }
        }

        // 3. Reassignments
        foreach ($bsp->reassignments as $reassignment) {
            $events->push([
                'time' => $reassignment->created_at,
                'type' => 'reassignment',
                'title' => 'Rebooked by OCC',
                'description' => "Rebooked to {$reassignment->toSchedule->flight->flight_number} due to {$reassignment->reason}",
                'icon' => 'refresh'
            ]);
        }

        // Sort by time
        $timeline = $events->sortByDesc('time')->values();

        return view('admin.operations.passengers.timeline_partial', compact('bsp', 'timeline'));
    }
}
