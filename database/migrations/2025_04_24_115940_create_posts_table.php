<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();  // 기본 키(id) 자동 생성
            $table->string('title');  // 게시글 제목
            $table->text('content');  // 게시글 내용
            $table->unsignedBigInteger('user_id');  // 게시글 작성자 (사용자 ID)
            $table->timestamps();  // 생성 시간, 수정 시간 자동으로 추가
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); // users 테이블과의 외래 키 관계
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
