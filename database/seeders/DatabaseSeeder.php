<?php

namespace Database\Seeders;

use App\Models\Enrollment;
use App\Models\Kelas;
use App\Models\MataKuliah;
use App\Models\Pengampu;
use App\Models\Pertemuan;
use App\Models\Rps;
use App\Models\Semester;
use App\Models\TahunAkademik;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Users ────────────────────────────────────────────────────────

        $admin = User::create([
            'name'     => 'Administrator',
            'email'    => 'admin@lmszila.ac.id',
            'password' => Hash::make('password'),
            'role'     => 'admin',
            'nip_nim'  => '198001012005011001',
        ]);

        $dosen1 = User::create([
            'name'     => 'Dr. Ahmad Fauzi, M.Kom',
            'email'    => 'ahmad.fauzi@lmszila.ac.id',
            'password' => Hash::make('password'),
            'role'     => 'dosen',
            'nip_nim'  => '197805152003121002',
        ]);

        $dosen2 = User::create([
            'name'     => 'Ir. Siti Rahayu, M.T',
            'email'    => 'siti.rahayu@lmszila.ac.id',
            'password' => Hash::make('password'),
            'role'     => 'dosen',
            'nip_nim'  => '198203102006042001',
        ]);

        $mhs1 = User::create([
            'name'     => 'Andi Pratama',
            'email'    => 'andi.pratama@mhs.lmszila.ac.id',
            'password' => Hash::make('password'),
            'role'     => 'mahasiswa',
            'nip_nim'  => '2023110001',
        ]);

        $mhs2 = User::create([
            'name'     => 'Nurul Hidayah',
            'email'    => 'nurul.hidayah@mhs.lmszila.ac.id',
            'password' => Hash::make('password'),
            'role'     => 'mahasiswa',
            'nip_nim'  => '2023110002',
        ]);

        $mhs3 = User::create([
            'name'     => 'Budi Santoso',
            'email'    => 'budi.santoso@mhs.lmszila.ac.id',
            'password' => Hash::make('password'),
            'role'     => 'mahasiswa',
            'nip_nim'  => '2023110003',
        ]);

        $mhs4 = User::create([
            'name'     => 'Dewi Lestari',
            'email'    => 'dewi.lestari@mhs.lmszila.ac.id',
            'password' => Hash::make('password'),
            'role'     => 'mahasiswa',
            'nip_nim'  => '2023110004',
        ]);

        // ── Tahun Akademik ───────────────────────────────────────────────

        $ta2425 = TahunAkademik::create(['nama' => '2024/2025', 'is_aktif' => false]);
        $ta2526 = TahunAkademik::create(['nama' => '2025/2026', 'is_aktif' => true]);

        // ── Semester ─────────────────────────────────────────────────────

        Semester::create([
            'tahun_akademik_id' => $ta2425->id,
            'tipe'              => 'ganjil',
            'tanggal_mulai'     => '2024-09-01',
            'tanggal_selesai'   => '2025-01-31',
            'is_aktif'          => false,
        ]);
        Semester::create([
            'tahun_akademik_id' => $ta2425->id,
            'tipe'              => 'genap',
            'tanggal_mulai'     => '2025-02-01',
            'tanggal_selesai'   => '2025-07-31',
            'is_aktif'          => false,
        ]);
        Semester::create([
            'tahun_akademik_id' => $ta2526->id,
            'tipe'              => 'ganjil',
            'tanggal_mulai'     => '2025-09-01',
            'tanggal_selesai'   => '2026-01-31',
            'is_aktif'          => false,
        ]);
        $semesterAktif = Semester::create([
            'tahun_akademik_id' => $ta2526->id,
            'tipe'              => 'genap',
            'tanggal_mulai'     => '2026-02-01',
            'tanggal_selesai'   => '2026-07-31',
            'is_aktif'          => true,
        ]);

        // ── Mata Kuliah ──────────────────────────────────────────────────

        $mk1 = MataKuliah::create([
            'kode'      => 'IF201',
            'nama'      => 'Pemrograman Web',
            'sks'       => 3,
            'deskripsi' => 'Mata kuliah yang membahas pengembangan aplikasi berbasis web menggunakan teknologi modern.',
            'is_arsip'  => false,
        ]);

        $mk2 = MataKuliah::create([
            'kode'      => 'IF301',
            'nama'      => 'Basis Data',
            'sks'       => 3,
            'deskripsi' => 'Konsep dan implementasi sistem basis data relasional dan non-relasional.',
            'is_arsip'  => false,
        ]);

        $mk3 = MataKuliah::create([
            'kode'      => 'IF401',
            'nama'      => 'Rekayasa Perangkat Lunak',
            'sks'       => 3,
            'deskripsi' => 'Metodologi dan praktik pengembangan perangkat lunak berkualitas tinggi.',
            'is_arsip'  => false,
        ]);

        // ── Kelas ────────────────────────────────────────────────────────

        $kelas1 = Kelas::create([
            'mata_kuliah_id' => $mk1->id,
            'semester_id'    => $semesterAktif->id,
            'nama_kelas'     => 'A',
            'kapasitas'      => 40,
            'is_aktif'       => true,
        ]);

        $kelas2 = Kelas::create([
            'mata_kuliah_id' => $mk1->id,
            'semester_id'    => $semesterAktif->id,
            'nama_kelas'     => 'B',
            'kapasitas'      => 40,
            'is_aktif'       => true,
        ]);

        $kelas3 = Kelas::create([
            'mata_kuliah_id' => $mk2->id,
            'semester_id'    => $semesterAktif->id,
            'nama_kelas'     => 'A',
            'kapasitas'      => 35,
            'is_aktif'       => true,
        ]);

        // ── Pengampu ─────────────────────────────────────────────────────

        Pengampu::create(['kelas_id' => $kelas1->id, 'user_id' => $dosen1->id, 'is_koordinator' => true]);
        Pengampu::create(['kelas_id' => $kelas2->id, 'user_id' => $dosen1->id, 'is_koordinator' => true]);
        Pengampu::create(['kelas_id' => $kelas3->id, 'user_id' => $dosen2->id, 'is_koordinator' => true]);

        // ── Enrollment ───────────────────────────────────────────────────

        foreach ([$mhs1, $mhs2, $mhs3, $mhs4] as $mhs) {
            Enrollment::create(['kelas_id' => $kelas1->id, 'user_id' => $mhs->id]);
        }
        foreach ([$mhs1, $mhs2] as $mhs) {
            Enrollment::create(['kelas_id' => $kelas3->id, 'user_id' => $mhs->id]);
        }

        // ── Pertemuan ────────────────────────────────────────────────────

        $pertemuanData = [
            ['nomor' => 1, 'topik' => 'Pengenalan HTML & CSS', 'tanggal' => '2026-02-10', 'status' => 'selesai'],
            ['nomor' => 2, 'topik' => 'Dasar JavaScript', 'tanggal' => '2026-02-17', 'status' => 'selesai'],
            ['nomor' => 3, 'topik' => 'DOM Manipulation', 'tanggal' => '2026-02-24', 'status' => 'selesai'],
            ['nomor' => 4, 'topik' => 'PHP Dasar & Sintaks', 'tanggal' => '2026-03-03', 'status' => 'selesai'],
            ['nomor' => 5, 'topik' => 'Laravel: Routing & Controller', 'tanggal' => '2026-03-10', 'status' => 'selesai'],
            ['nomor' => 6, 'topik' => 'Laravel: Eloquent ORM', 'tanggal' => '2026-03-17', 'status' => 'draft'],
            ['nomor' => 7, 'topik' => 'Laravel: Blade Template', 'tanggal' => '2026-03-24', 'status' => 'draft'],
            ['nomor' => 8, 'topik' => 'UTS', 'tanggal' => '2026-03-31', 'status' => 'draft'],
        ];

        $pertemuanKelas1 = [];
        foreach ($pertemuanData as $pd) {
            $pertemuanKelas1[] = Pertemuan::create(array_merge($pd, ['kelas_id' => $kelas1->id]));
        }

        // ── RPS ──────────────────────────────────────────────────────────

        Rps::create([
            'kelas_id'             => $kelas1->id,
            'deskripsi_mk'         => 'Mata kuliah Pemrograman Web membahas konsep dan implementasi pengembangan aplikasi web dari sisi front-end dan back-end menggunakan teknologi modern.',
            'cpl'                  => "CPL-1: Mampu mengidentifikasi dan menganalisis masalah komputasi\nCPL-2: Mampu merancang dan mengimplementasikan solusi berbasis web\nCPL-3: Mampu bekerja secara mandiri dan tim",
            'cpmk'                 => "Mahasiswa mampu membuat halaman web statis menggunakan HTML dan CSS\nMahasiswa mampu mengimplementasikan interaktivitas menggunakan JavaScript\nMahasiswa mampu membangun aplikasi web dinamis menggunakan PHP dan Laravel\nMahasiswa mampu mengintegrasikan basis data pada aplikasi web",
            'sub_cpmk'             => "Sub-CPMK 1.1: Struktur HTML5\nSub-CPMK 1.2: CSS Flexbox & Grid\nSub-CPMK 2.1: Variable, Function, Event\nSub-CPMK 3.1: MVC Pattern\nSub-CPMK 3.2: Eloquent ORM",
            'metode_pembelajaran'  => "Ceramah, diskusi, praktikum, dan project-based learning",
            'referensi'            => "1. Duckett, J. (2011). HTML & CSS: Design and Build Websites\n2. Laravel Documentation (https://laravel.com/docs)\n3. MDN Web Docs (https://developer.mozilla.org)",
        ]);

        // ── Tugas ────────────────────────────────────────────────────────

        $tugas1 = Tugas::create([
            'kelas_id'    => $kelas1->id,
            'pertemuan_id'=> $pertemuanKelas1[1]->id,
            'judul'       => 'Tugas 1 — Membuat Halaman Web Profil',
            'deskripsi'   => 'Buat halaman web profil pribadi menggunakan HTML5 dan CSS3. Halaman harus memuat: nama, foto, riwayat pendidikan, keahlian, dan kontak.',
            'tipe'        => 'individu',
            'deadline'    => '2026-02-28 23:59:00',
            'nilai_maks'  => 100,
        ]);

        $tugas2 = Tugas::create([
            'kelas_id'    => $kelas1->id,
            'pertemuan_id'=> $pertemuanKelas1[3]->id,
            'judul'       => 'Tugas 2 — CRUD Sederhana dengan PHP',
            'deskripsi'   => 'Implementasikan operasi CRUD (Create, Read, Update, Delete) menggunakan PHP murni dan MySQL. Buat antarmuka untuk manajemen data mahasiswa.',
            'tipe'        => 'individu',
            'deadline'    => now()->addDays(7)->format('Y-m-d H:i:s'),
            'nilai_maks'  => 100,
        ]);

        Tugas::create([
            'kelas_id'    => $kelas1->id,
            'pertemuan_id'=> null,
            'judul'       => 'Proyek Akhir — Aplikasi Web Laravel',
            'deskripsi'   => 'Buat aplikasi web lengkap menggunakan Laravel 10. Topik bebas, namun harus mencakup: autentikasi, CRUD, relasi database, dan validasi.',
            'tipe'        => 'kelompok',
            'deadline'    => '2026-06-30 23:59:00',
            'nilai_maks'  => 100,
        ]);
    }
}
