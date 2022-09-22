<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SubgenreController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\LoginController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/stocks',  [StockController::class, 'index']);

Route::get('/image', [StockController::class, 'images']);//StockControllerのshowImageメソッド発動

//アイテム検索
Route::get('/search', [StockController::class, 'search']);

//後で消す
Route::get('/search2', [StockController::class, 'search2']);

//videoアーカイブ
//音源アーカイブ

//素材投稿
Route::middleware('auth:sanctum')->group(function(){
    Route::post('/stocks/create', [StockController::class, 'create']);
    Route::post('/account/update', [UserController::class, 'update']);
});

Route::get('/stocks/getSubgenre', [SubgenreController::class, 'getSubgenre']);
Route::get('/stocks/subgenreSelectedByUrl', [SubgenreController::class, 'subgenreSelectedByUrl']);


Route::get('/stocks/{stock_id}', [StockController::class, 'single']);//urlのstock_idの部分に入力された数字をsingleメソッドに渡す
Route::get('/stock/author/{author_id}', [StockController::class, 'stocksByAuthorId']);
Route::get('/hoge/{author_id}', [UserController::class, 'index']);//idから投稿者情報を取得(作成中)Resoucers使ったほうがいい？
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



//会員登録
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);

Route::middleware('auth:sanctum')->get('/loginCheck',function (Request $request) {
    return $request->user();
});



Route::post('/logout', [LoginController::class, 'logout']);

Route::post('/account/checkOldPassword', [UserController::class, 'checkOldPassword']);