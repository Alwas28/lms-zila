<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Kelas;
use App\Models\Ujian;
use App\Models\UjianJawaban;
use App\Models\UjianSesi;
use Illuminate\Http\Request;

class UjianController extends Controller
{
    private function authorizeKelas(Kelas $kelas): void
    {
        abort_if(!Enrollment::where('kelas_id', $kelas->id)->where('user_id', auth()->id())->exists(), 403);
    }

    public function index(Kelas $kelas)
    {
        $this->authorizeKelas($kelas);
        $kelas->load('mataKuliah', 'semester.tahunAkademik');

        $ujian = Ujian::where('kelas_id', $kelas->id)
            ->where('status', '!=', 'draft')
            ->orderBy('mulai_at')
            ->get();

        $sesiMap = UjianSesi::where('user_id', auth()->id())
            ->whereIn('ujian_id', $ujian->pluck('id'))
            ->get()
            ->keyBy('ujian_id');

        return view('mahasiswa.ujian.index', compact('kelas', 'ujian', 'sesiMap'));
    }

    public function show(Kelas $kelas, Ujian $ujian)
    {
        $this->authorizeKelas($kelas);
        abort_if($ujian->kelas_id !== $kelas->id, 404);
        $kelas->load('mataKuliah', 'semester.tahunAkademik');

        $sesi = UjianSesi::where('ujian_id', $ujian->id)->where('user_id', auth()->id())->latest()->first();

        return view('mahasiswa.ujian.show', compact('kelas', 'ujian', 'sesi'));
    }

    public function mulai(Kelas $kelas, Ujian $ujian)
    {
        $this->authorizeKelas($kelas);
        abort_if($ujian->kelas_id !== $kelas->id, 404);
        abort_if($ujian->status !== 'aktif', 403, 'Ujian belum dibuka.');

        $now = now();
        if ($ujian->mulai_at && $now->lt($ujian->mulai_at)) {
            return back()->with('error', 'Ujian belum dimulai.');
        }
        if ($ujian->selesai_at && $now->gt($ujian->selesai_at)) {
            return back()->with('error', 'Ujian sudah berakhir.');
        }

        $percobaan = UjianSesi::where('ujian_id', $ujian->id)->where('user_id', auth()->id())->count();
        if ($percobaan >= $ujian->batas_percobaan) {
            return back()->with('error', 'Anda sudah mencapai batas percobaan ujian.');
        }

        $sesi = UjianSesi::create([
            'ujian_id'     => $ujian->id,
            'user_id'      => auth()->id(),
            'percobaan_ke' => $percobaan + 1,
            'mulai_at'     => now(),
        ]);

        return redirect()->route('mahasiswa.ujian.kerjakan', [$kelas, $ujian]);
    }

    public function kerjakan(Kelas $kelas, Ujian $ujian)
    {
        $this->authorizeKelas($kelas);
        abort_if($ujian->kelas_id !== $kelas->id, 404);

        $sesi = UjianSesi::where('ujian_id', $ujian->id)
            ->where('user_id', auth()->id())
            ->where('is_selesai', false)
            ->latest()
            ->firstOrFail();

        // Waktu habis → auto-submit
        if ($sesi->sisa_waktu_detik <= 0) {
            return $this->prosesSelesai($sesi, $ujian, []);
        }

        $soalList = $ujian->ujianSoal()->with(['bankSoal.pilihan'])->get();
        if ($ujian->is_acak_soal) $soalList = $soalList->shuffle();

        $jawabanSudah = $sesi->jawaban()->pluck('pilihan_soal_id', 'bank_soal_id');

        return view('mahasiswa.ujian.kerjakan', compact('kelas', 'ujian', 'sesi', 'soalList', 'jawabanSudah'));
    }

    public function selesai(Request $request, Kelas $kelas, Ujian $ujian)
    {
        $this->authorizeKelas($kelas);
        abort_if($ujian->kelas_id !== $kelas->id, 404);

        $sesi = UjianSesi::where('ujian_id', $ujian->id)
            ->where('user_id', auth()->id())
            ->where('is_selesai', false)
            ->latest()
            ->firstOrFail();

        $jawaban = $request->input('jawaban', []);
        return $this->prosesSelesai($sesi, $ujian, $jawaban);
    }

    private function prosesSelesai(UjianSesi $sesi, Ujian $ujian, array $jawaban)
    {
        $soalList  = $ujian->ujianSoal()->with(['bankSoal.pilihan'])->get();
        $totalPoin = 0;
        $bobot     = $soalList->count() > 0 ? 100 / $soalList->count() : 0;

        foreach ($soalList as $ujianSoal) {
            $soal        = $ujianSoal->bankSoal;
            $pilihanId   = $jawaban[$soal->id] ?? null;
            $isBenar     = null;
            $poin        = 0;

            if ($soal->tipe === 'pilihan_ganda' && $pilihanId) {
                $isBenar = $soal->pilihan->firstWhere('id', $pilihanId)?->is_benar ?? false;
                $poin    = $isBenar ? round($bobot, 2) : 0;
            }

            UjianJawaban::updateOrCreate(
                ['ujian_sesi_id' => $sesi->id, 'bank_soal_id' => $soal->id],
                ['pilihan_soal_id' => $pilihanId, 'is_benar' => $isBenar, 'poin' => $poin]
            );

            $totalPoin += $poin;
        }

        $sesi->update([
            'is_selesai' => true,
            'selesai_at' => now(),
            'nilai'      => min(100, round($totalPoin, 2)),
        ]);

        return redirect()->route('mahasiswa.ujian.hasil', [$ujian->kelas, $ujian])
            ->with('success', 'Ujian selesai! Nilai Anda: ' . number_format($sesi->nilai, 1));
    }

    public function hasil(Kelas $kelas, Ujian $ujian)
    {
        $this->authorizeKelas($kelas);
        abort_if($ujian->kelas_id !== $kelas->id, 404);
        $kelas->load('mataKuliah', 'semester.tahunAkademik');

        $sesi = UjianSesi::where('ujian_id', $ujian->id)
            ->where('user_id', auth()->id())
            ->where('is_selesai', true)
            ->latest()
            ->firstOrFail();

        $jawaban = $sesi->jawaban()->with(['soal.pilihan', 'pilihan'])->get()->keyBy('bank_soal_id');
        $soalList = $ujian->ujianSoal()->with(['bankSoal.pilihan'])->get();

        return view('mahasiswa.ujian.hasil', compact('kelas', 'ujian', 'sesi', 'soalList', 'jawaban'));
    }
}
