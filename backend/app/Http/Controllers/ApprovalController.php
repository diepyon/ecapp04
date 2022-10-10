<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock;
use App\Models\Rejected_reason;

class ApprovalController extends Controller
{
    public function index(Request $request,Stock $stock){
        return $stock->find($request->id)->update(['status' => 'publish',]);;        
    }
    public function getRejectedReasons(Request $request,Rejected_reason $Rejected_reason){
        $Rejected_reason = $Rejected_reason->where('genre',$request->genre)->orWhere('genre','all')->orderBy('id','desc')->get();
        return $Rejected_reason;
    }

    public function reject(Request $request,Stock $stock){
        $stock = $stock::find($request->id)->fill(array_merge($request->all(),
            //['status' => 'rejected'],//一旦オフ
        ))->save();     

        //キーと同じ名前のカラムを一括更新したい
        //update_atの日付をどうするか・・・

        return $stock;
    }    
}