<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Sesi presensi per pertemuan
        Schema::create('presensi_sesi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pertemuan_id')->unique()->constrained('pertemuan')->cascadeOnDelete();
            $table->string('kode', 8)->unique();   // kode check-in mahasiswa
            $table->boolean('is_aktif')->default(true);
            $table->timestamp('dibuka_at')->nullable();
            $table->timestamp('ditutup_at')->nullable();
            $table->timestamps();
        });

        // Kehadiran per mahasiswa per sesi
        Schema::create('presensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('presensi_sesi_id')->constrained('presensi_sesi')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['hadir', 'izin', 'alpha'])->default('alpha');
            $table->timestamp('waktu_masuk')->nullable();
            $table->timestamps();

            $table->unique(['presensi_sesi_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presensi');
        Schema::dropIfExists('presensi_sesi');
    }
};
