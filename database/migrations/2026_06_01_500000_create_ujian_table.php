<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ujian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->string('judul', 150);
            $table->enum('tipe', ['quiz', 'uts', 'uas']);
            $table->text('deskripsi')->nullable();
            $table->unsignedSmallInteger('durasi')->nullable();          // menit
            $table->dateTime('mulai_at')->nullable();
            $table->dateTime('selesai_at')->nullable();
            $table->unsignedSmallInteger('jumlah_soal')->default(10);   // soal yg ditampilkan
            $table->boolean('is_acak_soal')->default(false);
            $table->boolean('is_acak_jawaban')->default(false);
            $table->unsignedTinyInteger('batas_percobaan')->default(1);
            $table->enum('status', ['draft', 'aktif', 'selesai'])->default('draft');
            $table->timestamps();
        });

        Schema::create('ujian_soal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ujian_id')->constrained('ujian')->cascadeOnDelete();
            $table->foreignId('bank_soal_id')->constrained('bank_soal')->cascadeOnDelete();
            $table->unsignedSmallInteger('nomor')->default(0);
            $table->timestamps();

            $table->unique(['ujian_id', 'bank_soal_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ujian_soal');
        Schema::dropIfExists('ujian');
    }
};
