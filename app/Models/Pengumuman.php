<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    protected $table = 'pengumuman';

    protected $fillable = ['kelas_id', 'user_id', 'judul', 'isi', 'is_pinned'];

    protected $casts = ['is_pinned' => 'boolean'];

    public function kelas()   { return $this->belongsTo(Kelas::class); }
    public function penulis() { return $this->belongsTo(User::class, 'user_id'); }
}
