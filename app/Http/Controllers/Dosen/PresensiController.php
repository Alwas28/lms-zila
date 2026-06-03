<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Pengampu;
use App\Models\Pertemuan;
use App\Models\Presensi;
use App\Models\PresensiSesi;
use Illuminate\Http\Request;

class PresensiController extends Controller
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

        $pertemuan = $kelas->pertemuan()
            ->with(['presensiSesi.presensi.mahasiswa'])
            ->withCount('presensiSesi')
            ->get();

        $mahasiswa = $kelas->mahasiswa()->get();

        return view('dosen.presensi.index', compact('kelas', 'pertemuan', 'mahasiswa'));
    }

    public function buka(Kelas $kelas, Pertemuan $pertemuan)
    {
        $this->authorizeKelas($kelas);
        abort_if($pertemuan->kelas_id !== $kelas->id, 404);

        // Tutup sesi sebelumnya jika masih aktif
        if ($pertemuan->presensiSesi) {
            $pertemuan->presensiSesi->update([
                'is_aktif'   => false,
                'ditutup_at' => now(),
            ]);
            $sesi = $pertemuan->presensiSesi;
            $sesi->kode = strtoupper(\Illuminate\Support\Str::random(6));
            $sesi->is_aktif = true;
            $sesi->dibuka_at = now();
            $sesi->ditutup_at = null;
            $sesi->save();
        } else {
            $sesi = PresensiSesi::buat($pertemuan->id);
        }

        // Init record Presensi 'alpha' untuk semua mahasiswa yang belum ada
        $mahasiswaIds = $kelas->mahasiswa()->pluck('users.id');
        foreach ($mahasiswaIds as $uid) {
            Presensi::firstOrCreate(
                ['presensi_sesi_id' => $sesi->id, 'user_id' => $uid],
                ['status' => 'alpha', 'waktu_masuk' => null]
            );
        }

        return back()->with('success', "Presensi dibuka. Kode: {$sesi->kode}");
    }

    public function tutup(Kelas $kelas, PresensiSesi $sesi)
    {
        $this->authorizeKelas($kelas);
        abort_if($sesi->pertemuan->kelas_id !== $kelas->id, 404);

        $sesi->update([
            'is_aktif'   => false,
            'ditutup_at' => now(),
        ]);

        return back()->with('success', 'Presensi ditutup.');
    }

    public function updateStatus(Request $request, Kelas $kelas, PresensiSesi $sesi)
    {
        $this->authorizeKelas($kelas);
        abort_if($sesi->pertemuan->kelas_id !== $kelas->id, 404);

        $data = $request->validate([
            'status'   => 'required|array',
            'status.*' => 'required|in:hadir,izin,sakit,alpha',
        ]);

        foreach ($data['status'] as $userId => $status) {
            Presensi::updateOrCreate(
                ['presensi_sesi_id' => $sesi->id, 'user_id' => $userId],
                ['status' => $status]
            );
        }

        return back()->with('success', 'Status presensi berhasil diperbarui.');
    }
}
