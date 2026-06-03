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
        Schema::create('ujian_sesi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ujian_id')->constrained('ujian')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->tinyInteger('percobaan_ke')->default(1);
            $table->timestamp('mulai_at');
            $table->timestamp('selesai_at')->nullable();
            $table->decimal('nilai', 5, 2)->nullable();
            $table->boolean('is_selesai')->default(false);
            $table->timestamps();

            $table->unique(['ujian_id', 'user_id', 'percobaan_ke']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ujian_sesi');
    }
};
