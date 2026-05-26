<?php

namespace App\Services\Inventory;

use App\Exceptions\SeatAlreadyLockedException;
use App\Exceptions\SeatUnavailableException;
use App\Models\FlightScheduleSeat;
use Illuminate\Support\Facades\DB;

class SeatLockService
{
    /**
     * Lock a seat for a specific session/user using pessimistic locking.
     */
    public function lockSeat($scheduleId, $seatNumber, $lockSession, $userId = null)
    {
        return DB::transaction(function () use ($scheduleId, $seatNumber, $lockSession, $userId) {
            
            // SELECT FOR UPDATE to prevent race conditions
            $seat = FlightScheduleSeat::where('flight_schedule_id', $scheduleId)
                ->where('seat_number', $seatNumber)
                ->lockForUpdate()
                ->first();

            if (!$seat) {
                throw new SeatUnavailableException();
            }

            // Validate if seat is truly available
            $isAvailable = $seat->status === 'available' || 
                           ($seat->status === 'locked' && $seat->locked_until < now());

            if (!$isAvailable) {
                if ($seat->status === 'locked') {
                    // Check if it's our own lock to extend it
                    if ($seat->lock_session === $lockSession) {
                        $seat->locked_until = now()->addMinutes(config('seat_lock.duration_minutes', 15));
                        $seat->save();
                        return $seat;
                    }
                    throw new SeatAlreadyLockedException();
                }
                
                throw new SeatUnavailableException();
            }

            // Apply lock
            $seat->status = 'locked';
            $seat->locked_by = $userId;
            $seat->lock_session = $lockSession;
            $seat->locked_until = now()->addMinutes(config('seat_lock.duration_minutes', 15));
            $seat->save();

            return $seat;
        });
    }
}
