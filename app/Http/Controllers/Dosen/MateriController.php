<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Materi;
use App\Models\Pengampu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MateriController extends Controller
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

        $pertemuan = $kelas->pertemuan()->with('materi')->get();

        return view('dosen.materi.index', compact('kelas', 'pertemuan'));
    }

    public function store(Request $request, Kelas $kelas)
    {
        $this->authorizeKelas($kelas);

        $tipeFile = ['pdf', 'ppt', 'video', 'audio', 'dokumen', 'lainnya'];
        $tipeUrl  = ['youtube', 'website'];

        $data = $request->validate([
            'pertemuan_id' => 'required|integer|exists:pertemuan,id',
            'judul'        => 'required|string|max:255',
            'tipe'         => 'required|string|in:pdf,ppt,video,audio,youtube,website,dokumen,lainnya',
            'url'          => 'nullable|url|required_if:tipe,youtube|required_if:tipe,website',
            'file'         => 'nullable|file|max:51200|required_if:tipe,pdf|required_if:tipe,ppt|required_if:tipe,video|required_if:tipe,audio|required_if:tipe,dokumen',
            'is_wajib'     => 'nullable|boolean',
            'urutan'       => 'nullable|integer|min:1',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')
                ->store("materi/{$kelas->id}", 'public');
        }

        Materi::create([
            'pertemuan_id' => $data['pertemuan_id'],
            'judul'        => $data['judul'],
            'tipe'         => $data['tipe'],
            'file_path'    => $filePath,
            'url'          => $data['url'] ?? null,
            'is_wajib'     => $request->boolean('is_wajib'),
            'urutan'       => $data['urutan'] ?? 1,
        ]);

        return back()->with('success', 'Materi berhasil diunggah.');
    }

    public function destroy(Kelas $kelas, Materi $materi)
    {
        $this->authorizeKelas($kelas);
        abort_if($materi->pertemuan->kelas_id !== $kelas->id, 404);

        if ($materi->file_path) {
            Storage::disk('public')->delete($materi->file_path);
        }

        $materi->delete();

        return back()->with('success', 'Materi berhasil dihapus.');
    }
}
