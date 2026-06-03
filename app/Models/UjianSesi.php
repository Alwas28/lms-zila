<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UjianSesi extends Model
{
    protected $table = 'ujian_sesi';

    protected $fillable = ['ujian_id', 'user_id', 'percobaan_ke', 'mulai_at', 'selesai_at', 'nilai', 'is_selesai'];

    protected $casts = [
        'mulai_at'   => 'datetime',
        'selesai_at' => 'datetime',
        'is_selesai' => 'boolean',
    ];

    public function ujian()    { return $this->belongsTo(Ujian::class); }
    public function mahasiswa(){ return $this->belongsTo(User::class, 'user_id'); }
    public function jawaban()  { return $this->hasMany(UjianJawaban::class); }

    public function getSisaWaktuDetikAttribute(): int
    {
        if ($this->is_selesai || !$this->ujian) return 0;
        $durasi = $this->ujian->durasi * 60; // menit → detik
        $elapsed = now()->diffInSeconds($this->mulai_at);
        return max(0, $durasi - $elapsed);
    }
}
