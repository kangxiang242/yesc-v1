<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('no');
            $table->string('inside_no')->comment('内部單號');
            $table->decimal('total_price', 8, 2)->comment('总价');
            $table->decimal('product_price', 8, 2)->default(0.00)->comment('商品總價');
            $table->decimal('freight', 8, 2)->default(0.00)->comment('运费');
            $table->tinyInteger('delivery_type')->default(0)->comment('配送方式 0宅配到府');
            $table->tinyInteger('payment_type')->default(0)->comment('付款方式， 0货到付款');
            $table->string('name')->comment('收货人名称');
            $table->string('phone')->comment('收货人电话');
            $table->string('email')->comment('收货人邮件');
            $table->string('country')->default('中国')->comment('国家');
            $table->string('province')->default('台灣')->comment('省份');
            $table->string('city')->comment('市');
            $table->string('county')->comment('区');
            $table->string('street')->nullable()->comment('街道');
            $table->string('address')->comment('詳細地址');
            $table->tinyInteger('delivery_time')->nullable()->comment('送达时间');
            $table->tinyInteger('status')->default(0)->comment('訂單狀態 -1订单取消 0待处理 1发货中 2已发货 3运输中 5已付款 6拒绝付款 10订单完成');
            $table->text('remarks')->nullable()->comment('訂單備注');
            $table->text('admin_remarks')->nullable()->comment('管理員備注');
            $table->string('ip', 45)->comment('下单ip');
            $table->string('ipcountry')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('shop_no', 100)->nullable();
            $table->string('shop_name', 100)->nullable();
            $table->tinyInteger('shop_type')->default(0)->comment('0非便利店 1=711 2=全家 3=OK 4=萊爾富');
            $table->longText('shop_data')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
