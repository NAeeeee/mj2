<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostFilesTable extends Migration
{
    public function up()
    {
        Schema::create('post_files', function (Blueprint $table) {
            $table->bigIncrements('no'); // 기본키, big integer unsigned, auto-increment
            $table->unsignedBigInteger('post_id'); // posts.no 참조 FK
            $table->string('filename');
            $table->string('filepath');
            $table->integer('filesize')->nullable();
            $table->string('filetype')->nullable();
            $table->timestamps();

            // 외래키 설정 (posts 테이블 no 컬럼 참조)
            $table->foreign('post_id')->references('no')->on('posts')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('post_files');
    }
}
