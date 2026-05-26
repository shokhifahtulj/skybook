<?php

namespace App\Services\Booking;

use App\Models\Booking;
use App\Models\Ticket;
use App\Events\TicketsIssued;
use App\Jobs\GenerateTicketPdf;
use Illuminate\Support\Str;

class TicketIssuanceService
{
    /**
     * Issue tickets for a confirmed booking.
     */
    public function issueTickets(Booking $booking)
    {
        $tickets = [];

        foreach ($booking->segments as $segment) {
            $schedule = $segment->schedule;
            $flight = $schedule->flight;
            
            foreach ($segment->segmentPassengers as $segmentPassenger) {
                $passenger = $segmentPassenger->passenger;
                $seat = $segmentPassenger->seat;

                $ticketNumber = '080-' . strtoupper(Str::random(10));
                
                $snapshot = [
                    'booking_id' => $booking->id,
                    'passenger_name' => trim($passenger->title . ' ' . $passenger->first_name . ' ' . $passenger->last_name),
                    'airline_name' => $flight->airline->name ?? 'SkyBook Air',
                    'flight_number' => $flight->flight_number,
                    'origin' => $flight->route->origin->city . ' (' . $flight->route->origin->iata_code . ')',
                    'destination' => $flight->route->destination->city . ' (' . $flight->route->destination->iata_code . ')',
                    'departure_date' => $schedule->departure_datetime->format('d M Y'),
                    'departure_time' => $schedule->departure_datetime->format('H:i'),
                    'arrival_time' => $schedule->arrival_datetime->format('H:i'),
                    'seat_number' => $seat ? $seat->seat_number : 'Unassigned',
                    'cabin_class' => $segment->cabin_class,
                    'booking_reference' => $booking->pnr,
                    'ticket_url' => route('bookings.ticket', $booking),
                    'issued_at' => now()->toDateTimeString(),
                ];

                $ticket = Ticket::create([
                    'booking_segment_passenger_id' => $segmentPassenger->id,
                    'ticket_number' => $ticketNumber,
                    'ticket_status' => 'issued',
                    'snapshot_data' => $snapshot,
                    'issued_at' => now(),
                ]);
                
                $tickets[] = $ticket;
                
                // Queue PDF Generation
                GenerateTicketPdf::dispatch($ticket);
            }
        }

        event(new TicketsIssued($booking));

        return $tickets;
    }
}
