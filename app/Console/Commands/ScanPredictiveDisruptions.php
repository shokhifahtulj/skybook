<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Intelligence\PredictiveDisruptionService;

class ScanPredictiveDisruptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'intelligence:scan-predictions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan network for predictive disruptions (Phase 11B)';

    /**
     * Execute the console command.
     */
    public function handle(PredictiveDisruptionService $service, \App\Services\Intelligence\AutonomousResolutionService $autoService)
    {
        $this->info('Scanning for Gate Conflicts...');
        $service->scanForGateConflicts();

        $this->info('Scanning for Rotation Delays...');
        $service->scanForRotationDelays();

        $this->info('Executing Autonomous Resolutions...');
        $autoService->executeResolutions();

        $this->info('Predictive Scan Completed.');
    }
}
