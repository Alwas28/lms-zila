<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Kelas;
use App\Models\Tugas;
use App\Models\TugasSubmission;
use Illuminate\Http\Request;

class TugasController extends Controller
{
    private function authorizeKelas(Kelas $kelas): void
    {
        abort_if(!Enrollment::where('kelas_id', $kelas->id)->where('user_id', auth()->id())->exists(), 403);
    }

    public function index(Kelas $kelas)
    {
        $this->authorizeKelas($kelas);
        $kelas->load('mataKuliah', 'semester.tahunAkademik');

        $tugas = Tugas::where('kelas_id', $kelas->id)
            ->with(['submissions' => fn($q) => $q->where('user_id', auth()->id())])
            ->latest()
            ->get();

        return view('mahasiswa.tugas.index', compact('kelas', 'tugas'));
    }

    public function show(Kelas $kelas, Tugas $tugas)
    {
        $this->authorizeKelas($kelas);
        abort_if($tugas->kelas_id !== $kelas->id, 404);
        $kelas->load('mataKuliah', 'semester.tahunAkademik');

        $submission = TugasSubmission::where('tugas_id', $tugas->id)
            ->where('user_id', auth()->id())
            ->first();

        return view('mahasiswa.tugas.show', compact('kelas', 'tugas', 'submission'));
    }

    public function submit(Request $request, Kelas $kelas, Tugas $tugas)
    {
        $this->authorizeKelas($kelas);
        abort_if($tugas->kelas_id !== $kelas->id, 404);
        abort_if($tugas->is_expired, 422, 'Batas waktu pengumpulan sudah lewat.');

        $existing = TugasSubmission::where('tugas_id', $tugas->id)
            ->where('user_id', auth()->id())
            ->first();
        abort_if($existing !== null, 422, 'Anda sudah mengumpulkan tugas ini.');

        $request->validate(['file' => 'required|file|max:20480', 'catatan' => 'nullable|string|max:1000']);

        $path = $request->file('file')->store('submissions/' . $tugas->id, 'public');

        TugasSubmission::create([
            'tugas_id'     => $tugas->id,
            'user_id'      => auth()->id(),
            'file_path'    => $path,
            'catatan'      => $request->catatan,
            'submitted_at' => now(),
        ]);

        return back()->with('success', 'Tugas berhasil dikumpulkan.');
    }

    public function cancelSubmit(Kelas $kelas, Tugas $tugas)
    {
        $this->authorizeKelas($kelas);
        abort_if($tugas->kelas_id !== $kelas->id, 404);
        abort_if($tugas->is_expired, 422, 'Tidak dapat membatalkan setelah batas waktu.');

        TugasSubmission::where('tugas_id', $tugas->id)
            ->where('user_id', auth()->id())
            ->whereNull('nilai')
            ->delete();

        return back()->with('success', 'Pengumpulan dibatalkan.');
    }
}
