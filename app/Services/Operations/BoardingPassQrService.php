<?php

namespace App\Services\Operations;

use App\Models\BoardingPass;
use Illuminate\Support\Facades\Config;

class BoardingPassQrService
{
    /**
     * Menghasilkan payload HMAC untuk QR Boarding Pass
     */
    public function generateSignature(BoardingPass $boardingPass): string
    {
        $appKey = Config::get('app.key');
        $payload = implode('|', [
            $boardingPass->id,
            $boardingPass->booking_segment_passenger_id,
            $boardingPass->boarding_pass_number,
            $boardingPass->issued_at->timestamp
        ]);

        return hash_hmac('sha256', $payload, $appKey);
    }

    /**
     * Memvalidasi keaslian signature
     */
    public function verifySignature(BoardingPass $boardingPass, string $providedSignature): bool
    {
        $expectedSignature = $this->generateSignature($boardingPass);
        
        return hash_equals($expectedSignature, $providedSignature);
    }
}
