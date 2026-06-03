<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengampu extends Model
{
    protected $table = 'pengampu';

    protected $fillable = ['kelas_id', 'user_id', 'is_koordinator'];

    protected $casts = ['is_koordinator' => 'boolean'];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function dosen(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
