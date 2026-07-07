<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE jou_access_logs MODIFY created_at TIMESTAMP(3) NULL');
            DB::statement('ALTER TABLE jou_access_logs MODIFY updated_at TIMESTAMP(3) NULL');
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE jou_access_logs MODIFY created_at TIMESTAMP NULL');
            DB::statement('ALTER TABLE jou_access_logs MODIFY updated_at TIMESTAMP NULL');
        }
    }
};
