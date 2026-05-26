<?php

namespace App\Services\Irops;

use App\Events\Irops\BoardingPassRegenerated;
use App\Events\Irops\BoardingPassSuperseded;
use App\Models\BoardingPass;
use App\Models\FlightSchedule;
use App\Services\Operations\BoardingPassGeneratorService;
use Illuminate\Support\Facades\DB;

class BoardingPassRegenerationService
{
    protected $generatorService;

    public function __construct(BoardingPassGeneratorService $generatorService)
    {
        $this->generatorService = $generatorService;
    }

    /**
     * Regenerate all active boarding passes for a flight schedule
     */
    public function regenerateForSchedule(FlightSchedule $schedule, string $reason): void
    {
        // Find all active boarding passes
        $boardingPasses = BoardingPass::whereHas('bookingSegmentPassenger.bookingSegment', function ($q) use ($schedule) {
                $q->where('flight_schedule_id', $schedule->id);
            })
            ->whereIn('status', ['generated', 'active'])
            ->get();

        foreach ($boardingPasses as $oldPass) {
            DB::transaction(function () use ($oldPass, $reason) {
                // 1. Supersede old boarding pass
                $oldPass->update([
                    'status' => 'superseded',
                    'revoked_at' => now(),
                ]);
                
                BoardingPassSuperseded::dispatch($oldPass, $reason);

                // 2. Generate new boarding pass
                $bsp = $oldPass->bookingSegmentPassenger;
                // Force generator service to generate a new one
                $newPass = $this->generatorService->generateForPassenger($bsp);

                BoardingPassRegenerated::dispatch($oldPass, $newPass, $reason);
            });
        }
    }
}
