<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CpmkMapping extends Model
{
    protected $table = 'cpmk_mapping';

    protected $fillable = ['kelas_id', 'cpmk_nomor', 'komponen'];

    protected $casts = ['komponen' => 'array'];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }
}
