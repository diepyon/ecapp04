<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

//use App\Models\Stock;//使えない
use DB;


class DownloadAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  ...$guards
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
            if (Auth::guard()->check()) {

                $author_id = DB::table('stocks')->where('id',$request->id)->first()->author_id;//作品の投稿者のID

                //購入済みの人ならって条件も追加の必要あり
                if(Auth::user()->role == 'administrator'||Auth::user()->id == $author_id){
                    return $next($request);
                }                
            }
            return response()->json(['status'=>'Unauthorized']);
    }
}