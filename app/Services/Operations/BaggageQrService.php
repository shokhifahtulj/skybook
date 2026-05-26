<?php

namespace App\Services\Operations;

use App\Models\BaggageTag;
use Illuminate\Support\Facades\Crypt;

class BaggageQrService
{
    /**
     * Generate secure payload for baggage tag QR
     */
    public function generatePayload(BaggageTag $tag): string
    {
        $payload = [
            'id' => $tag->id,
            'tag_number' => $tag->tag_number,
            'dest' => $tag->destination_airport_code,
            'weight' => $tag->weight_kg,
            'ts' => time()
        ];
        
        // In real airline, this might be standard IATA format or custom encrypted string
        return Crypt::encryptString(json_encode($payload));
    }

    /**
     * Generate cryptographic signature to verify tag wasn't tampered with
     */
    public function generateSignature(BaggageTag $tag): string
    {
        $data = $tag->id . $tag->tag_number . $tag->destination_airport_code;
        return hash_hmac('sha256', $data, config('app.key'));
    }

    /**
     * Verify tag signature (e.g. at loading gate)
     */
    public function verifySignature(BaggageTag $tag, string $signatureToVerify): bool
    {
        return hash_equals($this->generateSignature($tag), $signatureToVerify);
    }
}
