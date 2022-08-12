<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubgenresTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('subgenres')->truncate(); //2回目実行の際にシーダー情報をクリア （ようわからん）
        DB::table('subgenres')->insert([
            'genre' => 'image',

            'subgenre' => 'illust',
            'subgenreText'=>'イラスト',
        ]);
        DB::table('subgenres')->insert([
            'genre' => 'image',

            'subgenre' => 'photo',
            'subgenreText'=>'写真',

        ]);
        DB::table('subgenres')->insert([
            'genre' => 'video',

            'subgenre' => 'real',
            'subgenreText'=>'実写',

        ]);
        DB::table('subgenres')->insert([
            'genre' => 'video',

            'subgenre' => 'animation',
            'subgenreText'=>'アニメーション',

        ]);
        DB::table('subgenres')->insert([
            'genre' => 'video',

            'subgenre' => 'cg',
            'subgenreText'=>'CG',

        ]);


        DB::table('subgenres')->insert([
            'genre' => 'audio',

            'subgenre' => 'bgm',
            'subgenreText'=>'BGM',

        ]);

        DB::table('subgenres')->insert([
            'genre' => 'audio',

            'subgenre' => 'sample',
            'subgenreText'=>'効果音',

        ]);
    }
}
