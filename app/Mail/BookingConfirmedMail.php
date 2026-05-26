<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Konfirmasi Booking Anda: ' . $this->booking->pnr,
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: '<h1>Booking Confirmed</h1><p>PNR: ' . $this->booking->pnr . '</p><p>Terima kasih atas pembayaran Anda. Tiket Anda sedang diproses.</p>',
        );
    }
}
