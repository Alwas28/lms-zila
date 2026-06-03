<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Pengampu;
use Illuminate\Http\Request;

class MataKuliahController extends Controller
{
    public function index()
    {
        $kelas = Kelas::whereHas('pengampu', function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->with([
                'mataKuliah',
                'semester.tahunAkademik',
            ])
            ->withCount(['mahasiswa', 'pertemuan'])
            ->get();

        return view('dosen.mata-kuliah.index', compact('kelas'));
    }
}
