<?php

use App\Http\Controllers\Admin\EnrollController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\MataKuliahController;
use App\Http\Controllers\Admin\PengampuController;
use App\Http\Controllers\Admin\DosenController;
use App\Http\Controllers\Admin\MahasiswaController;
use App\Http\Controllers\Admin\SemesterController;
use App\Http\Controllers\Admin\TahunAkademikController;
use App\Http\Controllers\Dosen\MataKuliahController as DosenMataKuliahController;
use App\Http\Controllers\Dosen\PertemuanController as DosenPertemuanController;
use App\Http\Controllers\Dosen\MateriController as DosenMateriController;
use App\Http\Controllers\Dosen\RpsController as DosenRpsController;
use App\Http\Controllers\Dosen\TugasController as DosenTugasController;
use App\Http\Controllers\Dosen\PresensiController as DosenPresensiController;
use App\Http\Controllers\Dosen\PenilaianController as DosenPenilaianController;
use App\Http\Controllers\Dosen\BankSoalController as DosenBankSoalController;
use App\Http\Controllers\Dosen\UjianController as DosenUjianController;
use App\Http\Controllers\Dosen\CapaianCpmkController as DosenCapaianCpmkController;
use App\Http\Controllers\Dosen\LaporanController as DosenLaporanController;
use App\Http\Controllers\Dosen\ForumController as DosenForumController;
use App\Http\Controllers\Dosen\PengumumanController as DosenPengumumanController;
use App\Http\Controllers\Mahasiswa\KelasController as MahasiswaKelasController;
use App\Http\Controllers\Mahasiswa\ForumController as MahasiswaForumController;
use App\Http\Controllers\Mahasiswa\PengumumanController as MahasiswaPengumumanController;
use App\Http\Controllers\Mahasiswa\MateriController as MahasiswaMateriController;
use App\Http\Controllers\Mahasiswa\TugasController as MahasiswaTugasController;
use App\Http\Controllers\Mahasiswa\PresensiController as MahasiswaPresensiController;
use App\Http\Controllers\Mahasiswa\NilaiController as MahasiswaNilaiController;
use App\Http\Controllers\Mahasiswa\UjianController as MahasiswaUjianController;
use App\Http\Controllers\Mahasiswa\RpsController as MahasiswaRpsController;
use App\Http\Controllers\Mahasiswa\CapaianCpmkController as MahasiswaCapaianCpmkController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ── Admin Routes ──────────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {

    // Tahun Akademik
    Route::get('/tahun-akademik', [TahunAkademikController::class, 'index'])->name('tahun-akademik.index');
    Route::post('/tahun-akademik', [TahunAkademikController::class, 'store'])->name('tahun-akademik.store');
    Route::delete('/tahun-akademik/{tahunAkademik}', [TahunAkademikController::class, 'destroy'])->name('tahun-akademik.destroy');
    Route::patch('/tahun-akademik/{tahunAkademik}/aktifkan', [TahunAkademikController::class, 'aktifkan'])->name('tahun-akademik.aktifkan');
    Route::patch('/tahun-akademik/{tahunAkademik}/nonaktifkan', [TahunAkademikController::class, 'nonaktifkan'])->name('tahun-akademik.nonaktifkan');

    // Mata Kuliah
    Route::get('/mata-kuliah', [MataKuliahController::class, 'index'])->name('mata-kuliah.index');
    Route::post('/mata-kuliah', [MataKuliahController::class, 'store'])->name('mata-kuliah.store');
    Route::patch('/mata-kuliah/{mataKuliah}', [MataKuliahController::class, 'update'])->name('mata-kuliah.update');
    Route::patch('/mata-kuliah/{mataKuliah}/arsipkan', [MataKuliahController::class, 'arsipkan'])->name('mata-kuliah.arsipkan');
    Route::patch('/mata-kuliah/{mataKuliah}/aktifkan', [MataKuliahController::class, 'aktifkan'])->name('mata-kuliah.aktifkan');
    Route::delete('/mata-kuliah/{mataKuliah}', [MataKuliahController::class, 'destroy'])->name('mata-kuliah.destroy');

    // Kelas
    Route::get('/kelas', [KelasController::class, 'index'])->name('kelas.index');
    Route::post('/kelas', [KelasController::class, 'store'])->name('kelas.store');
    Route::patch('/kelas/{kelas}', [KelasController::class, 'update'])->name('kelas.update');
    Route::patch('/kelas/{kelas}/tutup', [KelasController::class, 'tutup'])->name('kelas.tutup');
    Route::patch('/kelas/{kelas}/buka', [KelasController::class, 'buka'])->name('kelas.buka');
    Route::post('/kelas/salin', [KelasController::class, 'salin'])->name('kelas.salin');
    Route::delete('/kelas/{kelas}', [KelasController::class, 'destroy'])->name('kelas.destroy');

    // Pengampu
    Route::get('/pengampu', [PengampuController::class, 'index'])->name('pengampu.index');
    Route::post('/pengampu', [PengampuController::class, 'store'])->name('pengampu.store');
    Route::patch('/pengampu/{pengampu}/koordinator', [PengampuController::class, 'jadikanKoordinator'])->name('pengampu.koordinator');
    Route::delete('/pengampu/{pengampu}', [PengampuController::class, 'destroy'])->name('pengampu.destroy');

    // Enroll Mahasiswa
    Route::get('/enroll', [EnrollController::class, 'index'])->name('enroll.index');
    Route::post('/enroll', [EnrollController::class, 'store'])->name('enroll.store');
    Route::patch('/enroll/{enrollment}/pindah', [EnrollController::class, 'pindah'])->name('enroll.pindah');
    Route::delete('/enroll/{enrollment}', [EnrollController::class, 'destroy'])->name('enroll.destroy');

    // Dosen
    Route::get('/dosen', [DosenController::class, 'index'])->name('dosen.index');
    Route::post('/dosen', [DosenController::class, 'store'])->name('dosen.store');
    Route::patch('/dosen/{dosen}', [DosenController::class, 'update'])->name('dosen.update');
    Route::delete('/dosen/{dosen}', [DosenController::class, 'destroy'])->name('dosen.destroy');
    Route::patch('/dosen/{dosen}/reset-password', [DosenController::class, 'resetPassword'])->name('dosen.reset-password');

    // Mahasiswa
    Route::get('/mahasiswa', [MahasiswaController::class, 'index'])->name('mahasiswa.index');
    Route::post('/mahasiswa', [MahasiswaController::class, 'store'])->name('mahasiswa.store');
    Route::patch('/mahasiswa/{mahasiswa}', [MahasiswaController::class, 'update'])->name('mahasiswa.update');
    Route::delete('/mahasiswa/{mahasiswa}', [MahasiswaController::class, 'destroy'])->name('mahasiswa.destroy');
    Route::patch('/mahasiswa/{mahasiswa}/reset-password', [MahasiswaController::class, 'resetPassword'])->name('mahasiswa.reset-password');

    // Semester
    Route::patch('/semester/{semester}', [SemesterController::class, 'update'])->name('semester.update');
    Route::patch('/semester/{semester}/aktifkan', [SemesterController::class, 'aktifkan'])->name('semester.aktifkan');
    Route::patch('/semester/{semester}/nonaktifkan', [SemesterController::class, 'nonaktifkan'])->name('semester.nonaktifkan');
});

