<?php

namespace App\Http\Controllers;
use App\Models\Stock;
use Illuminate\Http\Request;
use App\Http\Resources\StockResource;

use Illuminate\Support\Facades\Storage;//ファイルアップロード・削除関連
use Image;
//use App\Models\User;

use DB;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){//ジャンル関係なく全部（最終的には不要になる）
        $stocks =StockResource::collection(Stock::orderBy('created_at', 'desc')->paginate(30));//paginateの引数を変数にしたい
        return $stocks;        
    }
   
    public function search(Request $request){
        $pat = '%' . addcslashes($request->key, '%_\\') . '%';
         
        //return $request;
        
        $stock = Stock::query();
  
        if($request->genre && $request->subgenre==null && $request->key){
            //return 'ジャンルあり、サブジャンルなし、キーワードあり';
            $stock->where('genre',$request->genre)->where('name','LIKE',$pat); 
        }

        if($request->genre && $request->subgenre==null && $request->key==null){
            //return '親ジャンルあり、サブジャンルなし、キーワードなし';
            $stock->where('genre',$request->genre);
        }

        if($request->subgenre && $request->key==null){
            //return 'サブジャンルあり、キーワードなし';
            $stock->where('subGenre',$request->subgenre);
        }        

        if($request->subgenre && $request->key){
            //return 'サブジャンルあり、キーワードあり';
            $stock->where('subGenre',$request->subgenre)->where('name','LIKE',$pat); 
        }

        if($request->genre==null && $request->subgenre==null && $request->key=null){
            return '全部ない　もういい？';
        }
        return StockResource::collection($stock->orderBy('created_at', 'desc')->paginate(10));
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, Stock $stock)
    {       
        $filename = substr(bin2hex(random_bytes(8)), 0, 8);//ランダムなファイル名を定義
        $extention = $request->form['extention']; //ファイルの拡張子を取得
        
        $stock->fill(array_merge($request->form,
            //request以外から生成してレコードに保存する必須のカラムの内容
             ['path' => $filename.'.'.$extention],
             ['filename' => $filename],
             ['author_id' => $request->userId],
        ))->save(); 
        
        //ランダムなファイル名.拡張子をファイル名に指定して保存
        $request->file('files')[0]->storeAs('private/stocks', $filename.'.'.$extention);

        //投稿時に走らせるとやっぱり重い。認証システム入れたら承認時に変換する方式にしたい
        if($request->form['genre']=='image'){
            $stock->ConversionImage($request->file('files')[0], $filename);//画像なら変換
        }else if($request->form['genre']=='video'){
            $stock->ConversionVideo($request->form['extention'],$filename);//動画なら変換
        }else if($request->form['genre']=='audio'){
            $stock->ConversionAudio($request->form['extention'],$filename);//音声なら変換
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Stock  $stock
     * @return \Illuminate\Http\Response
     */


    public function show()
    {
        $stock = Stock::all();//stocksテーブルの情報をすべて取得
        return new StockResource($stock);//StockResourceにデータを渡してjsonに変換してもらおう
    }

 
    public function single(Stock $stockModel,$stock_id)
    {   //url上の数値を取得
        $stock = Stock::find($stock_id);//受け取った数値と一致するIDのレコードを取得
        $stock->size = $stockModel->calcFileSize(Storage::size('private/stocks/'.$stock->path));//販売データのサイズを単位変換して取得        
        $stock->fileType =pathinfo($stock->path, PATHINFO_EXTENSION);//販売データの拡張子取得

        //縦横サイズ取得
        if($stock->genre=="image"){
            $stock->info = $stockModel->getWhSizeImg(storage_path(('app/private/stocks/'. $stock->path)));//販売テータのサーバーパス取得

        }elseif($stock->genre=="video"){
            $stock->info = $stockModel->getVideoInfo($stock->filename);
        }elseif($stock->genre=="audio"){
            $stock->info = $stockModel->getAudioInfo($stock_id);
           
        }
        return new StockResource($stock);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Stock  $stock
     * @return \Illuminate\Http\Response
     */
    public function edit(Stock $stock)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Stock  $stock
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Stock $stock)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Stock  $stock
     * @return \Illuminate\Http\Response
     */
    public function destroy(Stock $stock)
    {
        //
    }

    /**
     * 投稿者に紐付く商品を取得するFunction
     */
    public function stocksByAuthorId($author_id)
    {
        $author = Stock::find($author_id);
        return new StockCollection($stock->stocks);
    }
  
}