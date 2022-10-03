<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

use DB;

use App\Models\Stock;

class StockResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $stock = new Stock;

        
        //総再生時間の情報が存在するならxx:xx形式に整形
        $duration = null;
        if(isset($this->fileInfo['miriSeconds'])){
            $duration = $this->fileInfo['miriSeconds']/1000;
            $duration = $stock->sToM($duration);
        }
        if($duration == '00:00'){
            $duration =$this->fileInfo['miriSeconds']/1000;
        }
        
        $fileSize = null;
        if(isset($this->fileInfo['filesize'])){
            $fileSize = $this->fileInfo['filesize'];
            $fileSize = $stock->calcFileSize($fileSize);
        }

        $subGenre = null;
        if(isset($this->subGenre)){
            $subGenre =  DB::table('subgenres')->where('subgenre', $this->subGenre)->first()->subgenreText;
        }

        $authorName =  DB::table('users')->where('id', $this->author_id)->first()->name;
        $authorIcon =  DB::table('users')->where('id', $this->author_id)->first()->icon;
        
        $samplingrate = null;
        if(isset($this->fileInfo['samplingrate'])){
            $samplingrate = $this->fileInfo['samplingrate']/1000 .'kHz' ;
        }

        $bitDeapth = null;
        if(isset($this->fileInfo['bitDeapth'])){
            $bitDeapth = $this->fileInfo['bitDeapth'] .'bit' ;
        }

        return [
            'id' => $this->id,
            'created_at' => $this->created_at,
            'detail'=> $this->detail,
            'fee'=> $this->fee,
            'filename'=> $this->filename,
            'genre'=> $this->genre,
            'name'=> $this->name,
            'path'=> $this->bgm,
            'subGenre'=> $subGenre,
            'updated_at'=> $this->updated_at,
            'fileInfo'=> $this->fileInfo,
            'samplingrate'=>$samplingrate,
            'bitDeapth'=>$bitDeapth,
            'duration'=> $duration,
            'fileSize'=> $fileSize,
            'fileType'=>substr($this->path, strrpos($this->path, '.') + 1),
            'tags'=> explode(',', $this->tags),
            'author_id'=> $this->author_id,
            'author_name'=> $authorName,
            'author_icon'=>$authorIcon,
            'status'=>$this->status,
        ];        
    }
}