<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class PresensiSesi extends Model
{
    protected $table = 'presensi_sesi';

    protected $fillable = [
        'pertemuan_id', 'kode', 'is_aktif', 'dibuka_at', 'ditutup_at',
    ];

    protected $casts = [
        'is_aktif'   => 'boolean',
        'dibuka_at'  => 'datetime',
        'ditutup_at' => 'datetime',
    ];

    // ── Relations ─────────────────────────────────────────────

    public function pertemuan(): BelongsTo
    {
        return $this->belongsTo(Pertemuan::class);
    }

    public function presensi(): HasMany
    {
        return $this->hasMany(Presensi::class);
    }

    // ── Static Methods ────────────────────────────────────────

    public static function buat(int $pertemuan_id): self
    {
        return static::create([
            'pertemuan_id' => $pertemuan_id,
            'kode'         => strtoupper(Str::random(6)),
            'is_aktif'     => true,
            'dibuka_at'    => now(),
        ]);
    }
}
