<?php

namespace App\Services\Booking;

use App\Models\Booking;

class PnrGeneratorService
{
    /**
     * Generate a unique 6-character PNR.
     * Excludes: O, I, 0, 1 to prevent reading confusion.
     */
    public function generate(): string
    {
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        
        do {
            $pnr = '';
            for ($i = 0; $i < 6; $i++) {
                $pnr .= $characters[random_int(0, strlen($characters) - 1)];
            }
        } while (Booking::where('pnr', $pnr)->exists());
        
        return $pnr;
    }
}
