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

    public function reject(Request $request,Rejected_reason $Rejected_reason){
        return 'hoge';
    }    
}