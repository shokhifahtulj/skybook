<?php

namespace App\Services\Documents;

use App\Models\Ticket;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TicketPdfService
{
    protected $storageService;
    protected $qrCodeService;

    public function __construct(DocumentStorageService $storageService, QRCodeService $qrCodeService)
    {
        $this->storageService = $storageService;
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * Generate PDF for a ticket and save it.
     */
    public function generateAndStore(Ticket $ticket)
    {
        $snapshot = $ticket->snapshot_data;
        $ticketUrl = $snapshot['ticket_url'] ?? url('/bookings');
        $qrCodeDataUrl = 'data:image/svg+xml;base64,' . base64_encode((string) QrCode::format('svg')->size(180)->generate($ticketUrl));

        $pdf = Pdf::loadView('tickets.eticket', [
            'ticket' => $ticket,
            'snapshot' => $snapshot,
            'qrCode' => $qrCodeDataUrl,
        ]);

        $path = $this->storageService->storeTicketPdf($ticket->id, $pdf->output());

        $ticket->update(['document_path' => $path]);

        return $path;
    }
}
