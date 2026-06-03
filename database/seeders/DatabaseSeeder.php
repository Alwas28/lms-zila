<?php

namespace Database\Seeders;

use App\Models\Semester;
use App\Models\TahunAkademik;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Users ────────────────────────────────────────────────────────

        User::create([
            'name'     => 'Administrator',
            'email'    => 'admin@lmszila.ac.id',
            'password' => Hash::make('password'),
            'role'     => 'admin',
            'nip_nim'  => '198001012005011001',
        ]);

        User::create([
            'name'     => 'Dr. Ahmad Fauzi, M.Kom',
            'email'    => 'ahmad.fauzi@lmszila.ac.id',
            'password' => Hash::make('password'),
            'role'     => 'dosen',
            'nip_nim'  => '197805152003121002',
        ]);

        User::create([
            'name'     => 'Ir. Siti Rahayu, M.T',
            'email'    => 'siti.rahayu@lmszila.ac.id',
            'password' => Hash::make('password'),
            'role'     => 'dosen',
            'nip_nim'  => '198203102006042001',
        ]);

        User::create([
            'name'     => 'Andi Pratama',
            'email'    => 'andi.pratama@mhs.lmszila.ac.id',
            'password' => Hash::make('password'),
            'role'     => 'mahasiswa',
            'nip_nim'  => '2023110001',
        ]);

        User::create([
            'name'     => 'Nurul Hidayah',
            'email'    => 'nurul.hidayah@mhs.lmszila.ac.id',
            'password' => Hash::make('password'),
            'role'     => 'mahasiswa',
            'nip_nim'  => '2023110002',
        ]);

        // ── Tahun Akademik ───────────────────────────────────────────────

        $ta2425 = TahunAkademik::create(['nama' => '2024/2025', 'is_aktif' => false]);
        $ta2526 = TahunAkademik::create(['nama' => '2025/2026', 'is_aktif' => true]);  // aktif

        // ── Semester ─────────────────────────────────────────────────────

        // Tahun 2024/2025
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

        // Tahun 2025/2026
        Semester::create([
            'tahun_akademik_id' => $ta2526->id,
            'tipe'              => 'ganjil',
            'tanggal_mulai'     => '2025-09-01',
            'tanggal_selesai'   => '2026-01-31',
            'is_aktif'          => false,
        ]);
        Semester::create([
            'tahun_akademik_id' => $ta2526->id,
            'tipe'              => 'genap',
            'tanggal_mulai'     => '2026-02-01',
            'tanggal_selesai'   => '2026-07-31',
            'is_aktif'          => true,  // ← semester yang sedang berjalan
        ]);
    }
}
