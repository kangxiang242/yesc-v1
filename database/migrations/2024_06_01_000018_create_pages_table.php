<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->integer('id')->unsigned()->autoIncrement()->primary();
            $table->string('uri')->nullable()->comment('uri路徑');
            $table->string('title')->nullable()->comment('標題');
            $table->text('desc')->nullable()->comment('描述');
            $table->tinyInteger('mode')->default(0)->comment('内容模式');
            $table->longText('content')->nullable()->comment('内容');
            $table->string('html_file')->nullable()->comment('自定义布局html');
            $table->tinyInteger('status')->default(1)->comment('狀態');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
