<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\CpmkMapping;
use App\Models\Enrollment;
use App\Models\Kelas;
use App\Models\Penilaian;

class CapaianCpmkController extends Controller
{
    public function index(Kelas $kelas)
    {
        abort_if(!Enrollment::where('kelas_id', $kelas->id)->where('user_id', auth()->id())->exists(), 403);
        $kelas->load('mataKuliah', 'semester.tahunAkademik');

        $rps       = $kelas->rps;
        $cpmkLines = [];
        if ($rps && $rps->cpmk) {
            $cpmkLines = array_values(array_filter(
                array_map('trim', explode("\n", $rps->cpmk)),
                fn($l) => $l !== ''
            ));
        }

        $mappings = CpmkMapping::where('kelas_id', $kelas->id)
            ->get()->keyBy('cpmk_nomor')->map(fn($m) => $m->komponen ?? []);

        $penilaian = Penilaian::where('kelas_id', $kelas->id)
            ->where('user_id', auth()->id())
            ->first();

        $capaian    = [];
        $avgCapaian = [];

        foreach ($cpmkLines as $i => $line) {
            $nomor  = $i + 1;
            $mapped = $mappings[$nomor] ?? [];

            if ($penilaian && count($mapped) > 0) {
                $vals  = array_filter(array_map(fn($k) => $penilaian->{"nilai_$k"}, $mapped), fn($v) => $v !== null);
                $capaian[$nomor] = count($vals) > 0 ? round(array_sum($vals) / count($vals), 1) : null;
            } else {
                $capaian[$nomor] = null;
            }
        }

        $chartLabels = array_map(fn($i) => 'CPMK-' . ($i + 1), range(0, count($cpmkLines) - 1));
        $chartData   = array_map(fn($n) => $capaian[$n] ?? 0, range(1, count($cpmkLines)));

        return view('mahasiswa.capaian-cpmk.index', compact(
            'kelas', 'cpmkLines', 'mappings', 'capaian', 'chartLabels', 'chartData'
        ));
    }
}
