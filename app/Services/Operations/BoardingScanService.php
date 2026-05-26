<?php

namespace App\Services\Operations;

use App\Models\BoardingPass;
use App\Events\BoardingPassScanned;
use App\Events\PassengerBoarded;
use Exception;
use Illuminate\Support\Facades\DB;

class BoardingScanService
{
    protected $qrService;
    protected $logService;

    public function __construct(BoardingPassQrService $qrService, OperationalLogService $logService)
    {
        $this->qrService = $qrService;
        $this->logService = $logService;
    }

    /**
     * Memindai boarding pass secara transaksional
     */
    public function scan(string $boardingPassId, string $signature): array
    {
        return DB::transaction(function () use ($boardingPassId, $signature) {
            $boardingPass = BoardingPass::lockForUpdate()->findOrFail($boardingPassId);
            $scheduleId = $boardingPass->segmentPassenger->segment->flight_schedule_id;
            
            $logData = [
                'entity_type' => 'boarding_pass',
                'entity_id' => $boardingPassId,
                'booking_id' => $boardingPass->segmentPassenger->booking_id,
                'passenger_id' => $boardingPass->segmentPassenger->passenger_id,
                'actor_type' => 'Gate Agent',
                'payload' => ['gate' => $boardingPass->gate_snapshot]
            ];

            // 1. Verify Signature
            if (!$this->qrService->verifySignature($boardingPass, $signature)) {
                $logData['level'] = 'danger';
                $this->logService->log('signature_invalid', $scheduleId, $logData);
                return ['success' => false, 'message' => 'Invalid or forged QR signature.'];
            }

            // 2. Status Validation
            if ($boardingPass->status === 'revoked') {
                $logData['level'] = 'warning';
                $this->logService->log('revoked_scan_attempt', $scheduleId, $logData);
                return ['success' => false, 'message' => 'Boarding pass ini sudah dicabut (revoked). Gunakan versi terbaru.'];
            }
            if ($boardingPass->status === 'superseded') {
                $logData['level'] = 'warning';
                $this->logService->log('superseded_scan_attempt', $scheduleId, $logData);
                return ['success' => false, 'message' => 'BOARDING DENIED: Boarding pass superseded. Please use your newly generated boarding pass.'];
            }
            if ($boardingPass->status === 'expired') {
                return ['success' => false, 'message' => 'Boarding pass sudah expired.'];
            }
            if ($boardingPass->status === 'boarded' || $boardingPass->status === 'scanned') {
                $logData['level'] = 'danger';
                $this->logService->log('duplicate_scan_attempt', $scheduleId, $logData);
                return ['success' => false, 'message' => 'Penumpang sudah dipindai atau sudah boarding. (Duplicate scan prevention)'];
            }
            if ($boardingPass->status !== 'active') {
                return ['success' => false, 'message' => 'Boarding pass tidak aktif.'];
            }

            // 3. Update Status
            $boardingPass->update([
                'status' => 'boarded' // For simplicity we go straight to boarded
            ]);

            // 4. Update Operational State on passenger
            $boardingPass->segmentPassenger->update([
                'operational_status' => 'boarded',
                'boarded_at' => now()
            ]);

            // 5. Update Seat Status to boarded
            if ($boardingPass->segmentPassenger->seat) {
                $boardingPass->segmentPassenger->seat->update(['status' => 'boarded']);
            }

            // 6. Log Success
            $logData['level'] = 'info';
            $this->logService->log('boarding_approved', $scheduleId, $logData);

            // 7. Dispatch Event
            BoardingPassScanned::dispatch($boardingPass);
            PassengerBoarded::dispatch($boardingPass);

            return ['success' => true, 'message' => 'Boarding scan successful.', 'boarding_pass' => $boardingPass];
        });
    }
}
