<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Pengampu;
use App\Models\Pengumuman;
use Illuminate\Http\Request;

class PengumumanController extends Controller
{
    private function authorizeKelas(Kelas $kelas): void
    {
        abort_if(
            !Pengampu::where('kelas_id', $kelas->id)->where('user_id', auth()->id())->exists(),
            403
        );
    }

    public function index(Kelas $kelas)
    {
        $this->authorizeKelas($kelas);
        $kelas->load('mataKuliah', 'semester.tahunAkademik');

        $pengumuman = Pengumuman::where('kelas_id', $kelas->id)
            ->with('penulis')
            ->orderByDesc('is_pinned')
            ->latest()
            ->get();

        return view('dosen.pengumuman.index', compact('kelas', 'pengumuman'));
    }

    public function store(Request $request, Kelas $kelas)
    {
        $this->authorizeKelas($kelas);
        $data = $request->validate([
            'judul'     => 'required|string|max:200',
            'isi'       => 'required|string',
            'is_pinned' => 'nullable|boolean',
        ]);

        Pengumuman::create([
            'kelas_id'  => $kelas->id,
            'user_id'   => auth()->id(),
            'judul'     => $data['judul'],
            'isi'       => $data['isi'],
            'is_pinned' => $data['is_pinned'] ?? false,
        ]);

        return back()->with('success', 'Pengumuman berhasil diterbitkan.');
    }

    public function update(Request $request, Kelas $kelas, Pengumuman $pengumuman)
    {
        $this->authorizeKelas($kelas);
        abort_if($pengumuman->kelas_id !== $kelas->id, 404);

        $data = $request->validate([
            'judul'     => 'required|string|max:200',
            'isi'       => 'required|string',
            'is_pinned' => 'nullable|boolean',
        ]);

        $pengumuman->update([
            'judul'     => $data['judul'],
            'isi'       => $data['isi'],
            'is_pinned' => $data['is_pinned'] ?? false,
        ]);

        return back()->with('success', 'Pengumuman diperbarui.');
    }

    public function destroy(Kelas $kelas, Pengumuman $pengumuman)
    {
        $this->authorizeKelas($kelas);
        abort_if($pengumuman->kelas_id !== $kelas->id, 404);
        $pengumuman->delete();

        return back()->with('success', 'Pengumuman dihapus.');
    }
}