// ── Dosen Routes ─────────────────────────────────────────────────────────────
Route::middleware('auth')->prefix('dosen')->name('dosen.')->group(function () {

    // Mata Kuliah
    Route::get('/mata-kuliah', [DosenMataKuliahController::class, 'index'])->name('mata-kuliah.index');

    // Pertemuan
    Route::get('/kelas/{kelas}/pertemuan', [DosenPertemuanController::class, 'index'])->name('pertemuan.index');
    Route::post('/kelas/{kelas}/pertemuan', [DosenPertemuanController::class, 'store'])->name('pertemuan.store');
    Route::patch('/kelas/{kelas}/pertemuan/{pertemuan}', [DosenPertemuanController::class, 'update'])->name('pertemuan.update');
    Route::patch('/kelas/{kelas}/pertemuan/{pertemuan}/selesai', [DosenPertemuanController::class, 'selesai'])->name('pertemuan.selesai');
    Route::delete('/kelas/{kelas}/pertemuan/{pertemuan}', [DosenPertemuanController::class, 'destroy'])->name('pertemuan.destroy');

    // Materi
    Route::get('/kelas/{kelas}/materi', [DosenMateriController::class, 'index'])->name('materi.index');
    Route::post('/kelas/{kelas}/materi', [DosenMateriController::class, 'store'])->name('materi.store');
    Route::delete('/kelas/{kelas}/materi/{materi}', [DosenMateriController::class, 'destroy'])->name('materi.destroy');

    // RPS
    Route::get('/kelas/{kelas}/rps', [DosenRpsController::class, 'show'])->name('rps.show');
    Route::patch('/kelas/{kelas}/rps', [DosenRpsController::class, 'update'])->name('rps.update');

    // Tugas
    Route::get('/kelas/{kelas}/tugas', [DosenTugasController::class, 'index'])->name('tugas.index');
    Route::post('/kelas/{kelas}/tugas', [DosenTugasController::class, 'store'])->name('tugas.store');
    Route::get('/kelas/{kelas}/tugas/{tugas}', [DosenTugasController::class, 'show'])->name('tugas.show');
    Route::patch('/kelas/{kelas}/tugas/{tugas}/nilai/{submission}', [DosenTugasController::class, 'nilaiSubmission'])->name('tugas.nilai');
    Route::delete('/kelas/{kelas}/tugas/{tugas}', [DosenTugasController::class, 'destroy'])->name('tugas.destroy');

    // Presensi
    Route::get('/kelas/{kelas}/presensi', [DosenPresensiController::class, 'index'])->name('presensi.index');
    Route::post('/kelas/{kelas}/presensi/{pertemuan}/buka', [DosenPresensiController::class, 'buka'])->name('presensi.buka');
    Route::patch('/kelas/{kelas}/presensi/{sesi}/tutup', [DosenPresensiController::class, 'tutup'])->name('presensi.tutup');
    Route::patch('/kelas/{kelas}/presensi/{sesi}/status', [DosenPresensiController::class, 'updateStatus'])->name('presensi.status');

    // Penilaian
    Route::get('/kelas/{kelas}/penilaian', [DosenPenilaianController::class, 'index'])->name('penilaian.index');
    Route::patch('/kelas/{kelas}/penilaian/config', [DosenPenilaianController::class, 'updateConfig'])->name('penilaian.config');
    Route::post('/kelas/{kelas}/penilaian/simpan', [DosenPenilaianController::class, 'simpan'])->name('penilaian.simpan');

    // Ujian (Quiz / UTS / UAS)
    Route::get('/kelas/{kelas}/ujian', [DosenUjianController::class, 'index'])->name('ujian.index');
    Route::post('/kelas/{kelas}/ujian', [DosenUjianController::class, 'store'])->name('ujian.store');
    Route::patch('/kelas/{kelas}/ujian/{ujian}', [DosenUjianController::class, 'update'])->name('ujian.update');
    Route::delete('/kelas/{kelas}/ujian/{ujian}', [DosenUjianController::class, 'destroy'])->name('ujian.destroy');
    Route::patch('/kelas/{kelas}/ujian/{ujian}/aktifkan', [DosenUjianController::class, 'aktifkan'])->name('ujian.aktifkan');
    Route::patch('/kelas/{kelas}/ujian/{ujian}/tutup', [DosenUjianController::class, 'tutup'])->name('ujian.tutup');
    Route::patch('/kelas/{kelas}/ujian/{ujian}/draftkan', [DosenUjianController::class, 'draftkan'])->name('ujian.draftkan');
    Route::post('/kelas/{kelas}/ujian/{ujian}/soal', [DosenUjianController::class, 'tambahSoal'])->name('ujian.tambah-soal');
    Route::post('/kelas/{kelas}/ujian/{ujian}/soal/random', [DosenUjianController::class, 'randomSoal'])->name('ujian.random-soal');
    Route::delete('/kelas/{kelas}/ujian/{ujian}/soal/{ujianSoal}', [DosenUjianController::class, 'hapusSoal'])->name('ujian.hapus-soal');
    Route::delete('/kelas/{kelas}/ujian/{ujian}/soal', [DosenUjianController::class, 'hapusSemuaSoal'])->name('ujian.hapus-semua-soal');

    // Capaian CPMK
    Route::get('/kelas/{kelas}/capaian-cpmk', [DosenCapaianCpmkController::class, 'index'])->name('capaian-cpmk.index');
    Route::patch('/kelas/{kelas}/capaian-cpmk', [DosenCapaianCpmkController::class, 'update'])->name('capaian-cpmk.update');

    // Laporan
    Route::get('/kelas/{kelas}/laporan',              [DosenLaporanController::class, 'index'])->name('laporan.index');
    Route::get('/kelas/{kelas}/laporan/nilai/pdf',    [DosenLaporanController::class, 'nilaiPdf'])->name('laporan.nilai-pdf');
    Route::get('/kelas/{kelas}/laporan/nilai/csv',    [DosenLaporanController::class, 'nilaiCsv'])->name('laporan.nilai-csv');
    Route::get('/kelas/{kelas}/laporan/presensi/pdf', [DosenLaporanController::class, 'presensiPdf'])->name('laporan.presensi-pdf');
    Route::get('/kelas/{kelas}/laporan/presensi/csv', [DosenLaporanController::class, 'presensiCsv'])->name('laporan.presensi-csv');
    Route::get('/kelas/{kelas}/laporan/capaian/pdf',  [DosenLaporanController::class, 'capaianPdf'])->name('laporan.capaian-pdf');
    Route::get('/kelas/{kelas}/laporan/capaian/csv',  [DosenLaporanController::class, 'capaianCsv'])->name('laporan.capaian-csv');

    // Forum Diskusi (Dosen)
    Route::get('/kelas/{kelas}/forum',                                              [DosenForumController::class, 'index'])->name('forum.index');
    Route::post('/kelas/{kelas}/forum',                                             [DosenForumController::class, 'store'])->name('forum.store');
    Route::get('/kelas/{kelas}/forum/{topik}',                                      [DosenForumController::class, 'show'])->name('forum.show');
    Route::delete('/kelas/{kelas}/forum/{topik}',                                   [DosenForumController::class, 'destroy'])->name('forum.destroy');
    Route::patch('/kelas/{kelas}/forum/{topik}/lock',                               [DosenForumController::class, 'toggleLock'])->name('forum.lock');
    Route::patch('/kelas/{kelas}/forum/{topik}/pin',                                [DosenForumController::class, 'togglePin'])->name('forum.pin');
    Route::post('/kelas/{kelas}/forum/{topik}/komentar',                            [DosenForumController::class, 'storeKomentar'])->name('forum.komentar.store');
    Route::delete('/kelas/{kelas}/forum/{topik}/komentar/{komentar}',               [DosenForumController::class, 'destroyKomentar'])->name('forum.komentar.destroy');

    // Pengumuman (Dosen)
    Route::get('/kelas/{kelas}/pengumuman',                                         [DosenPengumumanController::class, 'index'])->name('pengumuman.index');
    Route::post('/kelas/{kelas}/pengumuman',                                        [DosenPengumumanController::class, 'store'])->name('pengumuman.store');
    Route::patch('/kelas/{kelas}/pengumuman/{pengumuman}',                          [DosenPengumumanController::class, 'update'])->name('pengumuman.update');
    Route::delete('/kelas/{kelas}/pengumuman/{pengumuman}',                         [DosenPengumumanController::class, 'destroy'])->name('pengumuman.destroy');

    // Bank Soal
    Route::get('/bank-soal', [DosenBankSoalController::class, 'index'])->name('bank-soal.index');
    Route::post('/bank-soal', [DosenBankSoalController::class, 'store'])->name('bank-soal.store');
    Route::patch('/bank-soal/{bankSoal}', [DosenBankSoalController::class, 'update'])->name('bank-soal.update');
    Route::delete('/bank-soal/{bankSoal}', [DosenBankSoalController::class, 'destroy'])->name('bank-soal.destroy');
});

