<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    protected $fillable = [
        'mata_kuliah_id', 'semester_id', 'nama_kelas', 'kapasitas', 'is_aktif',
    ];

    protected $casts = ['is_aktif' => 'boolean'];

    public function mataKuliah(): BelongsTo
    {
        return $this->belongsTo(MataKuliah::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    // Dosen pengampu via pivot
    public function dosen(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'pengampu')
                    ->withPivot('id', 'is_koordinator')
                    ->withTimestamps();
    }

    public function koordinator()
    {
        return $this->dosen()->wherePivot('is_koordinator', true)->first();
    }

    // Mahasiswa terdaftar via pivot
    public function mahasiswa(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'enrollments')->withTimestamps();
    }

    public function pengampu(): HasMany
    {
        return $this->hasMany(Pengampu::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function pertemuan(): HasMany
    {
        return $this->hasMany(Pertemuan::class)->orderBy('nomor');
    }

    public function tugas(): HasMany
    {
        return $this->hasMany(Tugas::class);
    }

    public function rps(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Rps::class);
    }

    public function penilaianConfig(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(PenilaianConfig::class);
    }

    public function penilaian(): HasMany
    {
        return $this->hasMany(Penilaian::class);
    }

    public function ujian(): HasMany
    {
        return $this->hasMany(Ujian::class);
    }

    public function forumTopik(): HasMany
    {
        return $this->hasMany(ForumTopik::class);
    }

    public function pengumuman(): HasMany
    {
        return $this->hasMany(Pengumuman::class);
    }

    public function getNamaLengkapAttribute(): string
    {
        return "{$this->mataKuliah->nama} - {$this->nama_kelas}";
    }

    public function getIsPenuhAttribute(): bool
    {
        return $this->mahasiswa()->count() >= $this->kapasitas;
    }

    public function tutup(): void
    {
        $this->update(['is_aktif' => false]);
    }

    public function buka(): void
    {
        $this->update(['is_aktif' => true]);
    }
}
