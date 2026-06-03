<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pertemuan extends Model
{
    protected $table = 'pertemuan';

    protected $fillable = [
        'kelas_id', 'nomor', 'topik', 'deskripsi', 'metode', 'tanggal', 'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'status'  => 'string',
    ];

    // ── Relations ─────────────────────────────────────────────

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function presensiSesi(): HasOne
    {
        return $this->hasOne(PresensiSesi::class);
    }

    public function materi(): HasMany
    {
        return $this->hasMany(Materi::class)->orderBy('urutan');
    }

    public function tugas(): HasMany
    {
        return $this->hasMany(Tugas::class);
    }

    // ── Scopes ────────────────────────────────────────────────

    public function scopeSelesai($query)
    {
        return $query->where('status', 'selesai');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }
}
