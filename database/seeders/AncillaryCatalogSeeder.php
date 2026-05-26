<?php

namespace Database\Seeders;

use App\Models\AncillaryService;
use Illuminate\Database\Seeder;

class AncillaryCatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'code' => 'BG15',
                'name' => 'Extra Baggage 15kg',
                'type' => 'baggage',
                'description' => 'Tambahan kuota bagasi tercatat seberat 15 kilogram.',
                'base_price' => 150000.00,
                'is_active' => true,
            ],
            [
                'code' => 'BG20',
                'name' => 'Extra Baggage 20kg',
                'type' => 'baggage',
                'description' => 'Tambahan kuota bagasi tercatat seberat 20 kilogram.',
                'base_price' => 200000.00,
                'is_active' => true,
            ],
            [
                'code' => 'PRIO',
                'name' => 'Priority Boarding',
                'type' => 'priority_boarding',
                'description' => 'Akses naik pesawat lebih awal dan jalur khusus di antrean boarding.',
                'base_price' => 75000.00,
                'is_active' => true,
            ],
            [
                'code' => 'MEAL_PREM',
                'name' => 'Premium Meal',
                'type' => 'meal',
                'description' => 'Sajian makanan premium siap santap di atas pesawat.',
                'base_price' => 120000.00,
                'is_active' => true,
            ],
            [
                'code' => 'LOUNGE',
                'name' => 'Executive Lounge Access',
                'type' => 'lounge',
                'description' => 'Akses ke executive lounge bandara keberangkatan maksimal 3 jam sebelum penerbangan.',
                'base_price' => 250000.00,
                'is_active' => true,
            ],
        ];

        foreach ($services as $serviceData) {
            AncillaryService::updateOrCreate(
                ['code' => $serviceData['code']],
                $serviceData
            );
        }
    }
}
