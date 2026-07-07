<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jou_analytics_events', function (Blueprint $table) {
            $table->id();
            $table->string('platform', 10)->index();
            $table->string('event_name', 50)->index();
            $table->string('visitor_id', 64)->index();
            $table->string('session_id', 64)->index();
            $table->string('page_view_id', 64)->nullable();
            $table->string('page_path', 500)->nullable();
            $table->string('page_type', 50)->nullable()->index();
            $table->string('element_id', 100)->nullable();
            $table->json('props')->nullable();
            $table->string('ip', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('host', 255)->nullable();
            $table->bigInteger('client_ts')->nullable();
            $table->timestamp('created_at')->nullable()->index();

            $table->index(['platform', 'created_at'], 'idx_platform_created');
            $table->index(['platform', 'page_type', 'event_name'], 'idx_platform_page_event');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jou_analytics_events');
    }
};
