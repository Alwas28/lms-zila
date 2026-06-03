<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Pengumuman;

class KelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::whereHas('enrollments', fn($q) => $q->where('user_id', auth()->id()))
            ->with(['mataKuliah', 'semester.tahunAkademik', 'dosen'])
            ->withCount(['pengumuman', 'forumTopik'])
            ->get();

        return view('mahasiswa.kelas.index', compact('kelas'));
    }
}
