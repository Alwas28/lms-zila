<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenilaianConfig extends Model
{
    protected $table = 'penilaian_config';

    protected $fillable = [
        'kelas_id', 'bobot_kehadiran', 'bobot_tugas', 'bobot_quiz', 'bobot_uts', 'bobot_uas',
    ];

    protected $casts = [
        'bobot_kehadiran' => 'integer',
        'bobot_tugas'     => 'integer',
        'bobot_quiz'      => 'integer',
        'bobot_uts'       => 'integer',
        'bobot_uas'       => 'integer',
    ];

    // ── Relations ─────────────────────────────────────────────

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }
}
