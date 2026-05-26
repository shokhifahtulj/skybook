<?php

namespace App\Services\Booking;

use App\Events\BookingConfirmed;
use App\Models\Booking;
use App\Models\FlightSchedule;
use App\Models\FlightScheduleSeat;
use App\Services\Inventory\FlightSeatInventoryService;

class BookingConfirmationService
{
    protected $ticketIssuanceService;

    public function __construct(TicketIssuanceService $ticketIssuanceService)
    {
        $this->ticketIssuanceService = $ticketIssuanceService;
    }

    /**
     * Confirm a booking, lock seats permanently, and issue tickets.
     * Called by PaymentCallbackService within a transaction.
     */
    public function confirmBooking(Booking $booking)
    {
        // 1. Update Booking Status
        $booking->update([
            'booking_status' => 'ticketed',
            'payment_status' => 'paid',
        ]);

        // 2. Finalize Inventory (Locked -> Booked)
        $seatIds = [];
        $scheduleCounts = [];

        foreach ($booking->segments as $segment) {
            foreach ($segment->segmentPassengers as $sp) {
                if (! $sp->flight_schedule_seat_id) {
                    continue;
                }

                $seatIds[] = $sp->flight_schedule_seat_id;
                $seat = $sp->seat;

                if ($seat) {
                    $scheduleCounts[$seat->flight_schedule_id] = ($scheduleCounts[$seat->flight_schedule_id] ?? 0) + 1;
                }
            }
        }

        if (! empty($seatIds)) {
            FlightScheduleSeat::whereIn('id', $seatIds)->update([
                'status' => 'booked',
                'booked_at' => now(),
            ]);
        }

        foreach ($scheduleCounts as $scheduleId => $count) {
            FlightSchedule::whereKey($scheduleId)->decrement('available_seats', $count);
            $schedule = FlightSchedule::find($scheduleId);

            if ($schedule) {
                app(FlightSeatInventoryService::class)->generate($schedule);
            }
        }

        // 3. Issue Tickets
        $this->ticketIssuanceService->issueTickets($booking);

        // 4. Dispatch Event
        event(new BookingConfirmed($booking));

        return $booking;
    }
}
