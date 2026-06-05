<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tugas extends Model
{
    protected $table = 'tugas';

    protected $fillable = [
        'kelas_id', 'pertemuan_id', 'judul', 'deskripsi', 'tipe', 'deadline', 'nilai_maks',
    ];

    protected $casts = [
        'kelas_id'     => 'integer',
        'pertemuan_id' => 'integer',
        'deadline'     => 'datetime',
    ];

    // ── Relations ─────────────────────────────────────────────

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function pertemuan(): BelongsTo
    {
        return $this->belongsTo(Pertemuan::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(TugasSubmission::class);
    }

    // ── Accessors ─────────────────────────────────────────────

    public function getIsExpiredAttribute(): bool
    {
        return $this->deadline !== null && $this->deadline->isPast();
    }
}
