<?php

namespace App\Services\Operations;

use App\Models\OperationalLog;
use Illuminate\Http\Request;

class OperationalLogService
{
    /**
     * Log an operational event.
     * This is an append-only operation. No updates allowed.
     */
    public function log(string $eventType, ?string $flightScheduleId, array $data = []): OperationalLog
    {
        $request = request();
        
        return OperationalLog::create([
            'event_type' => $eventType,
            'flight_schedule_id' => $flightScheduleId,
            'entity_type' => $data['entity_type'] ?? null,
            'entity_id' => $data['entity_id'] ?? null,
            'booking_id' => $data['booking_id'] ?? null,
            'passenger_id' => $data['passenger_id'] ?? null,
            'actor_type' => $data['actor_type'] ?? 'System',
            'actor_id' => $data['actor_id'] ?? null,
            'level' => $data['level'] ?? 'info',
            'event_payload' => $data['payload'] ?? null,
            'ip_address' => $request ? $request->ip() : null,
            'user_agent' => $request ? $request->userAgent() : null,
        ]);
    }
}
