<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    use HasFactory;

    protected $primaryKey = 'no'; // ← PK가 id가 아님을 Laravel에게 알려줌

    protected $table = 'post_replies';

    protected $fillable = [
        'post_no', 
        'content', 
        'admin_id',
        'status',
        'save_status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
