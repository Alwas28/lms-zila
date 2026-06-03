<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\CpmkMapping;
use App\Models\Kelas;
use App\Models\Penilaian;
use App\Models\Pengampu;
use Illuminate\Http\Request;

class CapaianCpmkController extends Controller
{
    private function authorizeKelas(Kelas $kelas): void
    {
        abort_if(
            !Pengampu::where('kelas_id', $kelas->id)->where('user_id', auth()->id())->exists(),
            403, 'Anda tidak memiliki akses ke kelas ini.'
        );
    }

    public function index(Kelas $kelas)
    {
        $this->authorizeKelas($kelas);

        $kelas->load('mataKuliah', 'semester.tahunAkademik');

        $rps = $kelas->rps;

        // Parse CPMK text: each non-empty line = one CPMK item
        $cpmkLines = [];
        if ($rps && $rps->cpmk) {
            $cpmkLines = array_values(array_filter(
                array_map('trim', explode("\n", $rps->cpmk)),
                fn($l) => $l !== ''
            ));
        }

        // Mappings keyed by cpmk_nomor → array of komponen
        $mappings = CpmkMapping::where('kelas_id', $kelas->id)
            ->get()
            ->keyBy('cpmk_nomor')
            ->map(fn($m) => $m->komponen ?? []);

        $mahasiswa  = $kelas->mahasiswa()->orderBy('name')->get();
        $penilaian  = Penilaian::where('kelas_id', $kelas->id)->get()->keyBy('user_id');

        $komponen = ['kehadiran', 'tugas', 'quiz', 'uts', 'uas'];

        // Calculate per-student, per-CPMK achievement
        $capaian    = [];   // [user_id][cpmk_nomor] => score|null
        $avgCapaian = [];   // [cpmk_nomor] => class avg|null

        foreach ($cpmkLines as $i => $line) {
            $nomor   = $i + 1;
            $mapped  = $mappings[$nomor] ?? [];
            $scores  = [];

            foreach ($mahasiswa as $mhs) {
                $nilai = $penilaian[$mhs->id] ?? null;

                if ($nilai && count($mapped) > 0) {
                    $vals = array_filter(
                        array_map(fn($k) => $nilai->{"nilai_$k"}, $mapped),
                        fn($v) => $v !== null
                    );
                    $score = count($vals) > 0 ? round(array_sum($vals) / count($vals), 1) : null;
                } else {
                    $score = null;
                }

                $capaian[$mhs->id][$nomor] = $score;
                if ($score !== null) {
                    $scores[] = $score;
                }
            }

            $avgCapaian[$nomor] = count($scores) > 0
                ? round(array_sum($scores) / count($scores), 1)
                : null;
        }

        // Chart data
        $chartLabels = array_map(fn($i) => 'CPMK-' . ($i + 1), range(0, count($cpmkLines) - 1));
        $chartData   = array_map(fn($n) => $avgCapaian[$n] ?? 0, range(1, count($cpmkLines)));

        return view('dosen.capaian-cpmk.index', compact(
            'kelas', 'rps', 'cpmkLines', 'mappings',
            'mahasiswa', 'penilaian', 'capaian', 'avgCapaian',
            'komponen', 'chartLabels', 'chartData'
        ));
    }

    public function update(Request $request, Kelas $kelas)
    {
        $this->authorizeKelas($kelas);

        $data = $request->input('mapping', []);

        foreach ($data as $nomor => $selected) {
            CpmkMapping::updateOrCreate(
                ['kelas_id' => $kelas->id, 'cpmk_nomor' => (int) $nomor],
                ['komponen' => is_array($selected) ? $selected : []]
            );
        }

        return back()->with('success', 'Mapping CPMK berhasil disimpan.');
    }
}
