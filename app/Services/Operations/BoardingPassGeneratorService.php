<?php

namespace App\Services\Operations;

use App\Models\BoardingPass;
use App\Models\BookingSegmentPassenger;

class BoardingPassGeneratorService
{
    public function __construct(protected BoardingPassService $boardingPassService)
    {
    }

    public function generateForPassenger(BookingSegmentPassenger $segmentPassenger): BoardingPass
    {
        return $this->boardingPassService->generate($segmentPassenger);
    }
}
