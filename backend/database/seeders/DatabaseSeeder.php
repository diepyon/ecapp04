<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //$this->call(StockTableSeeder::class);
        $this->call(StockSeeder::class);
        $this->call(SubgenresTableSeeder::class);
        $this->call(UserTableSeeder::class);
    }
}