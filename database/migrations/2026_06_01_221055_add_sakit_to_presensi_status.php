<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Skip jika tabel belum ada (fresh install sudah punya 'sakit' dari create_presensi_table)
        if (!Schema::hasTable('presensi')) {
            return;
        }
        DB::statement("ALTER TABLE presensi MODIFY COLUMN status ENUM('hadir', 'izin', 'sakit', 'alpha') NOT NULL DEFAULT 'alpha'");
    }

    public function down(): void
    {
        if (!Schema::hasTable('presensi')) {
            return;
        }
        DB::statement("ALTER TABLE presensi MODIFY COLUMN status ENUM('hadir', 'izin', 'alpha') NOT NULL DEFAULT 'alpha'");
    }
};
