<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;

class CompanySeeder extends Seeder
{
    public function run()
    {
        $categories = ['Company', 'Property', 'Individual'];

        for ($i = 1; $i <= 40; $i++) {

            $category = $categories[array_rand($categories)];

            Client::create([
                'name' => 'Company ' . $i,
                'active' => rand(0, 1),
                'category' => $category,
                'age_limit' => $category === 'Individual'
                    ? rand(55, 65)
                    : null,
            ]);
        }
    }
}