// ── Mahasiswa Routes ──────────────────────────────────────────────────────────
Route::middleware('auth')->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::get('/kelas', [MahasiswaKelasController::class, 'index'])->name('kelas.index');

    // Forum Diskusi (Mahasiswa)
    Route::get('/kelas/{kelas}/forum',                                              [MahasiswaForumController::class, 'index'])->name('forum.index');
    Route::post('/kelas/{kelas}/forum',                                             [MahasiswaForumController::class, 'store'])->name('forum.store');
    Route::get('/kelas/{kelas}/forum/{topik}',                                      [MahasiswaForumController::class, 'show'])->name('forum.show');
    Route::post('/kelas/{kelas}/forum/{topik}/komentar',                            [MahasiswaForumController::class, 'storeKomentar'])->name('forum.komentar.store');
    Route::delete('/kelas/{kelas}/forum/{topik}/komentar/{komentar}',               [MahasiswaForumController::class, 'destroyKomentar'])->name('forum.komentar.destroy');

    // Pengumuman (Mahasiswa)
    Route::get('/kelas/{kelas}/pengumuman',                                         [MahasiswaPengumumanController::class, 'index'])->name('pengumuman.index');

    // Materi
    Route::get('/kelas/{kelas}/materi',                                             [MahasiswaMateriController::class, 'index'])->name('materi.index');

    // Tugas
    Route::get('/kelas/{kelas}/tugas',                                              [MahasiswaTugasController::class, 'index'])->name('tugas.index');
    Route::get('/kelas/{kelas}/tugas/{tugas}',                                      [MahasiswaTugasController::class, 'show'])->name('tugas.show');
    Route::post('/kelas/{kelas}/tugas/{tugas}/submit',                              [MahasiswaTugasController::class, 'submit'])->name('tugas.submit');
    Route::delete('/kelas/{kelas}/tugas/{tugas}/submit',                            [MahasiswaTugasController::class, 'cancelSubmit'])->name('tugas.cancel');

    // Presensi
    Route::get('/kelas/{kelas}/presensi',                                           [MahasiswaPresensiController::class, 'index'])->name('presensi.index');

    // Nilai
    Route::get('/kelas/{kelas}/nilai',                                              [MahasiswaNilaiController::class, 'index'])->name('nilai.index');

    // Ujian
    Route::get('/kelas/{kelas}/ujian',                                              [MahasiswaUjianController::class, 'index'])->name('ujian.index');
    Route::get('/kelas/{kelas}/ujian/{ujian}',                                      [MahasiswaUjianController::class, 'show'])->name('ujian.show');
    Route::post('/kelas/{kelas}/ujian/{ujian}/mulai',                               [MahasiswaUjianController::class, 'mulai'])->name('ujian.mulai');
    Route::get('/kelas/{kelas}/ujian/{ujian}/kerjakan',                             [MahasiswaUjianController::class, 'kerjakan'])->name('ujian.kerjakan');
    Route::post('/kelas/{kelas}/ujian/{ujian}/selesai',                             [MahasiswaUjianController::class, 'selesai'])->name('ujian.selesai');
    Route::get('/kelas/{kelas}/ujian/{ujian}/hasil',                                [MahasiswaUjianController::class, 'hasil'])->name('ujian.hasil');

    // RPS (read-only)
    Route::get('/kelas/{kelas}/rps',                                                [MahasiswaRpsController::class, 'show'])->name('rps.show');

    // Capaian CPMK
    Route::get('/kelas/{kelas}/capaian-cpmk',                                       [MahasiswaCapaianCpmkController::class, 'index'])->name('capaian-cpmk.index');
});

require __DIR__.'/auth.php';
