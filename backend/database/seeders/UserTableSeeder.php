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
    DB::table('users')->truncate(); //2回目実行の際にシーダー情報をクリア
    DB::table('users')->insert([
        'name' => 'si_mid',
        'email' => 'si_mid@yahoo.co.jp',
        'password' => '$2y$10$lMS.w4oNg0PP8I8oeWwTROlQrBId4ZR7tc0FytR1pi7oXIUH7YSL.',
        'role'=>'administrator',
        'created_at' => new DateTime(),
        'updated_at' => new DateTime(),
    ]);
    DB::table('users')->insert([
        'name' => 'tomarimashita',
        'email' => 'tomarimashita@gmail.com',
        'password' => '$2y$10$lMS.w4oNg0PP8I8oeWwTROlQrBId4ZR7tc0FytR1pi7oXIUH7YSL.',
        'role'=>'user',
        'created_at' => new DateTime(),
        'updated_at' => new DateTime(),
    ]);   
    DB::table('users')->insert([
        'name' => 'aaa',
        'email' => 'aaa@aaa.com',
        'password' => '$2y$10$lMS.w4oNg0PP8I8oeWwTROlQrBId4ZR7tc0FytR1pi7oXIUH7YSL.',
        'role'=>'user',
        'created_at' => new DateTime(),
        'updated_at' => new DateTime(),
    ]);       
    }
}