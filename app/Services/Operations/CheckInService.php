<?php

namespace App\Services\Operations;

use App\Models\BookingSegmentPassenger;
use App\Events\PassengerCheckedIn;
use Exception;
use Illuminate\Support\Facades\DB;

class CheckInService
{
    protected $eligibilityService;

    public function __construct(CheckInEligibilityService $eligibilityService)
    {
        $this->eligibilityService = $eligibilityService;
    }

    /**
     * Melakukan check-in untuk satu penumpang pada satu segmen.
     *
     * @param string $bookingSegmentPassengerId
     * @return BookingSegmentPassenger
     * @throws Exception
     */
    public function checkIn(string $bookingSegmentPassengerId): BookingSegmentPassenger
    {
        return DB::transaction(function () use ($bookingSegmentPassengerId) {
            // Lock the row to prevent race conditions (double check-in)
            $segmentPassenger = BookingSegmentPassenger::with(['segment.booking', 'segment.schedule', 'ticket'])
                ->lockForUpdate()
                ->findOrFail($bookingSegmentPassengerId);

            // Validate eligibility
            [$isEligible, $reason] = $this->eligibilityService->validateEligibility($segmentPassenger);

            if (!$isEligible) {
                throw new Exception("Check-in ditolak: " . $reason);
            }

            // Update operational status
            $segmentPassenger->update([
                'operational_status' => 'checked_in',
                'checked_in_at' => now()
            ]);

            // Dispatch Event
            PassengerCheckedIn::dispatch($segmentPassenger);

            return $segmentPassenger;
        });
    }
}
