<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NotificationDelivery;

class PassengerNotificationController extends Controller
{
    /**
     * Endpoint for passengers to acknowledge notifications
     */
    public function acknowledge(Request $request, $id)
    {
        $notification = NotificationDelivery::findOrFail($id);
        
        if (is_null($notification->acknowledged_at)) {
            $notification->update(['acknowledged_at' => now()]);
            
            // Log the acknowledgement
            \App\Models\OperationalLog::create([
                'log_type' => 'passenger_acknowledgement',
                'flight_schedule_id' => $notification->flight_schedule_id,
                'logged_by' => null, // from passenger
                'payload' => [
                    'message' => 'Passenger acknowledged notification',
                    'notification_id' => $notification->id,
                    'event_type' => $notification->event_type
                ]
            ]);

            return response()->json(['status' => 'success', 'message' => 'Notification acknowledged']);
        }

        return response()->json(['status' => 'already_acknowledged']);
    }
}
