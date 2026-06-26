<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // 20 Security Guards
        for ($i = 1; $i <= 20; $i++) {

            User::create([
                'name' => 'EMP-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'firstname' => $faker->firstName(),
                'middlename' => $faker->firstName(),
                'lastname' => $faker->lastName(),

                'address' => $faker->streetAddress(),
                'city' => $faker->city(),
                'province' => $faker->state(),

                'email' => "guard{$i}@example.com",
                'phone' => $faker->numerify('09#########'),

                'religion' => 'Roman Catholic',

                'spouse_name' => $faker->name(),
                'spouse_birthdate' => $faker->dateTimeBetween('-60 years', '-25 years'),

                'beneficiary_name' => $faker->name(),
                'beneficiary_contact' => $faker->numerify('09#########'),

                'password' => Hash::make('password'),

                'civil_status' => $faker->randomElement([
                    'Single',
                    'Married',
                    'Widowed'
                ]),

                'birthdate' => $faker->dateTimeBetween('-55 years', '-21 years'),

                'height' => rand(160, 185) . ' cm',
                'weight' => rand(55, 95) . ' kg',

                'sss' => $faker->numerify('##-#######-#'),
                'tin' => $faker->numerify('###-###-###'),
                'pagibig' => $faker->numerify('####-####-####'),
                'philhealth' => $faker->numerify('##-#########-#'),

                'bloodtype' => $faker->randomElement([
                    'A+',
                    'A-',
                    'B+',
                    'B-',
                    'AB+',
                    'AB-',
                    'O+',
                    'O-'
                ]),

                'position' => 'Security Guard',

                'lesp_num' => 'LESP-' . rand(10000, 99999),
                'lesp_issued' => now()->subYears(rand(1, 5))->format('Y-m-d'),
                'lesp_expiry' => now()->addYears(rand(1, 3)),

                'date_hired' => $faker->dateTimeBetween('-10 years', 'now'),
                'dt_date' => $faker->dateTimeBetween('-5 years', 'now'),
            ]);
        }

        // 10 Security Officers
        for ($i = 21; $i <= 30; $i++) {

            User::create([
                'name' => 'EMP-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'firstname' => $faker->firstName(),
                'middlename' => $faker->firstName(),
                'lastname' => $faker->lastName(),

                'address' => $faker->streetAddress(),
                'city' => $faker->city(),
                'province' => $faker->state(),

                'email' => "officer{$i}@example.com",
                'phone' => $faker->numerify('09#########'),

                'religion' => 'Roman Catholic',

                'spouse_name' => $faker->name(),
                'spouse_birthdate' => $faker->dateTimeBetween('-60 years', '-25 years'),

                'beneficiary_name' => $faker->name(),
                'beneficiary_contact' => $faker->numerify('09#########'),

                'password' => Hash::make('password'),

                'civil_status' => $faker->randomElement([
                    'Single',
                    'Married',
                    'Widowed'
                ]),

                'birthdate' => $faker->dateTimeBetween('-55 years', '-25 years'),

                'height' => rand(160, 185) . ' cm',
                'weight' => rand(60, 100) . ' kg',

                'sss' => $faker->numerify('##-#######-#'),
                'tin' => $faker->numerify('###-###-###'),
                'pagibig' => $faker->numerify('####-####-####'),
                'philhealth' => $faker->numerify('##-#########-#'),

                'bloodtype' => $faker->randomElement([
                    'A+',
                    'A-',
                    'B+',
                    'B-',
                    'AB+',
                    'AB-',
                    'O+',
                    'O-'
                ]),

                'position' => 'Security Officer',

                'lesp_num' => 'LESP-' . rand(10000, 99999),
                'lesp_issued' => now()->subYears(rand(1, 5))->format('Y-m-d'),
                'lesp_expiry' => now()->addYears(rand(1, 3)),

                'date_hired' => $faker->dateTimeBetween('-15 years', 'now'),
                'dt_date' => $faker->dateTimeBetween('-5 years', 'now'),
            ]);
        }
    }
}
