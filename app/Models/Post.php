<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $primaryKey = 'no'; // ← PK가 id가 아님을 Laravel에게 알려줌

    protected $fillable = [
        'title', 
        'content', 
        'user_id',
        'status',
        'save_status',
        'div'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
