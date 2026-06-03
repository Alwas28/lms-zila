<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\BankSoal;
use App\Models\Kelas;
use App\Models\Pengampu;
use App\Models\PilihanSoal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BankSoalController extends Controller
{
    public function index(Request $request): View
    {
        $dosenId = auth()->id();

        // Semua kelas yang diampu dosen ini (untuk filter)
        $kelasDiampu = Kelas::whereHas('pengampu', fn($q) => $q->where('user_id', $dosenId))
            ->with('mataKuliah')
            ->get();

        // Semua kategori milik dosen ini (untuk filter dropdown)
        $kategori = BankSoal::milikDosen($dosenId)
            ->whereNotNull('kategori')
            ->distinct()
            ->pluck('kategori')
            ->sort()
            ->values();

        $query = BankSoal::milikDosen($dosenId)->with(['pilihan', 'kelas.mataKuliah']);

        // Filter tipe
        if ($request->filled('tipe') && in_array($request->tipe, ['pilihan_ganda', 'essay'])) {
            $query->where('tipe', $request->tipe);
        }

        // Filter kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // Filter kelas
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        // Search
        if ($request->filled('search')) {
            $q = $request->search;
            $query->where('pertanyaan', 'like', "%{$q}%");
        }

        $soal        = $query->latest()->paginate(15)->withQueryString();
        $totalPg     = BankSoal::milikDosen($dosenId)->pilihanGanda()->count();
        $totalEssay  = BankSoal::milikDosen($dosenId)->essay()->count();
        $total       = $totalPg + $totalEssay;

        return view('dosen.bank-soal.index', compact(
            'soal', 'kelasDiampu', 'kategori', 'total', 'totalPg', 'totalEssay'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'tipe'       => ['required', Rule::in(['pilihan_ganda', 'essay'])],
            'pertanyaan' => ['required', 'string'],
            'bobot'      => ['required', 'integer', 'min:1', 'max:100'],
            'kategori'   => ['nullable', 'string', 'max:100'],
            'kelas_id'   => ['nullable', 'exists:kelas,id'],
            'rubrik'     => ['nullable', 'required_if:tipe,essay', 'string'],
            // Pilihan ganda
            'pilihan'    => ['required_if:tipe,pilihan_ganda', 'array', 'min:2'],
            'pilihan.*'  => ['required_if:tipe,pilihan_ganda', 'string'],
            'kunci'      => ['required_if:tipe,pilihan_ganda', 'integer'],
        ]);

        // Validasi otorisasi kelas jika dipilih
        if (!empty($data['kelas_id'])) {
            abort_if(
                !Pengampu::where('kelas_id', $data['kelas_id'])->where('user_id', auth()->id())->exists(),
                403
            );
        }

        $soal = BankSoal::create([
            'user_id'    => auth()->id(),
            'kelas_id'   => $data['kelas_id'] ?? null,
            'tipe'       => $data['tipe'],
            'pertanyaan' => $data['pertanyaan'],
            'bobot'      => $data['bobot'],
            'kategori'   => $data['kategori'] ?? null,
            'rubrik'     => $data['rubrik'] ?? null,
        ]);

        // Simpan pilihan untuk PG
        if ($data['tipe'] === 'pilihan_ganda' && !empty($data['pilihan'])) {
            foreach ($data['pilihan'] as $idx => $teks) {
                if (trim($teks) === '') continue;
                PilihanSoal::create([
                    'bank_soal_id' => $soal->id,
                    'teks'         => $teks,
                    'is_benar'     => ($idx == ($data['kunci'] ?? -1)),
                    'urutan'       => $idx,
                ]);
            }
        }

        return back()->with('success', 'Soal berhasil ditambahkan.');
    }

    public function update(Request $request, BankSoal $bankSoal): RedirectResponse
    {
        abort_if($bankSoal->user_id !== auth()->id(), 403);

        $data = $request->validate([
            'pertanyaan' => ['required', 'string'],
            'bobot'      => ['required', 'integer', 'min:1', 'max:100'],
            'kategori'   => ['nullable', 'string', 'max:100'],
            'kelas_id'   => ['nullable', 'exists:kelas,id'],
            'rubrik'     => ['nullable', 'string'],
            'pilihan'    => ['nullable', 'array'],
            'pilihan.*'  => ['nullable', 'string'],
            'kunci'      => ['nullable', 'integer'],
        ]);

        $bankSoal->update([
            'kelas_id'   => $data['kelas_id'] ?? null,
            'pertanyaan' => $data['pertanyaan'],
            'bobot'      => $data['bobot'],
            'kategori'   => $data['kategori'] ?? null,
            'rubrik'     => $data['rubrik'] ?? null,
        ]);

        // Sinkron pilihan untuk PG
        if ($bankSoal->tipe === 'pilihan_ganda' && !empty($data['pilihan'])) {
            $bankSoal->pilihan()->delete();
            foreach ($data['pilihan'] as $idx => $teks) {
                if (trim($teks) === '') continue;
                PilihanSoal::create([
                    'bank_soal_id' => $bankSoal->id,
                    'teks'         => $teks,
                    'is_benar'     => ($idx == ($data['kunci'] ?? -1)),
                    'urutan'       => $idx,
                ]);
            }
        }

        return back()->with('success', 'Soal berhasil diperbarui.');
    }

    public function destroy(BankSoal $bankSoal): RedirectResponse
    {
        abort_if($bankSoal->user_id !== auth()->id(), 403);
        $bankSoal->delete();

        return back()->with('success', 'Soal berhasil dihapus.');
    }
}
