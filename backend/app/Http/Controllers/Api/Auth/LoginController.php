<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use \Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    public function login(User $user, Request $request)
    {
        // Log::info('ログを出せた');
        // Log::info(json_decode($request));

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $user = User::whereEmail($request->email)->first();
            $user->tokens()->delete();
            $token = $user->createToken("login:user{$user->id}")->plainTextToken;
            return response()->json(['token' => $token , 'user'=>$user], Response::HTTP_OK);
        }

        return response()->json('User Not Found.', Response::HTTP_INTERNAL_SERVER_ERROR);

        /*
            if($user->where('email',$request->email)->first()==false || Hash::check($request->password,$user->where('email',$request->email)->first()->password==false)){
                dd('ログイン失敗');
            }else{
                dd('ログイン成功');
                //ここにサンクタムに渡す処理を書くのか？
            }
            return response()->json(['login' => true], Response::HTTP_OK);
            //「login」的な処理が成功したらOKのレスポンスをvueに返す。名前や処理は未定。
             */
    }
    public function loginCheck($token)
    {
        dd($token);
    }
 
    public function logout()
    {
        Auth::logout();
        return response()->json(['message' => 'Logged out'], 200);
    }
}