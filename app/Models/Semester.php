<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Semester extends Model
{
    protected $fillable = ['tahun_akademik_id', 'tipe', 'tanggal_mulai', 'tanggal_selesai', 'is_aktif'];

    protected $casts = [
        'is_aktif'        => 'boolean',
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function tahunAkademik(): BelongsTo
    {
        return $this->belongsTo(TahunAkademik::class);
    }

    /** Ambil semester yang sedang aktif */
    public static function aktif(): ?self
    {
        return static::with('tahunAkademik')->where('is_aktif', true)->first();
    }

    /** Aktifkan semester ini, nonaktifkan yang lain */
    public function aktifkan(): void
    {
        static::query()->update(['is_aktif' => false]);
        $this->update(['is_aktif' => true]);

        // Pastikan tahun akademik induknya juga aktif
        $this->tahunAkademik->aktifkan();
    }

    public function getLabelAttribute(): string
    {
        $tipe = ucfirst($this->tipe);
        return "Semester {$tipe} — {$this->tahunAkademik->nama}";
    }

    public function getTipeHumanAttribute(): string
    {
        return ucfirst($this->tipe);
    }
}
