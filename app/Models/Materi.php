<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Materi extends Model
{
    protected $table = 'materi';

    protected $fillable = [
        'pertemuan_id', 'judul', 'tipe', 'file_path', 'url', 'is_wajib', 'urutan',
    ];

    protected $casts = [
        'is_wajib' => 'boolean',
    ];

    // ── Relations ─────────────────────────────────────────────

    public function pertemuan(): BelongsTo
    {
        return $this->belongsTo(Pertemuan::class);
    }

    // ── Accessors ─────────────────────────────────────────────

    public function getIconAttribute(): string
    {
        return match ($this->tipe) {
            'pdf'      => 'fa-file-pdf',
            'ppt'      => 'fa-file-powerpoint',
            'video'    => 'fa-file-video',
            'audio'    => 'fa-file-audio',
            'youtube'  => 'fa-brands fa-youtube',
            'website'  => 'fa-globe',
            'dokumen'  => 'fa-file-word',
            default    => 'fa-file',
        };
    }
}
