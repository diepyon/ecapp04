<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockController;


Route::get('{any}', function () {
    return view('vue');
})->where('any', '.*');