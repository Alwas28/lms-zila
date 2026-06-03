<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Kelas;

class RpsController extends Controller
{
    public function show(Kelas $kelas)
    {
        abort_if(!Enrollment::where('kelas_id', $kelas->id)->where('user_id', auth()->id())->exists(), 403);
        $kelas->load('mataKuliah', 'semester.tahunAkademik');
        $rps = $kelas->rps;

        return view('mahasiswa.rps.show', compact('kelas', 'rps'));
    }
}
