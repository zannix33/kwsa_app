<?php

namespace Database\Seeders;

use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $faker = Faker::create();

        User::updateOrCreate(
            [
                'email' => 'testadmin@kwsa.com',
            ],
            [
                'firstname' => 'Admin',
                'lastname' => 'KSA',
                'email' => 'admin@kwsa.com',
                'password' => Hash::make('kwsa12345'),
                'email_verified_at' => now(),
                'birthdate' => $faker->dateTimeBetween('-55 years', '-21 years'),
            ]
        );
    }
}
