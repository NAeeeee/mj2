<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostFile extends Model
{
    use HasFactory;

    protected $table = 'post_file'; // 테이블명 명시

    protected $primaryKey = 'no'; // ← PK가 id가 아님을 Laravel에게 알려줌

    protected $fillable = [
        'target_no',      // 게시글 ID (또는 no 사용 중이면 no)
        'filename',     // 원본 파일명
        'filepath',     // 저장 경로 (예: img/20250604/filename.jpg)
        'filetype',     // 확장자
    ];

    // 게시글과의 관계 (Post 모델과 연결)
    public function post()
    {
        return $this->belongsTo(Post::class, 'target_no', 'no');  // 외래키, 게시글 기본키
    }
}
