<?php

namespace App\Services\Ancillary;

use App\Models\AncillaryService;

class AncillaryPricingService
{
    /**
     * Get dynamic price for a given ancillary service.
     * In a full implementation, this could depend on the flight route,
     * time until departure, or passenger loyalty status.
     * For MVP, we return the base price from snapshot.
     */
    public function calculatePrice(AncillaryService $service, $context = [])
    {
        // Placeholder for dynamic pricing logic
        // e.g., if ($context['hours_to_departure'] < 24) return $service->base_price * 1.5;

        return $service->base_price;
    }
}
