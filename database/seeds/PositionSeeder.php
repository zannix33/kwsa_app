<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    public function run()
    {
        $positions = [
            'HR',
            'Accounting',
            'Security Officer',
            'Security Guard',
            'Head Operation',
            'Officer',
            'Admin Compliance',
            'Inspector',
        ];

        foreach ($positions as $position) {
            Position::updateOrCreate(
                ['name' => $position],
                ['active' => true]
            );
        }
    }
}
