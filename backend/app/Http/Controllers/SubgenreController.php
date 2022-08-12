<?php

namespace App\Http\Controllers;
use App\Models\Subgenre;
use Illuminate\Http\Request;
use DB;

class SubgenreController extends Controller
{
    public function getSubgenre(Subgenre $subgenre,Request $request)
    {   
        $genre  = $request->genre;
        return $subgenre->Where('genre',$genre)->get();
    }  
     public function subgenreSelectedByUrl(Subgenre $subgenre,Request $request){
        return $subgenre->Where('subgenre',$request->subgenre)->first();
    }
}