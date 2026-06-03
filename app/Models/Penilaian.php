<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penilaian extends Model
{
    protected $table = 'penilaian';

    protected $fillable = [
        'kelas_id', 'user_id',
        'nilai_kehadiran', 'nilai_tugas', 'nilai_quiz', 'nilai_uts', 'nilai_uas',
        'nilai_akhir', 'grade',
    ];

    protected $casts = [
        'nilai_kehadiran' => 'decimal:2',
        'nilai_tugas'     => 'decimal:2',
        'nilai_quiz'      => 'decimal:2',
        'nilai_uts'       => 'decimal:2',
        'nilai_uas'       => 'decimal:2',
        'nilai_akhir'     => 'decimal:2',
    ];

    // ── Relations ─────────────────────────────────────────────

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ── Methods ───────────────────────────────────────────────

    public function hitungNilaiAkhir(PenilaianConfig $config): float
    {
        $total = $config->bobot_kehadiran + $config->bobot_tugas + $config->bobot_quiz
               + $config->bobot_uts + $config->bobot_uas;

        if ($total <= 0) {
            return 0;
        }

        $nilai = (($this->nilai_kehadiran ?? 0) * $config->bobot_kehadiran
                + ($this->nilai_tugas     ?? 0) * $config->bobot_tugas
                + ($this->nilai_quiz      ?? 0) * $config->bobot_quiz
                + ($this->nilai_uts       ?? 0) * $config->bobot_uts
                + ($this->nilai_uas       ?? 0) * $config->bobot_uas)
               / $total;

        return round($nilai, 2);
    }

    public static function hitungGrade(float $nilai): string
    {
        return match (true) {
            $nilai >= 85 => 'A',
            $nilai >= 80 => 'A-',
            $nilai >= 75 => 'B+',
            $nilai >= 70 => 'B',
            $nilai >= 65 => 'B-',
            $nilai >= 60 => 'C+',
            $nilai >= 55 => 'C',
            $nilai >= 40 => 'D',
            default      => 'E',
        };
    }
}
