<?php

namespace App\Services\Operations;

use App\Events\BaggageLoaded;
use App\Events\BaggageTagGenerated;
use App\Models\BaggageTag;
use App\Models\BookingPassengerAncillary;
use Illuminate\Support\Str;

class BaggageTagService
{
    protected $qrService;

    public function __construct(BaggageQrService $qrService)
    {
        $this->qrService = $qrService;
    }

    /**
     * Generate baggage tag(s) for a purchased baggage ancillary.
     * Normally, 1 purchase of 20kg might be 1 bag, or 2 bags depending on the user input at counter.
     * For MVP, we assume 1 ancillary = 1 bag.
     */
    public function generateTag(BookingPassengerAncillary $ancillary, float $actualWeightKg): BaggageTag
    {
        if ($ancillary->type !== 'baggage') {
            throw new \Exception("Ancillary is not a baggage service.");
        }

        $segment = $ancillary->bookingSegmentPassenger->bookingSegment->flightSchedule;
        $destCode = $segment->route->destination->iata_code;

        // Generate Non-Sequential Tag Number (e.g. SKY-8X91P)
        $tagNumber = 'SKY-' . strtoupper(Str::random(5));

        $tag = BaggageTag::create([
            'booking_passenger_ancillary_id' => $ancillary->id,
            'tag_number' => $tagNumber,
            'weight_kg' => $actualWeightKg,
            'destination_airport_code' => $destCode,
            'status' => 'checked_in', // For MVP, generating it means it's checked in at the counter
            'issued_at' => now(),
        ]);

        // Generate Cryptographic Signature
        $signature = $this->qrService->generateSignature($tag);
        $tag->update(['signature' => $signature]);

        // Dispatch Event
        BaggageTagGenerated::dispatch($tag);

        return $tag;
    }

    /**
     * Load baggage into the aircraft hold
     */
    public function loadBaggage(string $tagNumber, string $signatureToVerify): BaggageTag
    {
        $tag = BaggageTag::where('tag_number', $tagNumber)->firstOrFail();

        if ($tag->status !== 'checked_in') {
            throw new \Exception("Baggage cannot be loaded. Current status: {$tag->status}");
        }

        if (!$this->qrService->verifySignature($tag, $signatureToVerify)) {
            throw new \Exception("Invalid baggage signature! Potential tampering detected.");
        }

        $tag->update([
            'status' => 'loaded',
            'loaded_at' => now(),
        ]);

        BaggageLoaded::dispatch($tag);

        return $tag;
    }
}
