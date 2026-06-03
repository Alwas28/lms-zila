<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Pengampu;
use App\Models\Tugas;
use App\Models\TugasSubmission;
use Illuminate\Http\Request;

class TugasController extends Controller
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

        $tugas = $kelas->tugas()
            ->with('pertemuan')
            ->withCount('submissions')
            ->orderBy('deadline')
            ->paginate(20);

        $totalMahasiswa = $kelas->mahasiswa()->count();

        $pertemuan = $kelas->pertemuan()->get(['id', 'nomor', 'topik']);

        return view('dosen.tugas.index', compact('kelas', 'tugas', 'totalMahasiswa', 'pertemuan'));
    }

    public function store(Request $request, Kelas $kelas)
    {
        $this->authorizeKelas($kelas);

        $data = $request->validate([
            'judul'       => 'required|string|max:255',
            'deskripsi'   => 'nullable|string',
            'tipe'        => 'required|in:individu,kelompok',
            'pertemuan_id'=> 'nullable|integer|exists:pertemuan,id',
            'deadline'    => 'required|date',
            'nilai_maks'  => 'required|numeric|min:1|max:1000',
        ]);

        $data['kelas_id'] = $kelas->id;

        Tugas::create($data);

        return back()->with('success', 'Tugas berhasil dibuat.');
    }

    public function show(Kelas $kelas, Tugas $tugas)
    {
        $this->authorizeKelas($kelas);
        abort_if($tugas->kelas_id !== $kelas->id, 404);

        $kelas->load('mataKuliah');

        $submissions = $tugas->submissions()->with('mahasiswa')->get();

        $submittedIds = $submissions->pluck('user_id');

        $belumKumpul = $kelas->mahasiswa()
            ->whereNotIn('users.id', $submittedIds)
            ->get();

        $rataRata = $submissions->whereNotNull('nilai')->avg('nilai');

        return view('dosen.tugas.show', compact('kelas', 'tugas', 'submissions', 'belumKumpul', 'rataRata'));
    }

    public function nilaiSubmission(Request $request, Kelas $kelas, Tugas $tugas, TugasSubmission $submission)
    {
        $this->authorizeKelas($kelas);
        abort_if($tugas->kelas_id !== $kelas->id, 404);
        abort_if($submission->tugas_id !== $tugas->id, 404);

        $data = $request->validate([
            'nilai'    => "required|numeric|min:0|max:{$tugas->nilai_maks}",
            'feedback' => 'nullable|string',
        ]);

        $submission->update($data);

        return back()->with('success', 'Nilai berhasil disimpan.');
    }

    public function destroy(Kelas $kelas, Tugas $tugas)
    {
        $this->authorizeKelas($kelas);
        abort_if($tugas->kelas_id !== $kelas->id, 404);

        $tugas->delete();

        return back()->with('success', 'Tugas berhasil dihapus.');
    }
}
