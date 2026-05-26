<?php

namespace App\Services\Ancillary;

use App\Models\AncillaryService;
use App\Models\BookingPassengerAncillary;
use App\Models\BookingSegmentPassenger;
use Illuminate\Support\Str;

class AncillaryPurchaseService
{
    protected $pricingService;
    protected $baggageService;

    public function __construct(AncillaryPricingService $pricingService, BaggageService $baggageService)
    {
        $this->pricingService = $pricingService;
        $this->baggageService = $baggageService;
    }

    /**
     * Add an ancillary service to a passenger (Post-booking upsell)
     */
    public function addAncillary(BookingSegmentPassenger $passenger, AncillaryService $service): BookingPassengerAncillary
    {
        $price = $this->pricingService->calculatePrice($service);
        
        $metadata = [];
        
        if ($service->type === 'baggage') {
            $metadata['weight_kg'] = $this->baggageService->getWeightFromCode($service->code);
            $metadata['bag_tag_uuid'] = (string) Str::uuid(); // Generate tag early for operations
        }

        return BookingPassengerAncillary::create([
            'booking_segment_passenger_id' => $passenger->id,
            'ancillary_service_id' => $service->id,
            'type' => $service->type,
            'snapshot_name' => $service->name,
            'snapshot_price' => $price,
            'metadata' => $metadata,
            'status' => 'pending',
            'operational_status' => 'not_used',
        ]);
    }
}
