<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Kelas;
use App\Models\Penilaian;
use App\Models\PenilaianConfig;

class NilaiController extends Controller
{
    public function index(Kelas $kelas)
    {
        abort_if(!Enrollment::where('kelas_id', $kelas->id)->where('user_id', auth()->id())->exists(), 403);
        $kelas->load('mataKuliah', 'semester.tahunAkademik');

        $penilaian = Penilaian::where('kelas_id', $kelas->id)
            ->where('user_id', auth()->id())
            ->first();

        $config = PenilaianConfig::where('kelas_id', $kelas->id)->first();

        return view('mahasiswa.nilai.index', compact('kelas', 'penilaian', 'config'));
    }
}
