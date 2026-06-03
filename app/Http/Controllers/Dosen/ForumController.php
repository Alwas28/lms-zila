<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\ForumKomentar;
use App\Models\ForumTopik;
use App\Models\Kelas;
use App\Models\Pengampu;
use Illuminate\Http\Request;

class ForumController extends Controller
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

        $topik = ForumTopik::where('kelas_id', $kelas->id)
            ->with(['penulis', 'komentar'])
            ->orderByDesc('is_pinned')
            ->latest()
            ->get();

        return view('dosen.forum.index', compact('kelas', 'topik'));
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

        return view('dosen.forum.show', compact('kelas', 'topik'));
    }

    public function destroy(Kelas $kelas, ForumTopik $topik)
    {
        $this->authorizeKelas($kelas);
        abort_if($topik->kelas_id !== $kelas->id, 404);
        $topik->delete();

        return back()->with('success', 'Topik dihapus.');
    }

    public function toggleLock(Kelas $kelas, ForumTopik $topik)
    {
        $this->authorizeKelas($kelas);
        abort_if($topik->kelas_id !== $kelas->id, 404);
        $topik->update(['is_locked' => !$topik->is_locked]);

        return back()->with('success', $topik->is_locked ? 'Topik dikunci.' : 'Topik dibuka kembali.');
    }

    public function togglePin(Kelas $kelas, ForumTopik $topik)
    {
        $this->authorizeKelas($kelas);
        abort_if($topik->kelas_id !== $kelas->id, 404);
        $topik->update(['is_pinned' => !$topik->is_pinned]);

        return back()->with('success', $topik->is_pinned ? 'Topik disematkan.' : 'Sematan dilepas.');
    }

    public function storeKomentar(Request $request, Kelas $kelas, ForumTopik $topik)
    {
        $this->authorizeKelas($kelas);
        abort_if($topik->kelas_id !== $kelas->id, 404);
        abort_if($topik->is_locked, 403, 'Topik ini sudah dikunci.');

        $request->validate(['isi' => 'required|string']);

        ForumKomentar::create([
            'forum_topik_id' => $topik->id,
            'user_id'        => auth()->id(),
            'isi'            => $request->isi,
        ]);

        return back()->with('success', 'Komentar ditambahkan.');
    }

    public function destroyKomentar(Kelas $kelas, ForumTopik $topik, ForumKomentar $komentar)
    {
        $this->authorizeKelas($kelas);
        abort_if($topik->kelas_id !== $kelas->id, 404);
        $komentar->delete();

        return back()->with('success', 'Komentar dihapus.');
    }
}
