<?php

namespace App\Services;

use App\Models\Aircraft;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class SeatMapGeneratorService
{
    /**
     * Generate master seat map for an aircraft based on preset.
     */
    public function generateForAircraft(Aircraft $aircraft, string $presetKey)
    {
        $presets = config('seatmap.presets');

        if (!isset($presets[$presetKey])) {
            throw new InvalidArgumentException("Preset {$presetKey} tidak ditemukan di konfigurasi.");
        }

        $preset = $presets[$presetKey];
        $letters = $preset['letters'];
        $aisleAfter = $preset['aisle_after'];

        DB::beginTransaction();
        try {
            $aircraft->seats()->delete();

            $seatsToInsert = [];
            
            foreach ($preset['rows'] as $rowConfig) {
                for ($rowNumber = $rowConfig['start']; $rowNumber <= $rowConfig['end']; $rowNumber++) {
                    
                    $isExitRow = in_array($rowNumber, $rowConfig['exit_rows'] ?? []);

                    foreach ($letters as $index => $letter) {
                        $isWindow = ($index === 0 || $index === count($letters) - 1);
                        
                        $isAisle = false;
                        if (in_array($letter, $aisleAfter)) {
                            $isAisle = true;
                        } elseif ($index > 0 && in_array($letters[$index - 1], $aisleAfter)) {
                            $isAisle = true;
                        }

                        $seatsToInsert[] = [
                            'aircraft_id' => $aircraft->id,
                            'seat_number' => $rowNumber . $letter,
                            'cabin_class' => $rowConfig['class'],
                            'row_number' => $rowNumber,
                            'seat_letter' => $letter,
                            'is_window' => $isWindow,
                            'is_aisle' => $isAisle,
                            'is_exit_row' => $isExitRow,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
            }

            foreach (array_chunk($seatsToInsert, 100) as $chunk) {
                DB::table('aircraft_seats')->insert($chunk);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
