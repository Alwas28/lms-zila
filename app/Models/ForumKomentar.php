<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForumKomentar extends Model
{
    protected $table = 'forum_komentar';

    protected $fillable = ['forum_topik_id', 'user_id', 'isi'];

    public function topik()   { return $this->belongsTo(ForumTopik::class, 'forum_topik_id'); }
    public function penulis() { return $this->belongsTo(User::class, 'user_id'); }
}
