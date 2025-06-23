<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->bigIncrements('no');
            $table->unsignedBigInteger('sender_id');      // 보낸 사람
            $table->unsignedBigInteger('receiver_id');    // 받는 사람
            $table->string('div', 1)->default('B');        // A: 알림, B: 쪽지
            $table->text('content');                       // 메시지 내용
            $table->boolean('is_read')->default(false);   // 읽음 여부
            $table->timestamps();

            // 외래 키 설정
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('messages');
    }
};
