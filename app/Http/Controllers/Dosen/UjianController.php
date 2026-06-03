<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\BankSoal;
use App\Models\Kelas;
use App\Models\Pengampu;
use App\Models\Ujian;
use App\Models\UjianSoal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UjianController extends Controller
{
    private function authorize(Kelas $kelas): void
    {
        abort_if(
            !Pengampu::where('kelas_id', $kelas->id)->where('user_id', auth()->id())->exists(),
            403, 'Anda tidak memiliki akses ke kelas ini.'
        );
    }

    public function index(Kelas $kelas): View
    {
        $this->authorize($kelas);
        $kelas->load('mataKuliah', 'semester.tahunAkademik');

        $semua = $kelas->ujian()
            ->withCount('ujianSoal')
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('tipe');

        $quiz = $semua->get('quiz', collect());
        $uts  = $semua->get('uts',  collect());
        $uas  = $semua->get('uas',  collect());

        // Bank soal milik dosen ini (untuk modal tambah soal ke ujian)
        $bankSoal = BankSoal::where('user_id', auth()->id())
            ->with('pilihan')
            ->orderBy('kategori')
            ->orderBy('id')
            ->get();

        return view('dosen.ujian.index', compact('kelas', 'quiz', 'uts', 'uas', 'bankSoal'));
    }

    public function store(Request $request, Kelas $kelas): RedirectResponse
    {
        $this->authorize($kelas);

        $data = $request->validate([
            'judul'            => ['required', 'string', 'max:150'],
            'tipe'             => ['required', Rule::in(['quiz', 'uts', 'uas'])],
            'deskripsi'        => ['nullable', 'string'],
            'durasi'           => ['nullable', 'integer', 'min:1', 'max:300'],
            'mulai_at'         => ['nullable', 'date'],
            'selesai_at'       => ['nullable', 'date', 'after_or_equal:mulai_at'],
            'jumlah_soal'      => ['required', 'integer', 'min:1', 'max:200'],
            'is_acak_soal'     => ['boolean'],
            'is_acak_jawaban'  => ['boolean'],
            'batas_percobaan'  => ['required', 'integer', 'min:1', 'max:10'],
        ]);

        $kelas->ujian()->create($data);

        return back()
            ->with('success', "Ujian \"{$data['judul']}\" berhasil dibuat.")
            ->with('active_tab', $data['tipe']);
    }

    public function update(Request $request, Kelas $kelas, Ujian $ujian): RedirectResponse
    {
        $this->authorize($kelas);
        abort_if($ujian->kelas_id !== $kelas->id, 404);

        $data = $request->validate([
            'judul'            => ['required', 'string', 'max:150'],
            'deskripsi'        => ['nullable', 'string'],
            'durasi'           => ['nullable', 'integer', 'min:1', 'max:300'],
            'mulai_at'         => ['nullable', 'date'],
            'selesai_at'       => ['nullable', 'date', 'after_or_equal:mulai_at'],
            'jumlah_soal'      => ['required', 'integer', 'min:1', 'max:200'],
            'is_acak_soal'     => ['boolean'],
            'is_acak_jawaban'  => ['boolean'],
            'batas_percobaan'  => ['required', 'integer', 'min:1', 'max:10'],
        ]);

        $ujian->update($data);

        return back()
            ->with('success', "Ujian \"{$ujian->judul}\" berhasil diperbarui.")
            ->with('active_tab', $ujian->tipe);
    }

    public function destroy(Kelas $kelas, Ujian $ujian): RedirectResponse
    {
        $this->authorize($kelas);
        abort_if($ujian->kelas_id !== $kelas->id, 404);

        $tipe  = $ujian->tipe;
        $judul = $ujian->judul;
        $ujian->delete();

        return back()
            ->with('success', "Ujian \"{$judul}\" dihapus.")
            ->with('active_tab', $tipe);
    }

    public function aktifkan(Kelas $kelas, Ujian $ujian): RedirectResponse
    {
        $this->authorize($kelas);
        abort_if($ujian->kelas_id !== $kelas->id, 404);
        abort_if($ujian->ujianSoal()->count() === 0, 422, 'Tambahkan soal terlebih dahulu sebelum mengaktifkan ujian.');

        $ujian->aktifkan();

        return back()
            ->with('success', "Ujian \"{$ujian->judul}\" diaktifkan. Mahasiswa dapat mulai mengerjakan.")
            ->with('active_tab', $ujian->tipe);
    }

    public function tutup(Kelas $kelas, Ujian $ujian): RedirectResponse
    {
        $this->authorize($kelas);
        abort_if($ujian->kelas_id !== $kelas->id, 404);

        $ujian->tutup();

        return back()
            ->with('success', "Ujian \"{$ujian->judul}\" ditutup.")
            ->with('active_tab', $ujian->tipe);
    }

    public function draftkan(Kelas $kelas, Ujian $ujian): RedirectResponse
    {
        $this->authorize($kelas);
        abort_if($ujian->kelas_id !== $kelas->id, 404);
        abort_if($ujian->status === 'aktif', 422, 'Tutup ujian terlebih dahulu sebelum kembali ke draft.');

        $ujian->draftkan();

        return back()
            ->with('success', "Ujian \"{$ujian->judul}\" dikembalikan ke draft.")
            ->with('active_tab', $ujian->tipe);
    }

    /** Tambah soal satu per satu dari bank ke ujian */
    public function tambahSoal(Request $request, Kelas $kelas, Ujian $ujian): RedirectResponse
    {
        $this->authorize($kelas);
        abort_if($ujian->kelas_id !== $kelas->id, 404);

        $request->validate([
            'bank_soal_ids'   => ['required', 'array', 'min:1'],
            'bank_soal_ids.*' => ['integer', 'exists:bank_soal,id'],
        ]);

        $ditambah = 0;
        foreach ($request->bank_soal_ids as $id) {
            // Pastikan soal milik dosen ini
            $soal = BankSoal::where('id', $id)->where('user_id', auth()->id())->first();
            if (!$soal) continue;

            $nomor = $ujian->ujianSoal()->max('nomor') + 1;
            UjianSoal::firstOrCreate(
                ['ujian_id' => $ujian->id, 'bank_soal_id' => $id],
                ['nomor'    => $nomor]
            );
            $ditambah++;
        }

        return back()
            ->with('success', "{$ditambah} soal ditambahkan ke ujian.")
            ->with('active_tab', $ujian->tipe);
    }

    /** Random: ambil N soal acak dari bank */
    public function randomSoal(Request $request, Kelas $kelas, Ujian $ujian): RedirectResponse
    {
        $this->authorize($kelas);
        abort_if($ujian->kelas_id !== $kelas->id, 404);

        $request->validate([
            'jumlah'   => ['required', 'integer', 'min:1', 'max:100'],
            'tipe_soal' => ['nullable', Rule::in(['pilihan_ganda', 'essay', ''])],
            'kategori'  => ['nullable', 'string'],
        ]);

        $query = BankSoal::where('user_id', auth()->id());
        if ($request->filled('tipe_soal'))  $query->where('tipe', $request->tipe_soal);
        if ($request->filled('kategori'))   $query->where('kategori', $request->kategori);

        // Exclude yang sudah ada di ujian ini
        $sudahAda = $ujian->ujianSoal()->pluck('bank_soal_id');
        $query->whereNotIn('id', $sudahAda);

        $soalRandom = $query->inRandomOrder()->limit($request->jumlah)->get();

        foreach ($soalRandom as $soal) {
            $nomor = $ujian->ujianSoal()->max('nomor') + 1;
            UjianSoal::create([
                'ujian_id'    => $ujian->id,
                'bank_soal_id' => $soal->id,
                'nomor'        => $nomor,
            ]);
        }

        return back()
            ->with('success', "{$soalRandom->count()} soal berhasil dipilih secara acak.")
            ->with('active_tab', $ujian->tipe);
    }

    /** Hapus satu soal dari ujian */
    public function hapusSoal(Kelas $kelas, Ujian $ujian, UjianSoal $ujianSoal): RedirectResponse
    {
        $this->authorize($kelas);
        abort_if($ujian->kelas_id !== $kelas->id, 404);
        abort_if($ujianSoal->ujian_id !== $ujian->id, 404);

        $ujianSoal->delete();

        // Urutkan ulang nomor
        $ujian->ujianSoal()->orderBy('nomor')->get()->each(function ($s, $i) {
            $s->update(['nomor' => $i + 1]);
        });

        return back()
            ->with('success', 'Soal dihapus dari ujian.')
            ->with('active_tab', $ujian->tipe);
    }

    /** Hapus semua soal dari ujian */
    public function hapusSemuaSoal(Kelas $kelas, Ujian $ujian): RedirectResponse
    {
        $this->authorize($kelas);
        abort_if($ujian->kelas_id !== $kelas->id, 404);

        $ujian->ujianSoal()->delete();

        return back()
            ->with('success', 'Semua soal dihapus dari ujian.')
            ->with('active_tab', $ujian->tipe);
    }
}
