<?php

namespace App\Http\Resources;
use App\Http\Resources\StockResource;//追記
use Illuminate\Http\Resources\Json\ResourceCollection;

class StockCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return [
        //     'stock_count' => $this->count(),
        //     'hoge' => $this->collection,
        //     'links' => [
        //         'self' => $request->fullUrl(),
        //     ]
        // ];

    }
}
