<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('article_cates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->comment('文章分類');
            $table->string('sub_name')->nullable();
            $table->integer('sort')->default(1)->comment('排序，从小到大');
            $table->tinyInteger('status')->default(1)->comment('1正常 0关闭');
            $table->string('uri')->nullable()->comment('路径');
            $table->longText('desc')->nullable()->comment('描述');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_cates');
    }
};
