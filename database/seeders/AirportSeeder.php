<?php

namespace Database\Seeders;

use App\Models\Airport;
use Illuminate\Database\Seeder;

class AirportSeeder extends Seeder
{
    public function run(): void
    {
        $airports = [
            ['iata_code' => 'CGK', 'name' => 'Soekarno-Hatta International Airport', 'city' => 'Jakarta'],
            ['iata_code' => 'DPS', 'name' => 'Ngurah Rai International Airport', 'city' => 'Bali'],
            ['iata_code' => 'SUB', 'name' => 'Juanda International Airport', 'city' => 'Surabaya'],
            ['iata_code' => 'KNO', 'name' => 'Kualanamu International Airport', 'city' => 'Medan'],
            ['iata_code' => 'YIA', 'name' => 'Yogyakarta International Airport', 'city' => 'Yogyakarta'],
        ];

        foreach ($airports as $airport) {
            Airport::firstOrCreate(['iata_code' => $airport['iata_code']], $airport);
        }
    }
}