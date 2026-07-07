<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jou_access_logs', function (Blueprint $table) {
            $table->string('release_token', 20)->nullable()->after('crawler')->index();
        });

        Schema::table('jou_analytics_events', function (Blueprint $table) {
            $table->string('release_token', 20)->nullable()->after('client_ts')->index();
        });
    }

    public function down(): void
    {
        Schema::table('jou_access_logs', function (Blueprint $table) {
            $table->dropColumn('release_token');
        });

        Schema::table('jou_analytics_events', function (Blueprint $table) {
            $table->dropColumn('release_token');
        });
    }
};
