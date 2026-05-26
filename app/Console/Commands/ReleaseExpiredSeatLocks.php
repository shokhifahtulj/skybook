<?php

namespace App\Console\Commands;

use App\Services\Inventory\SeatReleaseService;
use Illuminate\Console\Command;

class ReleaseExpiredSeatLocks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:release-locks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Release expired seat locks automatically';

    /**
     * Execute the console command.
     */
    public function handle(SeatReleaseService $releaseService)
    {
        $this->info('Starting expired lock cleanup...');
        
        $releasedCount = $releaseService->releaseExpiredLocks();
        
        if ($releasedCount > 0) {
            $this->info("Successfully released {$releasedCount} expired seat locks.");
        } else {
            $this->info('No expired locks found.');
        }
    }
}
