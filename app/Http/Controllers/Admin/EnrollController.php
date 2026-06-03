<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Kelas;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EnrollController extends Controller
{
    public function index(Request $request): View
    {
        $semesterAktif = Semester::aktif();
        $semesterId    = $request->get('semester_id', $semesterAktif?->id);
        $kelasId       = $request->get('kelas_id');
        $semua         = Semester::with('tahunAkademik')->orderByDesc('id')->get();

        // Daftar kelas untuk filter
        $semuaKelas = Kelas::with('mataKuliah')
                           ->when($semesterId, fn($q) => $q->where('semester_id', $semesterId))
                           ->orderBy('nama_kelas')
                           ->get();

        // Peserta kelas yang dipilih
        $peserta    = collect();
        $kelasAktif = null;

        if ($kelasId) {
            $kelasAktif = Kelas::with(['mataKuliah', 'semester.tahunAkademik'])->findOrFail($kelasId);
            $query      = $kelasAktif->mahasiswa()->orderBy('name');

            if ($request->filled('search')) {
                $q = $request->search;
                $query->where(fn($qb) =>
                    $qb->where('name', 'like', "%{$q}%")
                       ->orWhere('nip_nim', 'like', "%{$q}%")
                );
            }

            $peserta = $query->paginate(20)->withQueryString();
        }

        // Mahasiswa yang belum terdaftar di kelas aktif (untuk dropdown tambah)
        $mahasiswaBelumEnroll = collect();
        if ($kelasAktif) {
            $sudahEnroll = $kelasAktif->mahasiswa()->pluck('users.id');
            $mahasiswaBelumEnroll = User::where('role', 'mahasiswa')
                                        ->whereNotIn('id', $sudahEnroll)
                                        ->orderBy('name')
                                        ->get();
        }

        return view('admin.enroll.index', compact(
            'semua', 'semesterId', 'semesterAktif',
            'semuaKelas', 'kelasId', 'kelasAktif',
            'peserta', 'mahasiswaBelumEnroll'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'kelas_id' => ['required', 'exists:kelas,id'],
            'user_id'  => ['required', 'exists:users,id'],
        ]);

        $kelas    = Kelas::findOrFail($request->kelas_id);
        $mhs      = User::findOrFail($request->user_id);

        if ($mhs->role !== 'mahasiswa') {
            return back()->with('error', 'User bukan mahasiswa.');
        }

        if ($kelas->is_penuh) {
            return back()->with('error', "Kelas {$kelas->nama_lengkap} sudah penuh (kapasitas: {$kelas->kapasitas}).");
        }

        $sudah = Enrollment::where(['kelas_id' => $request->kelas_id, 'user_id' => $request->user_id])->exists();
        if ($sudah) {
            return back()->with('error', "{$mhs->name} sudah terdaftar di kelas ini.");
        }

        Enrollment::create(['kelas_id' => $request->kelas_id, 'user_id' => $request->user_id]);

        return back()->with('success', "{$mhs->name} berhasil didaftarkan ke {$kelas->nama_lengkap}.");
    }

    public function pindah(Request $request, Enrollment $enrollment): RedirectResponse
    {
        $request->validate([
            'kelas_tujuan_id' => ['required', 'exists:kelas,id', 'different:' . $enrollment->kelas_id],
        ]);

        $kelasTujuan = Kelas::findOrFail($request->kelas_tujuan_id);

        if ($kelasTujuan->is_penuh) {
            return back()->with('error', "Kelas tujuan sudah penuh.");
        }

        $sudah = Enrollment::where(['kelas_id' => $request->kelas_tujuan_id, 'user_id' => $enrollment->user_id])->exists();
        if ($sudah) {
            return back()->with('error', "Mahasiswa sudah terdaftar di kelas tujuan.");
        }

        $enrollment->update(['kelas_id' => $request->kelas_tujuan_id]);

        return back()->with('success', "Mahasiswa berhasil dipindahkan ke {$kelasTujuan->nama_lengkap}.");
    }

    public function destroy(Enrollment $enrollment): RedirectResponse
    {
        $nama = $enrollment->mahasiswa->name;
        $enrollment->delete();

        return back()->with('success', "{$nama} dihapus dari kelas.");
    }
}
