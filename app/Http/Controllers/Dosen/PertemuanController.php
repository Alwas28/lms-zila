<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Pengampu;
use App\Models\Pertemuan;
use Illuminate\Http\Request;

class PertemuanController extends Controller
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

        $pertemuan = $kelas->pertemuan()
            ->with(['presensiSesi', 'materi'])
            ->withCount('materi')
            ->get();

        return view('dosen.pertemuan.index', compact('kelas', 'pertemuan'));
    }

    public function store(Request $request, Kelas $kelas)
    {
        $this->authorizeKelas($kelas);

        $data = $request->validate([
            'nomor'    => 'required|integer|min:1|max:16',
            'topik'    => 'required|string|max:255',
            'deskripsi'=> 'nullable|string',
            'metode'   => 'required|string|max:100',
            'tanggal'  => 'required|date',
        ]);

        $data['kelas_id'] = $kelas->id;
        $data['status']   = 'draft';

        Pertemuan::create($data);

        return back()->with('success', 'Pertemuan berhasil ditambahkan.');
    }

    public function update(Request $request, Kelas $kelas, Pertemuan $pertemuan)
    {
        $this->authorizeKelas($kelas);
        abort_if($pertemuan->kelas_id !== $kelas->id, 404);

        $data = $request->validate([
            'nomor'    => 'required|integer|min:1|max:16',
            'topik'    => 'required|string|max:255',
            'deskripsi'=> 'nullable|string',
            'metode'   => 'required|string|max:100',
            'tanggal'  => 'required|date',
        ]);

        $pertemuan->update($data);

        return back()->with('success', 'Pertemuan berhasil diperbarui.');
    }

    public function destroy(Kelas $kelas, Pertemuan $pertemuan)
    {
        $this->authorizeKelas($kelas);
        abort_if($pertemuan->kelas_id !== $kelas->id, 404);

        $pertemuan->delete();

        return back()->with('success', 'Pertemuan berhasil dihapus.');
    }

    public function selesai(Kelas $kelas, Pertemuan $pertemuan)
    {
        $this->authorizeKelas($kelas);
        abort_if($pertemuan->kelas_id !== $kelas->id, 404);

        $pertemuan->update(['status' => 'selesai']);

        return back()->with('success', 'Pertemuan ditandai selesai.');
    }
}
