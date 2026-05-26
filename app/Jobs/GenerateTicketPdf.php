<?php

namespace App\Jobs;

use App\Models\Ticket;
use App\Services\Documents\TicketPdfService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateTicketPdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $ticket;

    // Retry settings
    public $tries = 3;
    public $backoff = [10, 30, 60];

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function handle(TicketPdfService $pdfService): void
    {
        $pdfService->generateAndStore($this->ticket);
    }
}
