<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $primaryKey = 'no'; // ← PK가 id가 아님을 Laravel에게 알려줌

    protected $fillable = ['title', 'sender_id', 'receiver_id', 'div', 'content', 'is_read', 'post_no', 'save_status', 'type'];

    public function sender() 
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver() 
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}