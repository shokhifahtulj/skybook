<?php

namespace App\Jobs;

use App\Models\BoardingPass;
use App\Services\Operations\BoardingPassPdfService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateBoardingPassPdf implements ShouldQueue
{
    use Queueable;

    public $boardingPassId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $boardingPassId)
    {
        $this->boardingPassId = $boardingPassId;
    }

    /**
     * Execute the job.
     */
    public function handle(BoardingPassPdfService $pdfService): void
    {
        $boardingPass = BoardingPass::find($this->boardingPassId);
        if ($boardingPass && !$boardingPass->pdf_path) {
            $pdfService->generatePdf($boardingPass);
            
            // Tandai issued at di segment_passenger
            $boardingPass->segmentPassenger->update([
                'boarding_pass_issued_at' => now()
            ]);
        }
    }
}
