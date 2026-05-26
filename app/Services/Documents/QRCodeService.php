<?php

namespace App\Services\Documents;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRCodeService
{
    /**
     * Generate SVG QR Code for ticket verification.
     */
    public function generateVerificationQr(string $ticketUuid): string
    {
        $verificationUrl = url('/api/tickets/verify/' . $ticketUuid);
        
        // Return base64 encoded SVG string to embed in PDF
        $svg = (string) QrCode::format('svg')->size(150)->generate($verificationUrl);
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
}
