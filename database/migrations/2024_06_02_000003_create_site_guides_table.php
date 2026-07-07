<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_guides', function (Blueprint $table) {
            $table->id();
            $table->string('page_type')->default('home')->comment('頁面類型：home=首頁, product=產品頁');
            $table->string('title')->nullable()->comment('標題');
            $table->text('description')->nullable()->comment('介紹描述');
            $table->string('item_title')->nullable()->comment('選項標題');
            $table->text('item_description')->nullable()->comment('選項描述');
            $table->string('item_image')->nullable()->comment('選項圖片');
            $table->integer('sort')->default(0)->comment('排序');
            $table->tinyInteger('status')->default(1)->comment('狀態：1=啟用, 0=禁用');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_guides');
    }
};
