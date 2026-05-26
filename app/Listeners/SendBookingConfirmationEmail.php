<?php

namespace App\Listeners;

use App\Events\BookingConfirmed;
use App\Mail\BookingConfirmedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendBookingConfirmationEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(BookingConfirmed $event): void
    {
        $email = $event->booking->bookedBy->email ?? 'guest@example.com';
        
        Mail::to($email)->send(new BookingConfirmedMail($event->booking));
    }
}
