<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Region;

class RegionSeeder extends Seeder
{
    public function run()
    {
        $regions = [
            ['code' => 'NCR', 'name' => 'National Capital Region'],
            ['code' => 'CAR', 'name' => 'Cordillera Administrative Region'],
            ['code' => 'R1', 'name' => 'Region I - Ilocos Region'],
            ['code' => 'R2', 'name' => 'Region II - Cagayan Valley'],
            ['code' => 'R3', 'name' => 'Region III - Central Luzon'],
            ['code' => 'R4A', 'name' => 'Region IV-A - CALABARZON'],
            ['code' => 'R4B', 'name' => 'MIMAROPA Region'],
            ['code' => 'R5', 'name' => 'Region V - Bicol Region'],
            ['code' => 'R6', 'name' => 'Region VI - Western Visayas'],
            ['code' => 'NIR', 'name' => 'Negros Island Region'],
            ['code' => 'R7', 'name' => 'Region VII - Central Visayas'],
            ['code' => 'R8', 'name' => 'Region VIII - Eastern Visayas'],
            ['code' => 'R9', 'name' => 'Region IX - Zamboanga Peninsula'],
            ['code' => 'R10', 'name' => 'Region X - Northern Mindanao'],
            ['code' => 'R11', 'name' => 'Region XI - Davao Region'],
            ['code' => 'R12', 'name' => 'Region XII - SOCCSKSARGEN'],
            ['code' => 'R13', 'name' => 'Region XIII - Caraga'],
            ['code' => 'BARMM', 'name' => 'Bangsamoro Autonomous Region in Muslim Mindanao'],
        ];

        foreach ($regions as $region) {
            Region::updateOrCreate(
                ['code' => $region['code']],
                [
                    'name' => $region['name'],
                    'is_active' => true,
                ]
            );
        }
    }
}
