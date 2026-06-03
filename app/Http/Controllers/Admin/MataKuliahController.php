<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MataKuliah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MataKuliahController extends Controller
{
    public function index(Request $request): View
    {
        $tab   = $request->get('tab', 'aktif');
        $query = MataKuliah::query();

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(fn($qb) =>
                $qb->where('kode', 'like', "%{$q}%")
                   ->orWhere('nama', 'like', "%{$q}%")
            );
        }

        $query->when($tab === 'arsip', fn($q) => $q->arsip(), fn($q) => $q->aktif());

        $mataKuliah = $query->orderBy('kode')->paginate(15)->withQueryString();
        $totalAktif = MataKuliah::aktif()->count();
        $totalArsip = MataKuliah::arsip()->count();

        return view('admin.mata-kuliah.index', compact('mataKuliah', 'totalAktif', 'totalArsip', 'tab'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'kode'      => ['required', 'string', 'max:10', 'unique:mata_kuliah,kode'],
            'nama'      => ['required', 'string', 'max:100'],
            'sks'       => ['required', 'integer', 'min:1', 'max:6'],
            'deskripsi' => ['nullable', 'string', 'max:500'],
        ]);

        MataKuliah::create($data);

        return back()->with('success', "Mata kuliah {$data['kode']} — {$data['nama']} berhasil ditambahkan.");
    }

    public function update(Request $request, MataKuliah $mataKuliah): RedirectResponse
    {
        $data = $request->validate([
            'kode'      => ['required', 'string', 'max:10', Rule::unique('mata_kuliah', 'kode')->ignore($mataKuliah->id)],
            'nama'      => ['required', 'string', 'max:100'],
            'sks'       => ['required', 'integer', 'min:1', 'max:6'],
            'deskripsi' => ['nullable', 'string', 'max:500'],
        ]);

        $mataKuliah->update($data);

        return back()->with('success', "Mata kuliah {$mataKuliah->kode} berhasil diperbarui.");
    }

    public function arsipkan(MataKuliah $mataKuliah): RedirectResponse
    {
        $mataKuliah->arsipkan();

        return back()->with('success', "{$mataKuliah->nama} dipindahkan ke arsip.");
    }

    public function aktifkan(MataKuliah $mataKuliah): RedirectResponse
    {
        $mataKuliah->aktifkan();

        return back()->with('success', "{$mataKuliah->nama} diaktifkan kembali.");
    }

    public function destroy(MataKuliah $mataKuliah): RedirectResponse
    {
        $nama = $mataKuliah->nama;
        $mataKuliah->delete();

        return back()->with('success', "Mata kuliah {$nama} berhasil dihapus.");
    }
}
