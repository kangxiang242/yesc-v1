<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 订单关联 visitor_id（doc/TRACKING_API.md #5）
 *
 * Cookie `vid_web` / `vid_m` 携带 visitor_id，
 * 下单时写入 orders.visitor_id，用于报表关联行为分析。
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('visitor_id', 64)->nullable()->after('release_token')->index();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('visitor_id');
        });
    }
};
