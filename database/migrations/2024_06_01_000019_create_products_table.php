<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('img')->nullable()->comment('主圖');
            $table->string('m_img')->nullable()->comment('手機版主圖');
            $table->string('market_img')->nullable()->comment('营销图');
            $table->string('market_m_img')->nullable()->comment('营销图');
            $table->text('subtitle')->nullable()->comment('副标题');
            $table->decimal('price', 8, 2)->default(0.00)->comment('价格');
            $table->decimal('market_price', 8, 2)->default(0.00)->comment('市场价格');
            $table->tinyInteger('status')->default(1)->comment('状态');
            $table->tinyInteger('is_stock')->default(1)->comment('是否有货');
            $table->integer('sort')->default(1);
            $table->integer('quantity')->default(1)->comment('数量');
            $table->string('label')->nullable()->comment('标签');
            $table->longText('describe')->nullable()->comment('商品描述');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
