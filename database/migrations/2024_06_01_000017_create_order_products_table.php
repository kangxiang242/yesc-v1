<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_products', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_id')->unsigned();
            $table->bigInteger('product_id')->unsigned();
            $table->string('product_name');
            $table->string('product_img')->nullable();
            $table->integer('number')->comment('数量');
            $table->decimal('unit_price', 8, 2)->comment('单价');
            $table->decimal('total_price', 8, 2)->comment('总价');
            $table->longText('product');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_products');
    }
};
