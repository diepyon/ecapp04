<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Illuminate\Support\Facades\Storage;//画像削除のために必要

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;//追記
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'icon',
        // 'created_at',
        // 'updated_at',
        // 'role',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];   

    public function deleteOldIcon($oldIcon)
    {
        if($oldIcon){
            //古い画像を削除
            $file = 'public/user_icon/'.$oldIcon;
            Storage::delete($file);
        }
    }    
}