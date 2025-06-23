<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;  // 이메일 인증
use Illuminate\Support\Facades\Notification;
use App\Notifications\CustomVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'ph',
        'password',
        'manager_yn',
        'status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

     /**
     * 이메일 인증 알림 커스텀 (테스트용)
     */
    public function sendEmailVerificationNotification()
    {
        Notification::route('mail', 'nazz0525z@gmail.com') // 네 테스트용 이메일
            ->notify(new CustomVerifyEmail);
    }
}
