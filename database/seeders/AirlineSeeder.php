<?php

namespace Database\Seeders;

use App\Models\Airline;
use Illuminate\Database\Seeder;

class AirlineSeeder extends Seeder
{
    public function run(): void
    {
        $airlines = [
            ['code' => 'GA', 'name' => 'Garuda Indonesia'],
            ['code' => 'JT', 'name' => 'Lion Air'],
            ['code' => 'QG', 'name' => 'Citilink'],
            ['code' => 'QZ', 'name' => 'AirAsia Indonesia'],
            ['code' => 'ID', 'name' => 'Batik Air'],
        ];

        foreach ($airlines as $airline) {
            Airline::firstOrCreate(['code' => $airline['code']], $airline);
        }
    }
}