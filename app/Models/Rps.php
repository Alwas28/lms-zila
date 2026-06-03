<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rps extends Model
{
    protected $table = 'rps';

    protected $fillable = [
        'kelas_id', 'deskripsi_mk', 'cpl', 'cpmk', 'sub_cpmk',
        'metode_pembelajaran', 'referensi', 'pdf_path',
    ];

    // ── Relations ─────────────────────────────────────────────

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }
}
