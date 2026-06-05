<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ujian extends Model
{
    protected $table = 'ujian';

    protected $fillable = [
        'kelas_id', 'judul', 'tipe', 'deskripsi',
        'durasi', 'mulai_at', 'selesai_at', 'jumlah_soal',
        'is_acak_soal', 'is_acak_jawaban', 'batas_percobaan', 'status',
    ];

    protected $casts = [
        'kelas_id'        => 'integer',
        'mulai_at'        => 'datetime',
        'selesai_at'      => 'datetime',
        'is_acak_soal'    => 'boolean',
        'is_acak_jawaban' => 'boolean',
    ];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function ujianSoal(): HasMany
    {
        return $this->hasMany(UjianSoal::class)->orderBy('nomor');
    }

    public function sesi(): HasMany
    {
        return $this->hasMany(UjianSesi::class);
    }

    public function soal(): BelongsToMany
    {
        return $this->belongsToMany(BankSoal::class, 'ujian_soal')
                    ->withPivot('id', 'nomor')
                    ->withTimestamps()
                    ->orderByPivot('nomor');
    }

    // ── Scopes ────────────────────────────────────
    public function scopeQuiz($q)   { return $q->where('tipe', 'quiz'); }
    public function scopeUts($q)    { return $q->where('tipe', 'uts'); }
    public function scopeUas($q)    { return $q->where('tipe', 'uas'); }
    public function scopeAktif($q)  { return $q->where('status', 'aktif'); }
    public function scopeDraft($q)  { return $q->where('status', 'draft'); }

    // ── Accessors ─────────────────────────────────
    public function getTipeLabelAttribute(): string
    {
        return match($this->tipe) {
            'quiz' => 'Quiz',
            'uts'  => 'UTS',
            'uas'  => 'UAS',
            default => strtoupper($this->tipe),
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft'   => 'Draft',
            'aktif'   => 'Aktif',
            'selesai' => 'Selesai',
            default   => $this->status,
        };
    }

    public function getIsSedangBerlangsungAttribute(): bool
    {
        if ($this->status !== 'aktif') return false;
        $now = now();
        if ($this->mulai_at && $now->lt($this->mulai_at)) return false;
        if ($this->selesai_at && $now->gt($this->selesai_at)) return false;
        return true;
    }

    // ── Actions ───────────────────────────────────
    public function aktifkan(): void { $this->update(['status' => 'aktif']); }
    public function tutup(): void    { $this->update(['status' => 'selesai']); }
    public function draftkan(): void { $this->update(['status' => 'draft']); }

    /** Tambah soal dari bank, urutkan ulang nomor */
    public function tambahSoal(int $bankSoalId): void
    {
        $nomor = $this->ujianSoal()->max('nomor') + 1;
        UjianSoal::firstOrCreate(
            ['ujian_id' => $this->id, 'bank_soal_id' => $bankSoalId],
            ['nomor'    => $nomor]
        );
    }
}
