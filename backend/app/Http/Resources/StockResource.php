<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

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
        //return parent::toArray($request);
        $stock = new Stock;

        //いらんと思う
        if( $this->genre=='image'){
            $mediaInfo = $this->info;
        }elseif($this->genre=='audio'){
            //$mediaInfo = $stock->getAudioInfo($this->id);
        }else{
            $mediaInfo = null;
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
            'subGenre'=> $this->detail,
            'updated_at'=> $this->updated_at,
            'fileInfo'=> $this->fileInfo,
            'tags'=> explode(',', $this->tags),
        ];        
    }
}