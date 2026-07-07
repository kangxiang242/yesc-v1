<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('faqs', function (Blueprint $table) {
            $table->unsignedBigInteger('article_cate_id')->nullable(true)->after('sort')->comment('關聯文章分類ID（可選）');
            $table->foreign('article_cate_id')->references('id')->on('article_cates')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('article_id')->nullable(true)->after('article_cate_id')->comment('關聯單篇文章ID（可選）');
            $table->foreign('article_id')->references('id')->on('articles')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('faqs', function (Blueprint $table) {
            $table->dropForeign(['article_cate_id']);
            $table->dropForeign(['article_id']);
            $table->dropColumn(['article_cate_id', 'article_id']);
        });
    }
};
