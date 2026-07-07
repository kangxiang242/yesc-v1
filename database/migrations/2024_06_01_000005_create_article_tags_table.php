<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('article_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('标签名称');
            $table->string('slug')->comment('标签别名');
            $table->string('color', 7)->default('#1E88E5')->comment('标签颜色');
            $table->text('description')->nullable();
            $table->longText('cat_ids')->nullable()->comment('关联的文章分类ID');
            $table->integer('sort')->default(0)->comment('排序');
            $table->tinyInteger('status')->default(1)->comment('状态：0=禁用，1=启用');
            $table->timestamps();
            $table->unique('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_tags');
    }
};
