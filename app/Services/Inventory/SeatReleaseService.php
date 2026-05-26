<?php

namespace App\Services\Inventory;

use App\Exceptions\LockOwnershipException;
use App\Models\FlightScheduleSeat;
use Illuminate\Support\Facades\DB;

class SeatReleaseService
{
    /**
     * Release a specific lock manually (e.g. user cancels payment).
     */
    public function releaseLock($scheduleId, $seatNumber, $lockSession, $userId = null)
    {
        return DB::transaction(function () use ($scheduleId, $seatNumber, $lockSession, $userId) {
            $seat = FlightScheduleSeat::where('flight_schedule_id', $scheduleId)
                ->where('seat_number', $seatNumber)
                ->lockForUpdate()
                ->first();

            if (!$seat || $seat->status !== 'locked') {
                return false;
            }

            // Validate ownership
            $ownsLock = false;
            if ($lockSession && $seat->lock_session === $lockSession) {
                $ownsLock = true;
            } elseif ($userId && $seat->locked_by === $userId) {
                $ownsLock = true;
            }

            if (!$ownsLock) {
                throw new LockOwnershipException();
            }

            // Release lock
            $seat->status = 'available';
            $seat->locked_until = null;
            $seat->lock_session = null;
            $seat->locked_by = null;
            $seat->save();

            return true;
        });
    }

    /**
     * System cleanup: release all expired locks.
     */
    public function releaseExpiredLocks()
    {
        // For mass update, atomic update is safe
        $affectedRows = DB::table('flight_schedule_seats')
            ->where('status', 'locked')
            ->where('locked_until', '<', now())
            ->update([
                'status' => 'available',
                'locked_until' => null,
                'lock_session' => null,
                'locked_by' => null,
                'updated_at' => now(),
            ]);
            
        return $affectedRows;
    }
}
