<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->string('author_name')->nullable(true)->comment('作者姓名')->after('seo_description');
            $table->text('author_bio')->nullable(true)->comment('作者簡介/背景')->after('author_name');
            $table->string('reviewer_name')->nullable(true)->comment('醫學/藥學審核者姓名')->after('author_bio');
            $table->timestamp('reviewed_at')->nullable(true)->comment('審核時間')->after('reviewer_name');
            $table->text('sources')->nullable(true)->comment('參考來源(JSON 或換行分隔的連結+標題)')->after('reviewed_at');
            $table->timestamp('last_updated_at')->nullable(true)->comment('最後更新時間')->after('sources');
            $table->text('update_summary')->nullable(true)->comment('本次更新摘要')->after('last_updated_at');
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn([
                'author_name',
                'author_bio',
                'reviewer_name',
                'reviewed_at',
                'sources',
                'last_updated_at',
                'update_summary',
            ]);
        });
    }
};
