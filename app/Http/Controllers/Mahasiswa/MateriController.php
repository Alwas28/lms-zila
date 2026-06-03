<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Kelas;

class MateriController extends Controller
{
    public function index(Kelas $kelas)
    {
        abort_if(!Enrollment::where('kelas_id', $kelas->id)->where('user_id', auth()->id())->exists(), 403);
        $kelas->load('mataKuliah', 'semester.tahunAkademik');

        $pertemuan = $kelas->pertemuan()->with('materi')->get();

        return view('mahasiswa.materi.index', compact('kelas', 'pertemuan'));
    }
}
