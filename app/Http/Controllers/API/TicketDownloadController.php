<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Services\Documents\DocumentStorageService;
use Illuminate\Http\Request;

class TicketDownloadController extends Controller
{
    protected $storageService;

    public function __construct(DocumentStorageService $storageService)
    {
        $this->storageService = $storageService;
    }

    /**
     * Download or stream ticket PDF (Must be authorized or use signed routes)
     * Endpoint /api/tickets/{uuid}/download
     */
    public function download(Request $request, $uuid)
    {
        // TODO: In Phase 4D/5 add signed route validation or Auth check
        // if (!$request->hasValidSignature()) { abort(403); }

        $ticket = Ticket::findOrFail($uuid);

        if (!$ticket->document_path) {
            return response()->json(['message' => 'Dokumen PDF belum digenerate.'], 404);
        }

        return response()->streamDownload(function () use ($ticket) {
            echo stream_get_contents($this->storageService->getTicketPdfStream($ticket->document_path));
        }, 'E-Ticket-' . $ticket->ticket_number . '.pdf', [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
