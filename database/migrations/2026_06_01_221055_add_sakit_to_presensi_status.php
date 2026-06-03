<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE presensi MODIFY COLUMN status ENUM('hadir', 'izin', 'sakit', 'alpha') NOT NULL DEFAULT 'alpha'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE presensi MODIFY COLUMN status ENUM('hadir', 'izin', 'alpha') NOT NULL DEFAULT 'alpha'");
    }
};
