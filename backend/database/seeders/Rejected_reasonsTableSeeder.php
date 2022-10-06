<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

//追記
use Illuminate\Support\Facades\DB; 
use DateTime;


class Rejected_reasonsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //追記

        DB::table('rejected_reasons')->truncate(); //2回目実行の際にシーダー情報をクリア
        //共通
        DB::table('rejected_reasons')->insert([//ここは正確なテーブル名
            'reason' => 'other',
            'reasonText' => 'その他',
            'genre' => 'all',
            'created_at' => new DateTime(),
            'updated_at' => new DateTime(),            
        ]);        

        //画像
        DB::table('rejected_reasons')->insert([//ここは正確なテーブル名
            'reason' => 'margin',
            'reasonText' => '素材の外に不要な余白が存在する（黒い帯等）',
            'genre' => 'image',
            'created_at' => new DateTime(),
            'updated_at' => new DateTime(),            
        ]);         
        
  
        //映像
        DB::table('rejected_reasons')->insert([//ここは正確なテーブル名
            'reason' => 'blockNoise',
            'reasonText' => '映像にブロックノイズが目立つ',
            'genre' => 'video',
            'created_at' => new DateTime(),
            'updated_at' => new DateTime(),            
        ]);

        DB::table('rejected_reasons')->insert([//ここは正確なテーブル名
            'reason' => 'uselessVideo',
            'reasonText' => 'メインコンテンツ時間が長すぎる(何も映っていない時間等のトリミングが必要)',
            'genre' => 'video',
            'created_at' => new DateTime(),
            'updated_at' => new DateTime(),            
        ]);
                
        
        //音源
        DB::table('rejected_reasons')->insert([//ここは正確なテーブル名
            'reason' => 'uselessAudio',
            'reasonText' => '無音の時間が長すぎる(トリミングが必要)',
            'genre' => 'audio',
            'created_at' => new DateTime(),
            'updated_at' => new DateTime(),            
        ]);


        DB::table('rejected_reasons')->insert([//ここは正確なテーブル名
            'reason' => 'OtherSite',
            'reasonText' => 'ほかの販売サイトにも登録されている（正規の投稿者か見分けがつかない）/フリー素材として配布されている',
            'genre' => 'all',
            'created_at' => new DateTime(),
            'updated_at' => new DateTime(),            
        ]);

        DB::table('rejected_reasons')->insert([//ここは正確なテーブル名
            'reason' => 'LowQuality',
            'reasonText' => '素材に向かない/クオリティーが低い',
            'genre' => 'all',
            'created_at' => new DateTime(),
            'updated_at' => new DateTime(),            
        ]);    

        DB::table('rejected_reasons')->insert([//ここは正確なテーブル名
            'reason' => 'morals',
            'reasonText' => '公序良俗に反する',
            'genre' => 'all',
            'created_at' => new DateTime(),
            'updated_at' => new DateTime(),            
        ]); 

        DB::table('rejected_reasons')->insert([//ここは正確なテーブル名
            'reason' => 'fileType',
            'reasonText' => '予期しないファイル形式',
            'genre' => 'all',
            'created_at' => new DateTime(),
            'updated_at' => new DateTime(),            
        ]);         

    
        DB::table('rejected_reasons')->insert([//ここは正確なテーブル名
            'reason' => 'duplication',
            'reasonText' => '重複・ほぼ同じ作品が既に存在する',
            'genre' => 'all',
            'created_at' => new DateTime(),
            'updated_at' => new DateTime(),            
        ]); 
     
        DB::table('rejected_reasons')->insert([//ここは正確なテーブル名
            'reason' => 'lowFileQuality',
            'reasonText' => '画質・音質が低い、ノイズが多い、音が割れているなど',
            'genre' => 'all',
            'created_at' => new DateTime(),
            'updated_at' => new DateTime(),            
        ]);               

        
       
    }
}
