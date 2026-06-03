<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->unique()->constrained('kelas')->cascadeOnDelete();
            $table->text('deskripsi_mk')->nullable();
            $table->text('cpl')->nullable();          // Capaian Pembelajaran Lulusan
            $table->text('cpmk')->nullable();         // Capaian Pembelajaran Mata Kuliah
            $table->text('sub_cpmk')->nullable();
            $table->text('metode_pembelajaran')->nullable();
            $table->text('referensi')->nullable();
            $table->string('pdf_path')->nullable();   // upload PDF RPS
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rps');
    }
};
