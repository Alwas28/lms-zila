<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\MataKuliah;
use App\Models\Penilaian;
use App\Models\Presensi;
use App\Models\PresensiSesi;
use App\Models\Semester;
use App\Models\Tugas;
use App\Models\TugasSubmission;
use App\Models\Ujian;
use App\Models\UjianSesi;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isMahasiswa()) {
            return $this->mahasiswaDashboard($user);
        }

        if ($user->isDosen()) {
            return $this->dosenDashboard($user);
        }

        return $this->adminDashboard();
    }

    private function mahasiswaDashboard($user)
    {
        $kelasIds = Kelas::whereHas('enrollments', fn($q) => $q->where('user_id', $user->id))
            ->pluck('id');

        // Jumlah mata kuliah
        $jumlahMK = $kelasIds->count();

        // Tugas belum dikumpulkan (deadline belum lewat)
        $tugasBelumDikumpulkan = Tugas::whereIn('kelas_id', $kelasIds)
            ->where(fn($q) => $q->whereNull('deadline')->orWhere('deadline', '>', now()))
            ->whereDoesntHave('submissions', fn($q) => $q->where('user_id', $user->id))
            ->count();

        // Ujian mendatang (aktif, belum selesai)
        $ujianMendatang = Ujian::whereIn('kelas_id', $kelasIds)
            ->where('status', 'aktif')
            ->where(fn($q) => $q->whereNull('selesai_at')->orWhere('selesai_at', '>', now()))
            ->whereDoesntHave('sesi', fn($q) => $q->where('user_id', $user->id)->where('is_selesai', true))
            ->count();

        // Rata-rata nilai akhir
        $rataRataNilai = Penilaian::whereIn('kelas_id', $kelasIds)
            ->where('user_id', $user->id)
            ->whereNotNull('nilai_akhir')
            ->avg('nilai_akhir');

        // Kelas yang diikuti (untuk card list)
        $kelasEnrolled = Kelas::whereHas('enrollments', fn($q) => $q->where('user_id', $user->id))
            ->with(['mataKuliah', 'semester.tahunAkademik', 'dosen'])
            ->withCount('pertemuan')
            ->get();

        // Tugas mendatang (deadline belum lewat, belum dikumpulkan)
        $tugasMendatang = Tugas::whereIn('kelas_id', $kelasIds)
            ->with(['kelas.mataKuliah', 'submissions' => fn($q) => $q->where('user_id', $user->id)])
            ->where(fn($q) => $q->whereNull('deadline')->orWhere('deadline', '>', now()))
            ->whereDoesntHave('submissions', fn($q) => $q->where('user_id', $user->id))
            ->orderBy('deadline')
            ->limit(4)
            ->get();

        // Ujian mendatang (list)
        $ujianMendatangList = Ujian::whereIn('kelas_id', $kelasIds)
            ->where('status', 'aktif')
            ->where(fn($q) => $q->whereNull('selesai_at')->orWhere('selesai_at', '>', now()))
            ->with('kelas.mataKuliah')
            ->orderBy('mulai_at')
            ->limit(3)
            ->get();

        // Penilaian per kelas
        $penilaianList = Penilaian::whereIn('kelas_id', $kelasIds)
            ->where('user_id', $user->id)
            ->with('kelas.mataKuliah')
            ->get()
            ->keyBy('kelas_id');

        return view('dashboard', compact(
            'jumlahMK', 'tugasBelumDikumpulkan', 'ujianMendatang', 'rataRataNilai',
            'kelasEnrolled', 'tugasMendatang', 'ujianMendatangList', 'penilaianList'
        ));
    }

    private function dosenDashboard($user)
    {
        $kelasIds = Kelas::whereHas('pengampu', fn($q) => $q->where('user_id', $user->id))
            ->pluck('id');

        $jumlahKelas = $kelasIds->count();

        $tugasPerluDinilai = TugasSubmission::whereHas('tugas', fn($q) => $q->whereIn('kelas_id', $kelasIds))
            ->whereNull('nilai')
            ->count();

        $ujianAktif = Ujian::whereIn('kelas_id', $kelasIds)
            ->where('status', 'aktif')
            ->count();

        $kelasList = Kelas::whereIn('id', $kelasIds)
            ->with(['mataKuliah', 'semester.tahunAkademik'])
            ->withCount(['mahasiswa', 'pertemuan'])
            ->get();

        return view('dashboard', compact('jumlahKelas', 'tugasPerluDinilai', 'ujianAktif', 'kelasList'));
    }

    private function adminDashboard()
    {
        $jumlahMK        = MataKuliah::where('is_arsip', false)->count();
        $jumlahKelas     = Kelas::where('is_aktif', true)->count();
        $jumlahDosen     = User::where('role', 'dosen')->count();
        $jumlahMahasiswa = User::where('role', 'mahasiswa')->count();

        $semesterAktif = Semester::where('is_aktif', true)->with('tahunAkademik')->first();

        $kelasTerbaru = Kelas::where('is_aktif', true)
            ->with(['mataKuliah', 'semester.tahunAkademik'])
            ->withCount('mahasiswa')
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'jumlahMK', 'jumlahKelas', 'jumlahDosen', 'jumlahMahasiswa',
            'semesterAktif', 'kelasTerbaru'
        ));
    }
}
