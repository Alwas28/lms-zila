<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Kelas;
use App\Models\Pengumuman;

class PengumumanController extends Controller
{
    public function index(Kelas $kelas)
    {
        abort_if(
            !Enrollment::where('kelas_id', $kelas->id)->where('user_id', auth()->id())->exists(),
            403
        );
        $kelas->load('mataKuliah', 'semester.tahunAkademik');

        $pengumuman = Pengumuman::where('kelas_id', $kelas->id)
            ->with('penulis')
            ->orderByDesc('is_pinned')
            ->latest()
            ->get();

        return view('mahasiswa.pengumuman.index', compact('kelas', 'pengumuman'));
    }
}
