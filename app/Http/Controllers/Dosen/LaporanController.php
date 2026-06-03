<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\CpmkMapping;
use App\Models\Kelas;
use App\Models\Penilaian;
use App\Models\PenilaianConfig;
use App\Models\Pengampu;
use App\Models\Pertemuan;
use App\Models\Presensi;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class LaporanController extends Controller
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

        $jumlahMahasiswa  = $kelas->mahasiswa()->count();
        $jumlahPertemuan  = $kelas->pertemuan()->count();
        $jumlahPenilaian  = Penilaian::where('kelas_id', $kelas->id)->count();

        $rps = $kelas->rps;
        $cpmkLines = [];
        if ($rps && $rps->cpmk) {
            $cpmkLines = array_values(array_filter(
                array_map('trim', explode("\n", $rps->cpmk)),
                fn($l) => $l !== ''
            ));
        }

        return view('dosen.laporan.index', compact(
            'kelas', 'jumlahMahasiswa', 'jumlahPertemuan', 'jumlahPenilaian', 'cpmkLines'
        ));
    }

    // ── NILAI ─────────────────────────────────────────────────

    public function nilaiPdf(Kelas $kelas)
    {
        $this->authorizeKelas($kelas);
        $kelas->load('mataKuliah', 'semester.tahunAkademik');

        $config    = PenilaianConfig::where('kelas_id', $kelas->id)->first();
        $mahasiswa = $kelas->mahasiswa()->orderBy('name')->get();
        $penilaian = Penilaian::where('kelas_id', $kelas->id)->get()->keyBy('user_id');

        $pdf = Pdf::loadView('dosen.laporan.pdf.nilai', compact('kelas', 'config', 'mahasiswa', 'penilaian'))
            ->setPaper('a4', 'landscape');

        $filename = 'Laporan_Nilai_' . str_replace(' ', '_', $kelas->mataKuliah->nama) . '_' . $kelas->nama_kelas . '.pdf';

        return $pdf->download($filename);
    }

    public function nilaiCsv(Kelas $kelas)
    {
        $this->authorizeKelas($kelas);
        $kelas->load('mataKuliah', 'semester.tahunAkademik');

        $mahasiswa = $kelas->mahasiswa()->orderBy('name')->get();
        $penilaian = Penilaian::where('kelas_id', $kelas->id)->get()->keyBy('user_id');

        $rows   = [];
        $rows[] = ['No', 'Nama', 'NIM/NIP', 'Kehadiran', 'Tugas', 'Quiz', 'UTS', 'UAS', 'Nilai Akhir', 'Grade'];

        foreach ($mahasiswa as $i => $mhs) {
            $p = $penilaian[$mhs->id] ?? null;
            $rows[] = [
                $i + 1,
                $mhs->name,
                $mhs->nip_nim ?? '-',
                $p?->nilai_kehadiran ?? '-',
                $p?->nilai_tugas ?? '-',
                $p?->nilai_quiz ?? '-',
                $p?->nilai_uts ?? '-',
                $p?->nilai_uas ?? '-',
                $p?->nilai_akhir ?? '-',
                $p?->grade ?? '-',
            ];
        }

        $filename = 'Laporan_Nilai_' . str_replace(' ', '_', $kelas->mataKuliah->nama) . '_' . $kelas->nama_kelas . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM for Excel
            foreach ($rows as $row) {
                fputcsv($out, $row);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    // ── PRESENSI ──────────────────────────────────────────────

    public function presensiPdf(Kelas $kelas)
    {
        $this->authorizeKelas($kelas);
        $kelas->load('mataKuliah', 'semester.tahunAkademik');

        [$mahasiswa, $pertemuan, $presensiMap, $rekapMhs, $rekapPertemuan] = $this->buildPresensiData($kelas);

        $pdf = Pdf::loadView('dosen.laporan.pdf.presensi', compact(
            'kelas', 'mahasiswa', 'pertemuan', 'presensiMap', 'rekapMhs'
        ))->setPaper('a4', 'landscape');

        $filename = 'Laporan_Presensi_' . str_replace(' ', '_', $kelas->mataKuliah->nama) . '_' . $kelas->nama_kelas . '.pdf';

        return $pdf->download($filename);
    }

    public function presensiCsv(Kelas $kelas)
    {
        $this->authorizeKelas($kelas);
        $kelas->load('mataKuliah', 'semester.tahunAkademik');

        [$mahasiswa, $pertemuan, $presensiMap, $rekapMhs] = $this->buildPresensiData($kelas);

        $header = ['No', 'Nama', 'NIM'];
        foreach ($pertemuan as $p) {
            $header[] = 'P-' . $p->nomor;
        }
        $header = array_merge($header, ['Hadir', 'Izin', 'Sakit', 'Alpha', '% Hadir']);

        $rows   = [$header];
        foreach ($mahasiswa as $i => $mhs) {
            $row = [$i + 1, $mhs->name, $mhs->nip_nim ?? '-'];
            foreach ($pertemuan as $p) {
                $row[] = strtoupper(substr($presensiMap[$mhs->id][$p->id] ?? 'alpha', 0, 1));
            }
            $rekap = $rekapMhs[$mhs->id] ?? ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpha' => 0];
            $total = $rekap['hadir'] + $rekap['izin'] + $rekap['sakit'] + $rekap['alpha'];
            $pct   = $total > 0 ? round($rekap['hadir'] / $total * 100) : 0;
            $row   = array_merge($row, [$rekap['hadir'], $rekap['izin'], $rekap['sakit'], $rekap['alpha'], $pct . '%']);
            $rows[] = $row;
        }

        $filename = 'Laporan_Presensi_' . str_replace(' ', '_', $kelas->mataKuliah->nama) . '_' . $kelas->nama_kelas . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            foreach ($rows as $row) {
                fputcsv($out, $row);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    // ── CAPAIAN CPMK ─────────────────────────────────────────

    public function capaianPdf(Kelas $kelas)
    {
        $this->authorizeKelas($kelas);
        $kelas->load('mataKuliah', 'semester.tahunAkademik');

        [$cpmkLines, $mappings, $mahasiswa, $capaian, $avgCapaian] = $this->buildCapaianData($kelas);

        $pdf = Pdf::loadView('dosen.laporan.pdf.capaian', compact(
            'kelas', 'cpmkLines', 'mappings', 'mahasiswa', 'capaian', 'avgCapaian'
        ))->setPaper('a4', 'landscape');

        $filename = 'Laporan_Capaian_CPMK_' . str_replace(' ', '_', $kelas->mataKuliah->nama) . '_' . $kelas->nama_kelas . '.pdf';

        return $pdf->download($filename);
    }

    public function capaianCsv(Kelas $kelas)
    {
        $this->authorizeKelas($kelas);
        $kelas->load('mataKuliah', 'semester.tahunAkademik');

        [$cpmkLines, $mappings, $mahasiswa, $capaian, $avgCapaian] = $this->buildCapaianData($kelas);

        $header = ['No', 'Nama', 'NIM'];
        foreach ($cpmkLines as $i => $line) {
            $header[] = 'CPMK-' . ($i + 1);
        }
        $header[] = 'Rata-rata';

        $rows = [$header];
        foreach ($mahasiswa as $i => $mhs) {
            $row    = [$i + 1, $mhs->name, $mhs->nip_nim ?? '-'];
            $scores = [];
            foreach ($cpmkLines as $j => $line) {
                $val    = $capaian[$mhs->id][$j + 1] ?? null;
                $row[]  = $val !== null ? number_format($val, 1) : '-';
                if ($val !== null) $scores[] = $val;
            }
            $row[]  = count($scores) > 0 ? number_format(array_sum($scores) / count($scores), 1) : '-';
            $rows[] = $row;
        }

        // Rata-rata kelas row
        $avgRow = ['', 'Rata-rata Kelas', ''];
        foreach ($cpmkLines as $i => $line) {
            $avgRow[] = $avgCapaian[$i + 1] !== null ? number_format($avgCapaian[$i + 1], 1) : '-';
        }
        $allAvgs = array_filter(array_values($avgCapaian), fn($v) => $v !== null);
        $avgRow[] = count($allAvgs) > 0 ? number_format(array_sum($allAvgs) / count($allAvgs), 1) : '-';
        $rows[] = $avgRow;

        $filename = 'Laporan_Capaian_CPMK_' . str_replace(' ', '_', $kelas->mataKuliah->nama) . '_' . $kelas->nama_kelas . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            foreach ($rows as $row) {
                fputcsv($out, $row);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    // ── Helpers ───────────────────────────────────────────────

    private function buildPresensiData(Kelas $kelas): array
    {
        $mahasiswa  = $kelas->mahasiswa()->orderBy('name')->get();
        $pertemuan  = $kelas->pertemuan()->with('presensiSesi.presensi')->get();

        // [user_id][pertemuan_id] => status
        $presensiMap   = [];
        $rekapMhs      = [];
        $rekapPertemuan = [];

        foreach ($mahasiswa as $mhs) {
            $rekapMhs[$mhs->id] = ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpha' => 0];
        }

        foreach ($pertemuan as $p) {
            $rekapPertemuan[$p->id] = ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpha' => 0];

            if (!$p->presensiSesi) continue;

            foreach ($p->presensiSesi->presensi as $pres) {
                $presensiMap[$pres->user_id][$p->id] = $pres->status;

                if (isset($rekapMhs[$pres->user_id])) {
                    $rekapMhs[$pres->user_id][$pres->status]++;
                }
                $rekapPertemuan[$p->id][$pres->status]++;
            }
        }

        return [$mahasiswa, $pertemuan, $presensiMap, $rekapMhs, $rekapPertemuan];
    }

    private function buildCapaianData(Kelas $kelas): array
    {
        $rps        = $kelas->rps;
        $cpmkLines  = [];
        if ($rps && $rps->cpmk) {
            $cpmkLines = array_values(array_filter(
                array_map('trim', explode("\n", $rps->cpmk)),
                fn($l) => $l !== ''
            ));
        }

        $mappings   = CpmkMapping::where('kelas_id', $kelas->id)
            ->get()
            ->keyBy('cpmk_nomor')
            ->map(fn($m) => $m->komponen ?? []);

        $mahasiswa  = $kelas->mahasiswa()->orderBy('name')->get();
        $penilaian  = Penilaian::where('kelas_id', $kelas->id)->get()->keyBy('user_id');

        $capaian    = [];
        $avgCapaian = [];

        foreach ($cpmkLines as $i => $line) {
            $nomor  = $i + 1;
            $mapped = $mappings[$nomor] ?? [];
            $scores = [];

            foreach ($mahasiswa as $mhs) {
                $nilai = $penilaian[$mhs->id] ?? null;
                if ($nilai && count($mapped) > 0) {
                    $vals  = array_filter(array_map(fn($k) => $nilai->{"nilai_$k"}, $mapped), fn($v) => $v !== null);
                    $score = count($vals) > 0 ? round(array_sum($vals) / count($vals), 1) : null;
                } else {
                    $score = null;
                }
                $capaian[$mhs->id][$nomor] = $score;
                if ($score !== null) $scores[] = $score;
            }

            $avgCapaian[$nomor] = count($scores) > 0 ? round(array_sum($scores) / count($scores), 1) : null;
        }

        return [$cpmkLines, $mappings, $mahasiswa, $capaian, $avgCapaian];
    }
}
