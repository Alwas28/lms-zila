<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Konfigurasi bobot nilai per kelas
        Schema::create('penilaian_config', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->unique()->constrained('kelas')->cascadeOnDelete();
            $table->unsignedTinyInteger('bobot_kehadiran')->default(10);
            $table->unsignedTinyInteger('bobot_tugas')->default(20);
            $table->unsignedTinyInteger('bobot_quiz')->default(20);
            $table->unsignedTinyInteger('bobot_uts')->default(25);
            $table->unsignedTinyInteger('bobot_uas')->default(25);
            $table->timestamps();
        });

        // Nilai per mahasiswa per kelas
        Schema::create('penilaian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('nilai_kehadiran', 5, 2)->nullable();
            $table->decimal('nilai_tugas', 5, 2)->nullable();
            $table->decimal('nilai_quiz', 5, 2)->nullable();
            $table->decimal('nilai_uts', 5, 2)->nullable();
            $table->decimal('nilai_uas', 5, 2)->nullable();
            $table->decimal('nilai_akhir', 5, 2)->nullable();
            $table->string('grade', 3)->nullable();   // A, A-, B+, B, C, D, E
            $table->timestamps();

            $table->unique(['kelas_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian');
        Schema::dropIfExists('penilaian_config');
    }
};
