<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ujian_jawaban', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ujian_sesi_id')->constrained('ujian_sesi')->cascadeOnDelete();
            $table->foreignId('bank_soal_id')->constrained('bank_soal')->cascadeOnDelete();
            $table->foreignId('pilihan_soal_id')->nullable()->constrained('pilihan_soal')->nullOnDelete();
            $table->text('jawaban_essay')->nullable();
            $table->boolean('is_benar')->nullable();
            $table->decimal('poin', 5, 2)->default(0);
            $table->timestamps();

            $table->unique(['ujian_sesi_id', 'bank_soal_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ujian_jawaban');
    }
};
