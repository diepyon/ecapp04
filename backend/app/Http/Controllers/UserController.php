<?php

namespace App\Http\Controllers;
use App\Models\User;   //name,email,passwordだけfillableなので注意

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use Image;

//ffmpeg関連
use FFMpeg;
use ProtoneMedia\LaravelFFMpeg\Filesystem\Media;

class UserController extends Controller
{
    public function index(User $user,$author_id)
    {
        $author =  User::where('id', $author_id)->first(); //作品の投稿者情報     
        return $author;//全部返してもいい？メールアドレス見られちゃう？
    }
    public function update(User $user,Request $request)
    {           
        $userRecord =  User::where('id', $request->id)->first();
        $beforeUpdatedAt = $userRecord->updated_at;//更新前のupdated_at
        
        //アイコンを最低限のサイズに縮
        if($request->extention && $userRecord->icon == null){
            //ファイルが（拡張子が）あり、アイコンが未設定なら画像を新規投稿     
            //サイズを縮小したい
            Image::make($request->file('files')[0])->resize(300, 300, function ($constraint) {
                $constraint->aspectRatio();
            })->save(storage_path(('app/public/user_icon/'. $userRecord->id.'.jpg'), 100));               
            
            $userRecord->update(['icon' => $userRecord->id.'.jpg',]);

            //画像だけ更新されてもupdated_atは更新されないとかも？？？
            $userRecord->touch();//updated_atを更新

            $request->file('files')[0]->storeAs('public/user_icon', $userRecord->id.'.'.$request->extention);
        }elseif($request->extention){ //ファイルありかつ既にアイコン設定済みなら上書き
            //サイズを縮小したい
            Image::make($request->file('files')[0])->resize(300, 300, function ($constraint) {
                $constraint->aspectRatio();
            })->save(storage_path(('app/public/user_icon/'. $userRecord->id.'.jpg'), 100));
            $userRecord->update(['icon' => $userRecord->id.'.jpg',]);
            $userRecord->touch();//updated_atを更新
        }

        $result = $userRecord->fill($request->only([
            'name', 'email'
        ]))->update();

        $afterUpdatedAt = $userRecord->updated_at; //更新後のupdated_at

        if($beforeUpdatedAt == $afterUpdatedAt ) {//updated_atに差分があるかをリターン
            return 'nothing';
        }else{
            return 'updated';
        }
        
    } 
    public function checkOldPassword(User $user,Request $request){
        $userRecord =  User::where('id', $request->userId)->first();
        $currentPassword =  $userRecord->password;
        
        if (Hash::check($request->currentPassword, $currentPassword)) {
            $userRecord->update(['password' =>  Hash::make($request->password)]);
            return 'success';
        } else {
            return 'oldPasswordError';
        }
    }   
}