<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\ForumKomentar;
use App\Models\ForumTopik;
use App\Models\Kelas;
use Illuminate\Http\Request;

class ForumController extends Controller
{
    private function authorizeKelas(Kelas $kelas): void
    {
        abort_if(
            !Enrollment::where('kelas_id', $kelas->id)->where('user_id', auth()->id())->exists(),
            403
        );
    }

    public function index(Kelas $kelas)
    {
        $this->authorizeKelas($kelas);
        $kelas->load('mataKuliah', 'semester.tahunAkademik');

        $topik = ForumTopik::where('kelas_id', $kelas->id)
            ->with(['penulis', 'komentar'])
            ->orderByDesc('is_pinned')
            ->latest()
            ->get();

        return view('mahasiswa.forum.index', compact('kelas', 'topik'));
    }

    public function store(Request $request, Kelas $kelas)
    {
        $this->authorizeKelas($kelas);
        $data = $request->validate([
            'judul' => 'required|string|max:200',
            'isi'   => 'required|string',
        ]);

        ForumTopik::create([
            'kelas_id' => $kelas->id,
            'user_id'  => auth()->id(),
            ...$data,
        ]);

        return back()->with('success', 'Topik berhasil dibuat.');
    }

    public function show(Kelas $kelas, ForumTopik $topik)
    {
        $this->authorizeKelas($kelas);
        abort_if($topik->kelas_id !== $kelas->id, 404);

        $kelas->load('mataKuliah', 'semester.tahunAkademik');
        $topik->load(['penulis', 'komentar.penulis']);

        return view('mahasiswa.forum.show', compact('kelas', 'topik'));
    }

    public function storeKomentar(Request $request, Kelas $kelas, ForumTopik $topik)
    {
        $this->authorizeKelas($kelas);
        abort_if($topik->kelas_id !== $kelas->id, 404);
        abort_if($topik->is_locked, 403, 'Topik ini sudah dikunci oleh dosen.');

        $request->validate(['isi' => 'required|string']);

        ForumKomentar::create([
            'forum_topik_id' => $topik->id,
            'user_id'        => auth()->id(),
            'isi'            => $request->isi,
        ]);

        return back()->with('success', 'Balasan ditambahkan.');
    }

    public function destroyKomentar(Kelas $kelas, ForumTopik $topik, ForumKomentar $komentar)
    {
        $this->authorizeKelas($kelas);
        abort_if($topik->kelas_id !== $kelas->id, 404);
        abort_if($komentar->user_id !== auth()->id(), 403);
        $komentar->delete();

        return back()->with('success', 'Komentar dihapus.');
    }
}
