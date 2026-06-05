<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumTopik extends Model
{
    protected $table = 'forum_topik';

    protected $fillable = ['kelas_id', 'user_id', 'judul', 'isi', 'is_pinned', 'is_locked'];

    protected $casts = ['kelas_id' => 'integer', 'is_pinned' => 'boolean', 'is_locked' => 'boolean'];

    public function kelas()     { return $this->belongsTo(Kelas::class); }
    public function penulis()   { return $this->belongsTo(User::class, 'user_id'); }
    public function komentar()  { return $this->hasMany(ForumKomentar::class); }
}
