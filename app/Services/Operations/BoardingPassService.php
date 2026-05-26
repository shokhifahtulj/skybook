<?php

namespace App\Services\Operations;

use App\Models\BoardingPass;
use App\Models\BookingSegmentPassenger;
use App\Events\BoardingPassIssued;
use App\Events\BoardingPassRegenerated;
use Exception;
use Illuminate\Support\Str;

class BoardingPassService
{
    protected $qrService;

    public function __construct(BoardingPassQrService $qrService)
    {
        $this->qrService = $qrService;
    }

    /**
     * Membuat boarding pass pertama kali
     */
    public function generate(BookingSegmentPassenger $segmentPassenger): BoardingPass
    {
        // Pastikan passenger sudah checked_in
        if ($segmentPassenger->operational_status !== 'checked_in') {
            throw new Exception("Penumpang belum melakukan check-in.");
        }

        // Cek apakah sudah ada active boarding pass
        $existing = BoardingPass::where('booking_segment_passenger_id', $segmentPassenger->id)
            ->whereIn('status', ['generated', 'active'])
            ->first();

        if ($existing) {
            return $existing;
        }

        $schedule = $segmentPassenger->segment->schedule;
        $departureTime = $schedule->departure_datetime;

        $boardingPass = BoardingPass::create([
            'booking_segment_passenger_id' => $segmentPassenger->id,
            'boarding_pass_number' => 'BP-' . strtoupper(Str::random(8)),
            'status' => 'active',
            'gate_snapshot' => $this->simulateGate($schedule->flight->route->origin->iata_code),
            'boarding_group_snapshot' => $this->simulateBoardingGroup($segmentPassenger->segment->cabin_class, $segmentPassenger->seat->row_number ?? 0),
            'boarding_time_snapshot' => $departureTime->copy()->subMinutes(45),
            'issued_at' => now(),
        ]);

        // Generate QR Signature
        $boardingPass->update([
            'qr_signature' => $this->qrService->generateSignature($boardingPass)
        ]);

        BoardingPassIssued::dispatch($boardingPass);

        return $boardingPass;
    }

    /**
     * Mencetak ulang boarding pass (misal karena ganti kursi/gate)
     */
    public function regenerate(BookingSegmentPassenger $segmentPassenger): BoardingPass
    {
        // Revoke the old one
        BoardingPass::where('booking_segment_passenger_id', $segmentPassenger->id)
            ->whereIn('status', ['generated', 'active'])
            ->update([
                'status' => 'revoked',
                'revoked_at' => now()
            ]);

        // Generate new one
        $newBoardingPass = $this->generate($segmentPassenger);

        BoardingPassRegenerated::dispatch($newBoardingPass);

        return $newBoardingPass;
    }

    // --- Simulation Helpers ---
    private function simulateGate($originIata)
    {
        // Simulasi deterministik menggunakan hash IATA
        $hash = crc32($originIata);
        $zone = ($hash % 2 === 0) ? 'A' : 'B';
        $number = ($hash % 20) + 1;
        return $zone . $number;
    }

    private function simulateBoardingGroup($cabinClass, $rowNumber)
    {
        if ($cabinClass === 'business' || $cabinClass === 'first') return 'A';
        if ($rowNumber <= 15) return 'B';
        return 'C';
    }
}
