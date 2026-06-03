<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TahunAkademik extends Model
{
    protected $table = 'tahun_akademik';

    protected $fillable = ['nama', 'is_aktif'];

    protected $casts = [
        'is_aktif' => 'boolean',
    ];

    public function semesters(): HasMany
    {
        return $this->hasMany(Semester::class);
    }

    public function semesterAktif(): HasMany
    {
        return $this->hasMany(Semester::class)->where('is_aktif', true);
    }

    /** Ambil tahun akademik yang sedang aktif */
    public static function aktif(): ?self
    {
        return static::where('is_aktif', true)->first();
    }

    /** Aktifkan tahun ini, nonaktifkan yang lain */
    public function aktifkan(): void
    {
        static::query()->update(['is_aktif' => false]);
        $this->update(['is_aktif' => true]);
    }

    public function getLabelAttribute(): string
    {
        return $this->nama;
    }
}
