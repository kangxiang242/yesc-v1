<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Release 映射派生字段（doc/RELEASE_TOKEN.md #1）
 *
 * 收到 page_view 后，根据 html_token 派生：
 * - release_version：releases.token = html_token → version
 * - release_deployed_at：同上 → deployed_at
 * - release_status：known / unknown / missing / invalid
 * - asset_token_status：ok / missing / mismatch / not_reported
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jou_analytics_events', function (Blueprint $table) {
            $table->string('release_version', 20)->nullable()->after('release_token')->index();
            $table->timestamp('release_deployed_at')->nullable()->after('release_version');
            $table->string('release_status', 12)->nullable()->after('release_deployed_at')->index();
            $table->string('asset_token_status', 16)->nullable()->after('release_status')->index();
        });
    }

    public function down(): void
    {
        Schema::table('jou_analytics_events', function (Blueprint $table) {
            $table->dropColumn([
                'release_version',
                'release_deployed_at',
                'release_status',
                'asset_token_status',
            ]);
        });
    }
};
