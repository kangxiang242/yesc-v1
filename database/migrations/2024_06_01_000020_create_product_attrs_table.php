<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_attrs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_id')->unsigned();
            $table->string('name')->comment('属性名称');
            $table->text('value')->comment('属性值');
            $table->tinyInteger('status')->default(1)->comment('0无效 1有效');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_attrs');
    }
};
