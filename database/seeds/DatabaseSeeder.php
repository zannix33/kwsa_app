<?php


use Illuminate\Database\Seeder;

use Database\Seeders\UserSeeder;
use Database\Seeders\EmployeeSeeder;
use Database\Seeders\CompanySeeder;
use Database\Seeders\PositionSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $this->call(UserSeeder::class);
        //$this->call(EmployeeSeeder::class);
        $this->call(PositionSeeder::class);
    }
}
