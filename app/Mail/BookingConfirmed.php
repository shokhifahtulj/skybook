<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingConfirmed extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Booking $booking;

    /**
     * Create a new message instance.
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
        $this->queue = 'mail';
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Booking Confirmation: ' . ($this->booking->pnr ?? ''))
            ->view('emails.booking_confirmed')
            ->with(['booking' => $this->booking]);
    }
}
