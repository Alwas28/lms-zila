<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Kelas;
use App\Models\Presensi;

class PresensiController extends Controller
{
    private function authorizeKelas(Kelas $kelas): void
    {
        abort_if(!Enrollment::where('kelas_id', $kelas->id)->where('user_id', auth()->id())->exists(), 403);
    }

    public function index(Kelas $kelas)
    {
        $this->authorizeKelas($kelas);
        $kelas->load('mataKuliah', 'semester.tahunAkademik');

        $pertemuan = $kelas->pertemuan()->with('presensiSesi')->get();

        $presensiMap = Presensi::whereIn(
                'presensi_sesi_id',
                $pertemuan->pluck('presensiSesi')->filter()->pluck('id')
            )
            ->where('user_id', auth()->id())
            ->get()
            ->keyBy('presensi_sesi_id');

        $rekap = [
            'hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpha' => 0, 'total' => 0,
        ];
        foreach ($presensiMap as $p) {
            $rekap[$p->status]++;
            $rekap['total']++;
        }

        $sesiAktif = $pertemuan->map->presensiSesi->filter(fn($s) => $s && $s->is_aktif)->first();

        return view('mahasiswa.presensi.index', compact('kelas', 'pertemuan', 'presensiMap', 'rekap', 'sesiAktif'));
    }
}
