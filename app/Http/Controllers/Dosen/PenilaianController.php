<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Penilaian;
use App\Models\PenilaianConfig;
use App\Models\Pengampu;
use Illuminate\Http\Request;

class PenilaianController extends Controller
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

        $config = PenilaianConfig::firstOrCreate(
            ['kelas_id' => $kelas->id],
            [
                'bobot_kehadiran' => 10,
                'bobot_tugas'     => 20,
                'bobot_quiz'      => 20,
                'bobot_uts'       => 25,
                'bobot_uas'       => 25,
            ]
        );

        $mahasiswa = $kelas->mahasiswa()->get();

        $penilaianMap = Penilaian::where('kelas_id', $kelas->id)
            ->get()
            ->keyBy('user_id');

        return view('dosen.penilaian.index', compact('kelas', 'config', 'mahasiswa', 'penilaianMap'));
    }

    public function updateConfig(Request $request, Kelas $kelas)
    {
        $this->authorizeKelas($kelas);

        $data = $request->validate([
            'bobot_kehadiran' => 'required|integer|min:0|max:100',
            'bobot_tugas'     => 'required|integer|min:0|max:100',
            'bobot_quiz'      => 'required|integer|min:0|max:100',
            'bobot_uts'       => 'required|integer|min:0|max:100',
            'bobot_uas'       => 'required|integer|min:0|max:100',
        ]);

        $total = array_sum($data);
        if ($total !== 100) {
            return back()->with('error', "Total bobot harus 100%. Saat ini: {$total}%.");
        }

        PenilaianConfig::updateOrCreate(['kelas_id' => $kelas->id], $data);

        return back()->with('success', 'Konfigurasi bobot berhasil disimpan.');
    }

    public function simpan(Request $request, Kelas $kelas)
    {
        $this->authorizeKelas($kelas);

        $data = $request->validate([
            'penilaian'              => 'required|array',
            'penilaian.*.user_id'    => 'required|integer|exists:users,id',
            'penilaian.*.kehadiran'  => 'nullable|numeric|min:0|max:100',
            'penilaian.*.tugas'      => 'nullable|numeric|min:0|max:100',
            'penilaian.*.quiz'       => 'nullable|numeric|min:0|max:100',
            'penilaian.*.uts'        => 'nullable|numeric|min:0|max:100',
            'penilaian.*.uas'        => 'nullable|numeric|min:0|max:100',
        ]);

        $config = PenilaianConfig::firstOrCreate(
            ['kelas_id' => $kelas->id],
            [
                'bobot_kehadiran' => 10,
                'bobot_tugas'     => 20,
                'bobot_quiz'      => 20,
                'bobot_uts'       => 25,
                'bobot_uas'       => 25,
            ]
        );

        foreach ($data['penilaian'] as $row) {
            $p = Penilaian::firstOrNew([
                'kelas_id' => $kelas->id,
                'user_id'  => $row['user_id'],
            ]);

            $p->kelas_id        = $kelas->id;
            $p->user_id         = $row['user_id'];
            $p->nilai_kehadiran = $row['kehadiran'] ?? null;
            $p->nilai_tugas     = $row['tugas'] ?? null;
            $p->nilai_quiz      = $row['quiz'] ?? null;
            $p->nilai_uts       = $row['uts'] ?? null;
            $p->nilai_uas       = $row['uas'] ?? null;

            $nilaiAkhir    = $p->hitungNilaiAkhir($config);
            $p->nilai_akhir = $nilaiAkhir;
            $p->grade       = Penilaian::hitungGrade($nilaiAkhir);

            $p->save();
        }

        return back()->with('success', 'Nilai berhasil disimpan.');
    }
}
