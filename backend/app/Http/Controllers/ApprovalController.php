<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock;

class ApprovalController extends Controller
{
    public function index(Request $request,Stock $stock){
        return $stock->find($request->id)->update(['status' => 'publish',]);;        
    }
}
