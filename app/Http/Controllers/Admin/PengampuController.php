<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Pengampu;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PengampuController extends Controller
{
    public function index(Request $request): View
    {
        $semesterAktif = Semester::aktif();
        $semesterId    = $request->get('semester_id', $semesterAktif?->id);
        $semua         = Semester::with('tahunAkademik')->orderByDesc('id')->get();

        $query = Kelas::with(['mataKuliah', 'dosen', 'semester.tahunAkademik'])
                      ->withCount('dosen');

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
        $semuaDosen = User::where('role', 'dosen')->orderBy('name')->get();

        return view('admin.pengampu.index', compact('kelas', 'semuaDosen', 'semua', 'semesterId', 'semesterAktif'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'kelas_id'       => ['required', 'exists:kelas,id'],
            'user_id'        => ['required', 'exists:users,id'],
            'is_koordinator' => ['boolean'],
        ]);

        $dosen = User::findOrFail($request->user_id);
        if ($dosen->role !== 'dosen') {
            return back()->with('error', 'User bukan dosen.');
        }

        // Jika jadikan koordinator, lepas koordinator sebelumnya
        if ($request->boolean('is_koordinator')) {
            Pengampu::where('kelas_id', $request->kelas_id)
                    ->update(['is_koordinator' => false]);
        }

        Pengampu::updateOrCreate(
            ['kelas_id' => $request->kelas_id, 'user_id' => $request->user_id],
            ['is_koordinator' => $request->boolean('is_koordinator')]
        );

        return back()->with('success', "Dosen {$dosen->name} berhasil ditetapkan sebagai pengampu.");
    }

    public function jadikanKoordinator(Pengampu $pengampu): RedirectResponse
    {
        // Lepas koordinator lama di kelas yang sama
        Pengampu::where('kelas_id', $pengampu->kelas_id)
                ->update(['is_koordinator' => false]);

        $pengampu->update(['is_koordinator' => true]);

        return back()->with('success', "{$pengampu->dosen->name} dijadikan koordinator.");
    }

    public function destroy(Pengampu $pengampu): RedirectResponse
    {
        $nama = $pengampu->dosen->name;
        $pengampu->delete();

        return back()->with('success', "{$nama} dihapus dari pengampu.");
    }
}
