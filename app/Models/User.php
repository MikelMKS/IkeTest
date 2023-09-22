<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'user',
        'name',
        'phone',
        'password',
        'id_consent2',
        'id_consent3'
    ];

    protected $hidden = [
        // 'password',
        'remember_token',
    ];

    // protected $casts = [
    //     'password' => 'hashed',
    // ];

    protected $dates = [
        'deleted_at'
    ];

    public static function getUserConsents($idUsuario)
    {
        // Get actual consent2 and consent3 status
        return self::where('id', $idUsuario)
            ->select('id_consent2', 'id_consent3')
            ->first();
    }
}
