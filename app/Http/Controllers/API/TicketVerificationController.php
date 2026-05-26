<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketVerificationController extends Controller
{
    /**
     * Endpoint /api/tickets/verify/{uuid}
     */
    public function verify($uuid)
    {
        $ticket = Ticket::find($uuid);

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket tidak ditemukan atau tidak valid.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Tiket valid',
            'data' => [
                'ticket_number' => $ticket->ticket_number,
                'status' => $ticket->ticket_status,
                'issued_at' => $ticket->issued_at,
                'snapshot' => $ticket->snapshot_data,
            ]
        ]);
    }
}
