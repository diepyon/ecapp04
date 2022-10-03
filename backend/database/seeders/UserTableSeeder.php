<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; 
use DateTime;


class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    //追記
    DB::table('users')->insert([
        'name' => 'si_mid',
        'email' => 'si_mid@yahoo.co.jp',
        'password' => '$2y$10$lMS.w4oNg0PP8I8oeWwTROlQrBId4ZR7tc0FytR1pi7oXIUH7YSL.',
        'role'=>'administrator',
        'created_at' => new DateTime(),
        'updated_at' => new DateTime(),
    ]);
    }
}