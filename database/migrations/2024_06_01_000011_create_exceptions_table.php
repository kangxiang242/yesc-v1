<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exceptions', function (Blueprint $table) {
            $table->id();
            $table->string('ip', 45)->nullable()->comment('訪問IP');
            $table->string('ip_country')->nullable()->comment('访问地區');
            $table->integer('status_code')->default(400)->comment('状态码');
            $table->text('message')->nullable()->comment('异常信息');
            $table->string('uri')->nullable()->comment('路径');
            $table->string('method')->nullable()->comment('请求方法');
            $table->string('referer')->nullable()->comment('引用页');
            $table->text('user_agent')->nullable()->comment('载具信息');
            $table->longText('parameters')->comment('请求参数');
            $table->longText('headers')->comment('请求头');
            $table->longText('trace');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exceptions');
    }
};
