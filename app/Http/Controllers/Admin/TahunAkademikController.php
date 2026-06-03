<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TahunAkademik;
use App\Models\Semester;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TahunAkademikController extends Controller
{
    public function index(): View
    {
        $tahunAkademik = TahunAkademik::with('semesters')->orderByDesc('nama')->get();
        $aktif         = TahunAkademik::aktif();

        return view('admin.tahun-akademik.index', compact('tahunAkademik', 'aktif'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nama' => ['required', 'string', 'regex:/^\d{4}\/\d{4}$/', 'unique:tahun_akademik,nama'],
        ], [
            'nama.regex'  => 'Format tahun akademik harus: YYYY/YYYY (contoh: 2025/2026)',
            'nama.unique' => 'Tahun akademik ini sudah ada.',
        ]);

        $ta = TahunAkademik::create(['nama' => $request->nama]);

        // Otomatis buat dua semester (ganjil & genap)
        Semester::create(['tahun_akademik_id' => $ta->id, 'tipe' => 'ganjil', 'is_aktif' => false]);
        Semester::create(['tahun_akademik_id' => $ta->id, 'tipe' => 'genap',  'is_aktif' => false]);

        return back()->with('success', "Tahun akademik {$ta->nama} berhasil ditambahkan.");
    }

    public function destroy(TahunAkademik $tahunAkademik): RedirectResponse
    {
        if ($tahunAkademik->is_aktif) {
            return back()->with('error', 'Tahun akademik aktif tidak dapat dihapus.');
        }

        $tahunAkademik->delete();

        return back()->with('success', 'Tahun akademik berhasil dihapus.');
    }

    public function aktifkan(TahunAkademik $tahunAkademik): RedirectResponse
    {
        $tahunAkademik->aktifkan();

        return back()->with('success', "Tahun akademik {$tahunAkademik->nama} diaktifkan.");
    }

    public function nonaktifkan(TahunAkademik $tahunAkademik): RedirectResponse
    {
        $tahunAkademik->update(['is_aktif' => false]);

        return back()->with('success', "Tahun akademik {$tahunAkademik->nama} dinonaktifkan.");
    }
}
