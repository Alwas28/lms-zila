<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BankSoal extends Model
{
    protected $table = 'bank_soal';

    protected $fillable = [
        'user_id', 'kelas_id', 'tipe', 'pertanyaan', 'rubrik', 'bobot', 'kategori',
    ];

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function pilihan(): HasMany
    {
        return $this->hasMany(PilihanSoal::class)->orderBy('urutan');
    }

    public function kunciJawaban(): HasMany
    {
        return $this->hasMany(PilihanSoal::class)->where('is_benar', true);
    }

    public function scopePilihanGanda($q) { return $q->where('tipe', 'pilihan_ganda'); }
    public function scopeEssay($q)        { return $q->where('tipe', 'essay'); }
    public function scopeMilikDosen($q, int $dosenId) { return $q->where('user_id', $dosenId); }

    public function getPertanyaanSingkatAttribute(): string
    {
        return \Str::limit(strip_tags($this->pertanyaan), 80);
    }

    public function getTipeLabelAttribute(): string
    {
        return $this->tipe === 'pilihan_ganda' ? 'Pilihan Ganda' : 'Essay';
    }
}
