<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;  // 이메일 인증
use Illuminate\Support\Facades\Notification;
use App\Notifications\CustomVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\CustomResetPassword;
use App\Models\PostFile;

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
        'is_admin',
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
        Notification::route('mail', 'nazz0525z@gmail.com') // 테스트용 이메일
        ->notify(new CustomVerifyEmail($this));
    }

    // 비밀번호 재설정 커스텀
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\CustomResetPassword($token));
    }

    public function getEmailForVerification()
    {
        return 'nazz0525z@gmail.com';  // 테스트용 이메일로 고정
    }

    // nav
    public function profile_img()
    {
        return $this->hasOne(\App\Models\PostFile::class, 'target_no')
                    ->where('target_type', 'I')
                    ->where('save_status', 'Y')
                    ->latest('no');
    }
}
