<?php

namespace App\Services\Documents;

use Illuminate\Support\Facades\Storage;

class DocumentStorageService
{
    /**
     * Store ticket PDF document privately.
     */
    public function storeTicketPdf(string $ticketUuid, string $pdfContent): string
    {
        $path = "tickets/{$ticketUuid}.pdf";
        Storage::put($path, $pdfContent);
        return $path;
    }

    /**
     * Get the stream for a ticket PDF.
     */
    public function getTicketPdfStream(string $path)
    {
        return Storage::readStream($path);
    }
}
