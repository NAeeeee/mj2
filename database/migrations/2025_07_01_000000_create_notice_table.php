<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNoticeTable extends Migration
{
    public function up()
    {
        Schema::create('notice', function (Blueprint $table) {
            $table->bigIncrements('no'); // 기본키
            $table->string('div',50);
            $table->string('title');
            $table->text('content');
            $table->string('save_id',50);
            $table->string('save_status', 2)->default('Y'); // 저장 여부
            $table->timestamps();

        });
    }

    public function writer()
    {
        return $this->belongsTo(User::class, 'save_id');
    }
}
