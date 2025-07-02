<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    use HasFactory;

    protected $table = 'notice';
    
    protected $primaryKey = 'no';

    protected $fillable = ['div', 'title', 'content', 'save_id', 'save_status'];
}
