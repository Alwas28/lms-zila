<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UjianJawaban extends Model
{
    protected $table = 'ujian_jawaban';

    protected $fillable = ['ujian_sesi_id', 'bank_soal_id', 'pilihan_soal_id', 'jawaban_essay', 'is_benar', 'poin'];

    protected $casts = ['is_benar' => 'boolean'];

    public function sesi()       { return $this->belongsTo(UjianSesi::class, 'ujian_sesi_id'); }
    public function soal()       { return $this->belongsTo(BankSoal::class, 'bank_soal_id'); }
    public function pilihan()    { return $this->belongsTo(PilihanSoal::class, 'pilihan_soal_id'); }
}
