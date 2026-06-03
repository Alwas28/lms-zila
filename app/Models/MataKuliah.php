<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MataKuliah extends Model
{
    protected $table = 'mata_kuliah';

    protected $fillable = ['kode', 'nama', 'sks', 'deskripsi', 'is_arsip'];

    protected $casts = ['is_arsip' => 'boolean'];

    public function kelas(): HasMany
    {
        return $this->hasMany(Kelas::class);
    }

    public function arsipkan(): void
    {
        $this->update(['is_arsip' => true]);
    }

    public function aktifkan(): void
    {
        $this->update(['is_arsip' => false]);
    }

    public function scopeAktif($query)
    {
        return $query->where('is_arsip', false);
    }

    public function scopeArsip($query)
    {
        return $query->where('is_arsip', true);
    }
}
