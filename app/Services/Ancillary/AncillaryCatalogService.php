<?php

namespace App\Services\Ancillary;

use App\Models\AncillaryService;

class AncillaryCatalogService
{
    /**
     * Get all active ancillary services from the catalog.
     * Can be filtered by flight schedule if we want specific rules later.
     */
    public function getAvailableCatalog()
    {
        return AncillaryService::where('is_active', true)->get();
    }

    /**
     * Get a specific service by its code
     */
    public function getServiceByCode(string $code)
    {
        return AncillaryService::where('code', $code)->where('is_active', true)->firstOrFail();
    }
}
