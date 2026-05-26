<?php

namespace App\Services\Notifications;

use App\Models\NotificationDelivery;
use App\Models\BookingSegmentPassenger;
use App\Models\FlightSchedule;
use Illuminate\Support\Str;

class NotificationOrchestratorService
{
    /**
     * Compose and queue a notification for a passenger
     */
    public function queueNotification(
        BookingSegmentPassenger $passenger, 
        string $eventType, 
        string $priority, 
        array $channels, 
        array $payload, 
        string $idempotencyKey,
        ?FlightSchedule $relatedSchedule = null
    ) {
        // Prevent duplicate messages
        if (NotificationDelivery::where('idempotency_key', $idempotencyKey)->exists()) {
            return;
        }

        // Determine message version based on past events of the same type for this schedule
        $version = 1;
        if ($relatedSchedule) {
            $lastDelivery = NotificationDelivery::where('booking_segment_passenger_id', $passenger->id)
                ->where('flight_schedule_id', $relatedSchedule->id)
                ->where('event_type', $eventType)
                ->orderBy('message_version', 'desc')
                ->first();
            if ($lastDelivery) {
                $version = $lastDelivery->message_version + 1;
            }
        }

        foreach ($channels as $channel) {
            $recipient = $this->getRecipientForChannel($passenger, $channel);
            
            // For channels that don't have a direct recipient (e.g., IN_APP), we still track it
            if (!$recipient && $channel !== 'IN_APP') {
                continue;
            }

            // Generate specific idempotency key per channel
            $channelKey = $idempotencyKey . '-' . $channel;

            NotificationDelivery::create([
                'booking_segment_passenger_id' => $passenger->id,
                'flight_schedule_id' => $relatedSchedule?->id,
                'event_type' => $eventType,
                'channel' => $channel,
                'recipient' => $recipient ?? 'APP_USER',
                'idempotency_key' => $channelKey,
                'message_version' => $version,
                'payload_snapshot' => $payload,
                'priority_level' => $priority,
                'delivery_status' => 'SENT', // MVP: Mocking delivery
                'sent_at' => now(),
            ]);
        }
    }

    private function getRecipientForChannel(BookingSegmentPassenger $passenger, string $channel): ?string
    {
        return match ($channel) {
            'EMAIL' => $passenger->passenger->email,
            'SMS' => $passenger->passenger->phone_number,
            default => null,
        };
    }

    /**
     * Handle Gate Change Notification
     */
    public function handleGateChange($schedule, $oldGate, $newGate, $type)
    {
        $passengers = $schedule->seats()->with('bookingSegmentPassengers.passenger')->get()
            ->pluck('bookingSegmentPassengers')->flatten();

        foreach ($passengers as $bsp) {
            $oldGateStr = $oldGate ? "T{$oldGate->terminal}-{$oldGate->gate_number}" : "TBA";
            $newGateStr = "T{$newGate->terminal}-{$newGate->gate_number}";
            
            $payload = [
                'title' => "Gate Change: Flight {$schedule->flight->flight_number}",
                'message' => "Your departure gate has been changed from {$oldGateStr} to {$newGateStr}.",
                'old_gate' => $oldGateStr,
                'new_gate' => $newGateStr,
                'action_required' => 'Please proceed to the new gate immediately.'
            ];

            $idempotencyKey = "GATE-CHANGE-{$schedule->id}-{$bsp->id}-{$newGate->id}";

            $this->queueNotification(
                $bsp,
                'GATE_CHANGED',
                'HIGH',
                ['EMAIL', 'IN_APP'],
                $payload,
                $idempotencyKey,
                $schedule
            );
        }
    }

    /**
     * Handle Flight Disrupted Notification
     */
    public function handleFlightDisrupted($schedule, $type, $reason)
    {
        $passengers = $schedule->seats()->with('bookingSegmentPassengers.passenger')->get()
            ->pluck('bookingSegmentPassengers')->flatten();

        foreach ($passengers as $bsp) {
            $payload = [
                'title' => "Flight Update: {$schedule->flight->flight_number} is " . strtoupper($type),
                'message' => "We regret to inform you that your flight has been {$type} due to {$reason}.",
                'status' => $type,
                'reason' => $reason,
                'action_required' => 'Please await further instructions or check your rebooking options.'
            ];

            $priority = $type === 'cancelled' ? 'CRITICAL' : 'HIGH';
            $channels = $type === 'cancelled' ? ['EMAIL', 'SMS', 'IN_APP'] : ['EMAIL', 'IN_APP'];
            $idempotencyKey = "FLIGHT-DISRUPTED-{$schedule->id}-{$bsp->id}-{$type}";

            $this->queueNotification(
                $bsp,
                'FLIGHT_DISRUPTED',
                $priority,
                $channels,
                $payload,
                $idempotencyKey,
                $schedule
            );
        }
    }

    /**
     * Handle Passenger Rebooked Notification
     */
    public function handlePassengerRebooked($bsp, $oldSchedule, $newSchedule, $reason)
    {
        $payload = [
            'title' => "Rebooking Confirmation: {$newSchedule->flight->flight_number}",
            'message' => "You have been rebooked to a new flight. Your new departure is {$newSchedule->departure_datetime->format('d M Y H:i')} from {$newSchedule->flight->route->origin->iata_code}.",
            'old_flight' => $oldSchedule->flight->flight_number,
            'new_flight' => $newSchedule->flight->flight_number,
            'new_departure' => $newSchedule->departure_datetime->toIso8601String(),
            'action_required' => 'Please review your new itinerary and check in.'
        ];

        $idempotencyKey = "PASSENGER-REBOOKED-{$bsp->id}-{$oldSchedule->id}-{$newSchedule->id}";

        $this->queueNotification(
            $bsp,
            'PASSENGER_REBOOKED',
            'HIGH',
            ['EMAIL', 'IN_APP'],
            $payload,
            $idempotencyKey,
            $newSchedule
        );
    }
}
