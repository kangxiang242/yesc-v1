<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('parent_id')->nullable()->default(0);
            $table->string('name')->nullable();
            $table->integer('level')->nullable()->default(1);
            $table->tinyInteger('is_grab')->nullable()->default(0);
            $table->boolean('is_special')->nullable()->default(0);
            $table->string('code', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('areas');
    }
};
