<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jou_access_logs', function (Blueprint $table) {
            $table->id();
            $table->string('url')->comment('访问的url');
            $table->string('method', 20)->comment('请求方式');
            $table->string('host')->comment('域名');
            $table->string('referer')->nullable()->comment('访问来源');
            $table->string('ip', 45);
            $table->string('ipcountry')->nullable();
            $table->text('user_agent')->comment('浏览器信息');
            $table->string('device', 100)->comment('访问设备');
            $table->string('crawler', 100)->nullable()->comment('搜索引擎类型');
            $table->longText('parameter')->nullable()->comment('参数');
            $table->longText('headers')->nullable()->comment('请求头');
            $table->text('response')->nullable()->comment('返回的信息');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jou_access_logs');
    }
};
