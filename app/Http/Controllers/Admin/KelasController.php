<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\MataKuliah;
use App\Models\Semester;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KelasController extends Controller
{
    public function index(Request $request): View
    {
        $semesterAktif = Semester::aktif();
        $semesterId    = $request->get('semester_id', $semesterAktif?->id);
        $semua         = Semester::with('tahunAkademik')->orderByDesc('id')->get();

        $query = Kelas::with(['mataKuliah', 'semester.tahunAkademik'])
                      ->withCount(['mahasiswa', 'dosen']);

        if ($semesterId) {
            $query->where('semester_id', $semesterId);
        }

        if ($request->filled('search')) {
            $q = $request->search;
            $query->whereHas('mataKuliah', fn($qb) =>
                $qb->where('nama', 'like', "%{$q}%")->orWhere('kode', 'like', "%{$q}%")
            );
        }

        $kelas      = $query->orderBy('nama_kelas')->paginate(15)->withQueryString();
        $mataKuliah = MataKuliah::aktif()->orderBy('kode')->get();
        $totalAktif = Kelas::where('is_aktif', true)->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))->count();

        return view('admin.kelas.index', compact('kelas', 'mataKuliah', 'semua', 'semesterId', 'semesterAktif', 'totalAktif'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'mata_kuliah_id' => ['required', 'exists:mata_kuliah,id'],
            'semester_id'    => ['required', 'exists:semesters,id'],
            'nama_kelas'     => ['required', 'string', 'max:5'],
            'kapasitas'      => ['required', 'integer', 'min:1', 'max:200'],
        ]);

        $exists = Kelas::where([
            'mata_kuliah_id' => $data['mata_kuliah_id'],
            'semester_id'    => $data['semester_id'],
            'nama_kelas'     => strtoupper($data['nama_kelas']),
        ])->exists();

        if ($exists) {
            return back()->withErrors(['nama_kelas' => 'Kelas ini sudah ada untuk semester tersebut.'])->withInput();
        }

        $data['nama_kelas'] = strtoupper($data['nama_kelas']);
        Kelas::create($data);

        return back()->with('success', 'Kelas berhasil dibuat.');
    }

    public function update(Request $request, Kelas $kelas): RedirectResponse
    {
        $data = $request->validate([
            'kapasitas' => ['required', 'integer', 'min:1', 'max:200'],
        ]);

        $kelas->update($data);

        return back()->with('success', "Kapasitas kelas {$kelas->nama_lengkap} diperbarui.");
    }

    public function tutup(Kelas $kelas): RedirectResponse
    {
        $kelas->tutup();

        return back()->with('success', "Kelas {$kelas->nama_lengkap} ditutup.");
    }

    public function buka(Kelas $kelas): RedirectResponse
    {
        $kelas->buka();

        return back()->with('success', "Kelas {$kelas->nama_lengkap} dibuka kembali.");
    }

    public function salin(Request $request): RedirectResponse
    {
        $request->validate([
            'dari_semester_id' => ['required', 'exists:semesters,id'],
            'ke_semester_id'   => ['required', 'exists:semesters,id', 'different:dari_semester_id'],
        ]);

        $kelasAsal = Kelas::where('semester_id', $request->dari_semester_id)->get();
        $disalin   = 0;

        foreach ($kelasAsal as $k) {
            $sudahAda = Kelas::where([
                'mata_kuliah_id' => $k->mata_kuliah_id,
                'semester_id'    => $request->ke_semester_id,
                'nama_kelas'     => $k->nama_kelas,
            ])->exists();

            if (!$sudahAda) {
                Kelas::create([
                    'mata_kuliah_id' => $k->mata_kuliah_id,
                    'semester_id'    => $request->ke_semester_id,
                    'nama_kelas'     => $k->nama_kelas,
                    'kapasitas'      => $k->kapasitas,
                    'is_aktif'       => true,
                ]);
                $disalin++;
            }
        }

        return back()->with('success', "{$disalin} kelas berhasil disalin ke semester tujuan.");
    }

    public function destroy(Kelas $kelas): RedirectResponse
    {
        $nama = $kelas->nama_lengkap;
        $kelas->delete();

        return back()->with('success', "Kelas {$nama} berhasil dihapus.");
    }
}
