<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\NotificationDelivery;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ScanUnacknowledgedNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:scan-unacknowledged';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan for HIGH/CRITICAL notifications that have not been acknowledged within 15 minutes.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $timeoutMins = 15;
        $cutoffTime = Carbon::now()->subMinutes($timeoutMins);

        $unacknowledged = NotificationDelivery::whereNull('acknowledged_at')
            ->whereIn('priority_level', ['HIGH', 'CRITICAL'])
            ->where('sent_at', '<=', $cutoffTime)
            ->where('delivery_status', '!=', 'ESCALATED')
            ->get();

        foreach ($unacknowledged as $notification) {
            // Flag as escalated
            $notification->update(['delivery_status' => 'ESCALATED']);

            // For MVP: we just log it. In a real scenario, this might trigger an Airport Desk alert
            Log::warning("Notification Escalated: Passenger {$notification->booking_segment_passenger_id} has not acknowledged {$notification->event_type} after {$timeoutMins} mins.");
            
            // Could add an operational_log entry here too
            \App\Models\OperationalLog::create([
                'log_type' => 'escalation',
                'flight_schedule_id' => $notification->flight_schedule_id,
                'logged_by' => null,
                'payload' => [
                    'message' => 'Passenger Notification Escalated',
                    'notification_id' => $notification->id,
                    'passenger_id' => $notification->booking_segment_passenger_id,
                    'event_type' => $notification->event_type
                ]
            ]);
        }

        $this->info("Scanned and escalated {$unacknowledged->count()} notifications.");
    }
}
