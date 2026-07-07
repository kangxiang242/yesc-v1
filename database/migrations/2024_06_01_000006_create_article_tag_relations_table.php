<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('article_tag_relations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('article_id')->unsigned()->comment('文章ID');
            $table->bigInteger('tag_id')->unsigned()->comment('标签ID');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_tag_relations');
    }
};
