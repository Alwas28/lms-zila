<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Presensi extends Model
{
    protected $table = 'presensi';

    protected $fillable = [
        'presensi_sesi_id', 'user_id', 'status', 'waktu_masuk',
    ];

    protected $casts = [
        'waktu_masuk' => 'datetime',
    ];

    // ── Relations ─────────────────────────────────────────────

    public function presensiSesi(): BelongsTo
    {
        return $this->belongsTo(PresensiSesi::class);
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
