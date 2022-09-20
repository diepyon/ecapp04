<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Stock;
use Illuminate\Support\Facades\Storage;//ファイルのリネームと削除で必要

use Image;//画像リサイズライブラリ

//ffmpeg関連
use FFMpeg;
use FFMpeg\Format\VideoInterface;
use FFMpeg\Media\Video;
use ProtoneMedia\LaravelFFMpeg\Filters\WatermarkFactory;
use ProtoneMedia\LaravelFFMpeg\Filters\WatermarkFilter;
use ProtoneMedia\LaravelFFMpeg\Filesystem\Media;

class Stock extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'fileInfo' => 'json',
        'tags' => 'json',
    ];
    use HasFactory;

    public function Stock()
    {
        return $this->hasMany(Stock::class);
        //これいるのか？なくても動いた
    }

    private function gcd( $a, $b ) {

        //最大公約数を計算
        if( $a === 0 ) return $a ;
        $diff = $a > $b ? $a - $b : $b - $a ;
        $A = $diff ;
        $B = $b ;
        if( $B - $A ) {
            $A = $b ;
            $B = $diff ;
        }
        while( true ) {
            if( $B === 0 ) return $A ;
            $A %= $B ;
            if( $A === 0 ) return $B ;
            $B %= $A ;
        }
    }

    static function aspect($a, $b)
        {//アスペクト比を求める関数
            if ($a === 0) {
                return $a ;
            }
            $diff = $a > $b ? $a - $b : $b - $a ;
            $A = $diff ;
            $B = $b ;
            if ($B - $A) {
                $A = $b ;
                $B = $diff ;
            }
            while (true) {
                if ($B === 0) {
                    return $a/$A.':'. $b/$A ;
                }
                $A %= $B ;
                if ($A === 0) {
                    return $a/$B.':'. $b/$B ;
                }
                $B %= $A ;
            }
        }    

    public static function ConversionImage($file, $filename)
    {//画像変換メソッド
        /*----------
        縮小サイズで保存（ループ一覧用）
        ----------*/
        Image::make($file)->resize(800, 280, function ($constraint) {
            $constraint->aspectRatio();
        })->save(storage_path(('app/public/stock_thumbnail/'. $filename.'.png'), 100));
        

        $imageWidth =Image::make($file)->width();//画像の横幅を取得
        $fontsize = $imageWidth*0.08;//画像に見合った透かしのフォントサイズを定義

        $sample = 'sample  sample  sample  sample  sample  sample  sample'."\n\n";
        for ($i =1; $i <= 5; $i++) {
            $sample = $sample.$sample;
        }//sample文字をたくさん挿入
            
        $x=100;
        $y=100;//挿入座標
        
        /*----------
        圧縮ありの透かし(singleページ用)
        ----------*/
        Image::make($file)
        ->text($sample, $x, $y, function ($font) use ($fontsize) {
            $font->file('assets/fonts/SawarabiGothic-Regular.ttf');
            $font->size($fontsize);        // 文字サイズ
            $font->color(array(255, 255, 255, 0.5));   // 文字の色
            $font->align('center'); // 横の揃え方（left, center, right）
            $font->valign('top');   // 縦の揃え方（top, middle, bottom）
            $font->angle(30);       // 回転（フォントが指定されていないと無視されます。）
        })
        ->resize(1000, 1000, function ($constraint) {
            $constraint->aspectRatio();
        })//singleページ用に圧縮
        ->save(storage_path('app/public/stock_sample/'. $filename.'.png'), 100);


        /*----------
        圧縮なしの透かし(サンプルダウンロード用)
        ----------*/
        Image::make($file)
        ->text($sample, $x, $y, function ($font) use ($fontsize) {
            $font->file('assets/fonts/SawarabiGothic-Regular.ttf');
            $font->size($fontsize);        // 文字サイズ
            $font->color(array(255, 255, 255, 0.5));   // 文字の色
            $font->align('center'); // 横の揃え方（left, center, right）
            $font->valign('top');   // 縦の揃え方（top, middle, bottom）
            $font->angle(30);       // 回転（フォントが指定されていないと無視されます。）
        })
        ->save(storage_path('app/public/stock_download_sample/'. $filename.'.png'), 100);
    }
    public static function ConversionVideo($extention, $filename)
    {
        //動画変換メソッド
        $file = 'private/stocks/'.$filename.'.mp4';//元ファイルのパス

            $format = new \FFMpeg\Format\Video\X264('aac');
            FFMpeg::open('private/stocks/'.$filename.'.'.$extention)
            ->export()
            ->addFilter('-an')//音を消す
            ->inFormat($format)
            ->save('private/stocks/'.$filename.'_tmp.mp4');//上書きできないので別名で保存
            
            Storage::delete($file);//上書きできないので元ファイルを削除
            Storage::move('private/stocks/'.$filename.'_tmp.mp4', $file);//一時ファイルの名前を正しい名前に変更

        //動画サムネイル画像を生成
        $media = FFMpeg::fromDisk('local')
        ->open($file)
        ->getFrameFromSeconds(1)//1フレーム目
        ->export()
        ->save('public/stock_thumbnail/'.$filename.'.png');//ファイルをpngに変換




        //どんな透かしを入れるのかを定義
        $watermark = new WatermarkFilter(storage_path('app/private/watermark/logo.png'), [
        'position'=>'relative',
        'center' =>0,
         ]);

        /*----------
        リサイズ
        ----------*/
        $stock = new Stock();

        //元ファイルの情報を取得
        $media = FFMpeg::open($file);
        $mediaStreams = $media->getStreams()[0];

        //var_dump($mediaStreams);

        $height = $mediaStreams->get('height');// 解像度(縦)を取得
        $width = $mediaStreams->get('width');// 解像度(横)を取得
        $aspect=$stock->gcd($width, $height);//アスペクト比を取得

        if ($height >= 2160) {
            $size ='4k'; //サイズを指定
            $stock->resizeVideo($size, $filename, $width, $height);//サイズの名前,ファイルid,横幅,高さ
        }
        
        //fullHD以上なら1080pに変換
        if ($height >= 1080) {
            $size='hd';
            $stock->resizeVideo($size, $filename, $width, $height);
        }
        
        //480以上ならSD画質に変換
        if ($height >= 480) {
            $size='sd';
            $stock->resizeVideo($size, $filename, $width, $height);
        }


        //動く低画質サムネイル
        FFMpeg::fromDisk('local')
            ->open($file)
            ->export()
            ->inFormat(new \FFMpeg\Format\Video\X264('aac'))
            ->addFilter(['-r', '5'])  //5fps
            ->addFilter(function ($filters) use($width,$height){
                $changeWidth = round($width*360/$height);
                if ($changeWidth%2==1) {
                    $changeWidth = $changeWidth+1;
                }
                $filters->resize(new \FFMpeg\Coordinate\Dimension($changeWidth, 360));
            })               
            ->save('public/stock_thumbnail/'.$filename.'.mp4');

         
        /*----------
        圧縮ありの透かし(singleページ用)
        ----------*/
        $stock->resizeVideo('stock_sample', $filename);//480pに変換

        FFMpeg::open('private/stocks/'.$filename.'_stock_sample.mp4')//480pに変換したファイルを読み込み
            ->export()
            ->inFormat(new \FFMpeg\Format\Video\X264('aac'))
            ->addFilter($watermark)
            ->save('public/stock_sample/'. $filename.'.mp4');//480pに変換したファイルに透かしを付けて保存

        //これを下回る画質の動画をアップしたどうなるのかも知りたい。

         Storage::delete('private/stocks/'.$filename.'_stock_sample.mp4');//480pのデータ削除

        /*----------
        圧縮なしの透かし(サンプルダウンロード用)
        ----------*/
        FFMpeg::open($file)//mp4形式のデータを読み込み
            ->export()
            ->inFormat(new \FFMpeg\Format\Video\X264('aac'))
            ->addFilter($watermark)
            ->save('public/stock_download_sample/'. $filename.'.mp4');
    }

    public static function ConversionAudio($extention, $filename){
       //サンプル用にmp3に変換する
        $file = 'private/stocks/'.$filename.'.'.$extention;//元ファイルのパス

        //透かしなしwav(買った人が使う)ビットレートとかサイト内で統一したいから拡張子が同じでも変換
        //元データがmp3の場合は音質が上がるわけではないのでそれを明記する必要がある
        $ffMpeg = FFMpeg::fromDisk('local')->open([$file])->export();
        $ffMpeg->inFormat(new \FFMpeg\Format\Audio\Wav);
        $ffMpeg->save('private/stocks/'.$filename.'_convert.wav');  

        //透かしなしmp3(買った人が使う)ビットレートとかサイト内で統一したいから拡張子が同じでも変換
        $ffMpeg = FFMpeg::fromDisk('local')->open([$file])->export();
        $ffMpeg->inFormat(new \FFMpeg\Format\Audio\Mp3);
        $ffMpeg->save('private/stocks/'.$filename.'_convert.mp3');          

        //ページに埋め込むmp3（サンプル音声付、再現性のあるサンプルにしたいからあえて元データを使って合成)
        $ffMpeg = FFMpeg::fromDisk('local')->open(['private/watermark/sample.mp3', $file])->export(); 
        $ffMpeg->addFilter('[0][1]', 'amix=inputs=2:duration=shortest', '[a]'); 
        //$ffMpeg->addFormatOutputMapping(new \FFMpeg\Format\Video\WebM, Media::make('local', 'public/stock_sample/'.$filename.'.mp3'), ['[a]']);
        $ffMpeg->addFormatOutputMapping(new \FFMpeg\Format\Audio\Mp3, Media::make('local', 'public/stock_sample/'.$filename.'.mp3'), ['[a]']);
        $ffMpeg->save();

        //ダウンロード用にmp3サンプルをコピー
        Storage::copy('public/stock_sample/'.$filename.'.mp3', 'public/stock_download_sample/'.$filename.'.mp3');

        //アーカイブのループサムネイル用にmp3サンプルをコピー（使うかはわからん）
        Storage::copy('public/stock_sample/'.$filename.'.mp3', 'public/stock_thumbnail/'.$filename.'.mp3');
    }    

    //ビデオリサイズ関数。
    private function resizeVideo($size, $filename)
    {
        $stock = new Stock();
        $file = 'private/stocks/'.$filename.'.mp4';//元ファイルのパス
        $media = FFMpeg::open($file);
        $mediaStreams = $media->getStreams()[0];

        // 解像度(縦)を取得
        $height = $mediaStreams->get('height');
        // 解像度(横)を取得
        $width = $mediaStreams->get('width');
        //アスペクト比を取得
        $aspect=$stock->gcd($width, $height);//アスペクト比を取得

        //指定されたサイズにより解像度を決定
        if($size=="4k"){
            $resolution = 2160;
        }elseif($size=="hd"){
            $resolution = 1080;
        }elseif($size=="sd"){
            $resolution = 480;
        }elseif($size=="stock_sample"){
            $resolution = 480;
        }

        FFMpeg::open($file)->addFilter(function ($filters) use($width,$height,$resolution){
            $changeWidth = round($width*$resolution/$height);
            if ($changeWidth%2==1) {
                $changeWidth = $changeWidth+1;
            }
            $filters->resize(new \FFMpeg\Coordinate\Dimension($changeWidth, $resolution));
        })
        ->export()
        ->toDisk('local')
        ->inFormat(new \FFMpeg\Format\Video\X264('aac'))
        ->save('private/stocks/'.$filename.'_'.$size.'.mp4');
    }

        //サイズ別にビデオをまとめて変換
        public function videoEncode($height,$width, $stock_filename,$stock_path){
            //4以上なら4kに変換(ファイルサイズ制限がdocker側にあるので動作確認できていない)
            if($height >= 2160){
                $size ='4k'; //サイズを指定
                $this->resizeVideo($size,$stock_filenam);
                
            }                
            
            //fullHD以上なら1080pに変換
            if($height >= 1080){
                $size='hd';
                $this->resizeVideo($size,$stock_filename);                       
            }                     
            
            //480以上ならSD画質に変換
            if($height >= 480){
                $size='sd';
                $this->resizeVideo($size,$stock_filename);
            }
        }
        public function calcFileSize($size){
          {//ファイルサイズをいい感じの単位に調整する関数 
            $b = 1024;    // バイト 
            $mb = pow($b, 2);   // メガバイト 
            $gb = pow($b, 3);   // ギガバイト 
            
            switch(true){ 
                case $size >= $gb: 
                $target = $gb; 
                $unit = 'GB'; 
                break; 
                case $size >= $mb: 
                $target = $mb; 
                $unit = 'MB'; 
                break; 
                default: 
                $target = $b; 
                $unit = 'KB'; 
                break; 
            } 
            
            $new_size = round($size / $target, 2); 
            $file_size = number_format($new_size, 2, '.', ',') . $unit; 
            
            return $file_size; 
          }           
        }
        public function getWhSizeImg($file){//販売データの縦サイズと横サイズ取得

            $width =Image::make($file)->width();
            $height =Image::make($file)->height();

            $whSize=array(
                'width'=>$width,
                'height'=>$height,      
                'aspect'=>$this->aspect($width,$height),
            );
            return $whSize;
        }
        public function getVideoInfo($filename){
            $file = 'private/stocks/'.$filename.'.mp4';//元ファイルのパス      
            
            $media = FFMpeg::open($file);
            $mediaStreams = $media->getStreams()[0];
            $height = $mediaStreams->get('height');// 解像度(縦)を取得
            $width = $mediaStreams->get('width');// 解像度(横)を取得
                
            $info=array(
                'width'=>$width,
                'height'=>$height,      
                'aspect'=>$this->aspect($width,$height),
                'time'=>$this->sToM($media->getDurationInSeconds())
            );
            return $info;
        }
        public function getAudioInfo($stock_id){
            $stock = Stock::where('id', $stock_id)->first();
            $file =  'private/stocks/'.$stock->path;
            $media = FFMpeg::open($file);
            $mediaStreams =  $media->getStreams()[0];

            $info=array(
                'miriSeconds'=>$media->getDurationInMiliseconds(),//詳細な長さ
                'time'=>$this->sToM($media->getDurationInSeconds()),
                'bitrate'=>$mediaStreams->get('bit_rate')/$mediaStreams->get('sample_rate')/$mediaStreams->get('channels'),
                'sampringlate'=>$mediaStreams->get('sample_rate'),
                'channels'=>$mediaStreams->get('channels'),
            );
            return $info;
        }
        public function getAudioInfoByFilename($file){
            $file = 'private/stocks/'.$file; 
            $media = FFMpeg::open($file);
            $mediaStreams =  $media->getStreams()[0];

            $info=array(
                'miriSeconds'=>$media->getDurationInMiliseconds(),//詳細な長さ
                'time'=>$this->sToM($media->getDurationInSeconds()),
                'bitrate'=>$mediaStreams->get('bit_rate')/$mediaStreams->get('sample_rate')/$mediaStreams->get('channels'),
                'sampringlate'=>$mediaStreams->get('sample_rate'),
                'channels'=>$mediaStreams->get('channels'),
            );
            
            return $info;
        }        
        private  function sToM($seconds) {   
            //秒を分に変換
            $hours = floor($seconds / 3600);
            $minutes = floor(($seconds / 60) % 60);
            $seconds = $seconds % 60;
            $hms = sprintf("%02d:%02d",  $minutes, $seconds);
            return $hms;
        }

                
    
}