<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PilihanSoal extends Model
{
    protected $table = 'pilihan_soal';

    protected $fillable = ['bank_soal_id', 'teks', 'is_benar', 'urutan'];

    protected $casts = ['is_benar' => 'boolean'];

    public function soal(): BelongsTo
    {
        return $this->belongsTo(BankSoal::class, 'bank_soal_id');
    }
}
