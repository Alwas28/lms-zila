{{-- ===== STATS ROW ===== --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-6">

    {{-- Mata Kuliah Aktif --}}
    <div class="bg-white rounded-2xl p-4 md:p-5 border border-slate-200 card-hover">
        <div class="flex items-start justify-between mb-3">
            <div class="w-9 h-9 md:w-10 md:h-10 rounded-xl bg-ink-50 flex items-center justify-center">
                <i class="fa-solid fa-book text-ink-500 text-sm md:text-base"></i>
            </div>
            <span class="text-xs text-sage-600 bg-sage-50 px-2 py-0.5 rounded-full font-medium badge hidden sm:block">Aktif</span>
        </div>
        <p class="text-xl md:text-2xl font-heading font-semibold text-slate-800">{{ $jumlahMK ?? 0 }}</p>
        <p class="text-xs md:text-sm text-slate-400 mt-0.5">Mata Kuliah Aktif</p>
    </div>

    {{-- Kelas Aktif --}}
    <div class="bg-white rounded-2xl p-4 md:p-5 border border-slate-200 card-hover">
        <div class="flex items-start justify-between mb-3">
            <div class="w-9 h-9 md:w-10 md:h-10 rounded-xl bg-sage-50 flex items-center justify-center">
                <i class="fa-solid fa-door-open text-sage-500 text-sm md:text-base"></i>
            </div>
            <span class="text-xs text-ink-600 bg-ink-50 px-2 py-0.5 rounded-full font-medium badge hidden sm:block">Semester ini</span>
        </div>
        <p class="text-xl md:text-2xl font-heading font-semibold text-slate-800">{{ $jumlahKelas ?? 0 }}</p>
        <p class="text-xs md:text-sm text-slate-400 mt-0.5">Kelas Aktif</p>
    </div>

    {{-- Total Dosen --}}
    <div class="bg-white rounded-2xl p-4 md:p-5 border border-slate-200 card-hover">
        <div class="flex items-start justify-between mb-3">
            <div class="w-9 h-9 md:w-10 md:h-10 rounded-xl bg-purple-50 flex items-center justify-center">
                <i class="fa-solid fa-chalkboard-user text-purple-500 text-sm md:text-base"></i>
            </div>
            <span class="text-xs text-purple-600 bg-purple-50 px-2 py-0.5 rounded-full font-medium badge hidden sm:block">+2 baru</span>
        </div>
        <p class="text-xl md:text-2xl font-heading font-semibold text-slate-800">{{ $jumlahDosen ?? 0 }}</p>
        <p class="text-xs md:text-sm text-slate-400 mt-0.5">Total Dosen</p>
    </div>

    {{-- Total Mahasiswa --}}
    <div class="bg-white rounded-2xl p-4 md:p-5 border border-slate-200 card-hover">
        <div class="flex items-start justify-between mb-3">
            <div class="w-9 h-9 md:w-10 md:h-10 rounded-xl bg-ember-50 flex items-center justify-center">
                <i class="fa-solid fa-user-graduate text-ember-500 text-sm md:text-base"></i>
            </div>
            <span class="text-xs text-ember-700 bg-ember-50 px-2 py-0.5 rounded-full font-medium badge hidden sm:block">Aktif</span>
        </div>
        <p class="text-xl md:text-2xl font-heading font-semibold text-slate-800">{{ number_format($jumlahMahasiswa ?? 0) }}</p>
        <p class="text-xs md:text-sm text-slate-400 mt-0.5">Total Mahasiswa</p>
    </div>

</div>

{{-- ===== TWO COLUMN ===== --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-4 md:gap-6 mb-6">

    {{-- LEFT: Aktivitas Pembelajaran --}}
    <div class="xl:col-span-2 space-y-4">

        {{-- Akses Cepat --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200">
            <h2 class="font-heading font-semibold text-slate-800 text-sm md:text-base mb-4">Akses Cepat</h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <a href="#" class="flex flex-col items-center gap-2 p-3 rounded-xl bg-ink-50 hover:bg-ink-100 transition-colors group">
                    <div class="w-10 h-10 rounded-xl bg-ink-500 flex items-center justify-center">
                        <i class="fa-solid fa-book text-white text-sm"></i>
                    </div>
                    <span class="text-xs font-medium text-slate-700 text-center">Tambah Mata Kuliah</span>
                </a>
                <a href="#" class="flex flex-col items-center gap-2 p-3 rounded-xl bg-sage-50 hover:bg-sage-100 transition-colors group">
                    <div class="w-10 h-10 rounded-xl bg-sage-500 flex items-center justify-center">
                        <i class="fa-solid fa-door-open text-white text-sm"></i>
                    </div>
                    <span class="text-xs font-medium text-slate-700 text-center">Buat Kelas</span>
                </a>
                <a href="#" class="flex flex-col items-center gap-2 p-3 rounded-xl bg-purple-50 hover:bg-purple-100 transition-colors group">
                    <div class="w-10 h-10 rounded-xl bg-purple-500 flex items-center justify-center">
                        <i class="fa-solid fa-user-plus text-white text-sm"></i>
                    </div>
                    <span class="text-xs font-medium text-slate-700 text-center">Tambah Pengguna</span>
                </a>
                <a href="#" class="flex flex-col items-center gap-2 p-3 rounded-xl bg-ember-50 hover:bg-ember-100 transition-colors group">
                    <div class="w-10 h-10 rounded-xl bg-ember-500 flex items-center justify-center">
                        <i class="fa-solid fa-calendar-alt text-white text-sm"></i>
                    </div>
                    <span class="text-xs font-medium text-slate-700 text-center">Tahun Akademik</span>
                </a>
            </div>
        </div>

        {{-- Daftar Kelas Terbaru --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-heading font-semibold text-slate-800 text-sm md:text-base">Kelas Aktif Terbaru</h2>
                <a href="#" class="text-xs text-ink-500 hover:text-ink-700 font-medium transition-colors">Lihat Semua →</a>
            </div>
            <div class="space-y-3">
                @php
                $kelas = [
                    ['nama'=>'Algoritma & Pemrograman - A', 'mk'=>'Algoritma & Pemrograman', 'dosen'=>'Dr. Ahmad Fauzi, M.Kom', 'mhs'=>32, 'kapasitas'=>40, 'color'=>'ink'],
                    ['nama'=>'Basis Data - B', 'mk'=>'Basis Data', 'dosen'=>'Ir. Siti Rahayu, M.T', 'mhs'=>28, 'kapasitas'=>35, 'color'=>'sage'],
                    ['nama'=>'Jaringan Komputer - A', 'mk'=>'Jaringan Komputer', 'dosen'=>'Drs. Budi Santoso, M.Sc', 'mhs'=>30, 'kapasitas'=>35, 'color'=>'purple'],
                    ['nama'=>'Rekayasa Perangkat Lunak - C', 'mk'=>'Rekayasa Perangkat Lunak', 'dosen'=>'Dr. Nur Hidayat, M.Kom', 'mhs'=>25, 'kapasitas'=>35, 'color'=>'ember'],
                ];
                @endphp
                @foreach($kelas as $k)
                <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 hover:bg-slate-100 transition-colors">
                    <div class="w-10 h-10 rounded-xl bg-{{ $k['color'] }}-100 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-door-open text-{{ $k['color'] }}-500 text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-700 truncate">{{ $k['nama'] }}</p>
                        <p class="text-xs text-slate-400 truncate"><i class="fa-solid fa-chalkboard-user mr-1"></i>{{ $k['dosen'] }}</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-xs font-medium text-slate-700">{{ $k['mhs'] }}/{{ $k['kapasitas'] }}</p>
                        <p class="text-xs text-slate-400">mahasiswa</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Statistik Aktivitas Pembelajaran --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-heading font-semibold text-slate-800 text-sm md:text-base">Statistik Aktivitas</h2>
                <span class="text-xs text-slate-400">Minggu ini</span>
            </div>
            <div class="grid grid-cols-3 gap-4 mb-4">
                <div class="text-center">
                    <div class="w-12 h-12 rounded-2xl bg-ink-50 flex items-center justify-center mx-auto mb-2">
                        <i class="fa-solid fa-file-lines text-ink-500 text-lg"></i>
                    </div>
                    <p class="text-xl font-heading font-semibold text-slate-800">142</p>
                    <p class="text-xs text-slate-400">Tugas Dikumpul</p>
                </div>
                <div class="text-center">
                    <div class="w-12 h-12 rounded-2xl bg-sage-50 flex items-center justify-center mx-auto mb-2">
                        <i class="fa-solid fa-user-check text-sage-500 text-lg"></i>
                    </div>
                    <p class="text-xl font-heading font-semibold text-slate-800">892</p>
                    <p class="text-xs text-slate-400">Presensi Masuk</p>
                </div>
                <div class="text-center">
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 flex items-center justify-center mx-auto mb-2">
                        <i class="fa-solid fa-comments text-purple-500 text-lg"></i>
                    </div>
                    <p class="text-xl font-heading font-semibold text-slate-800">67</p>
                    <p class="text-xs text-slate-400">Diskusi Aktif</p>
                </div>
            </div>
            {{-- Bar chart activity --}}
            <div class="flex items-end gap-1.5 h-16 mt-2">
                @php $days = [['l'=>'S','h'=>40],['l'=>'S','h'=>65],['l'=>'R','h'=>55],['l'=>'K','h'=>85],['l'=>'J','h'=>50],['l'=>'S','h'=>20],['l'=>'M','h'=>70]]; @endphp
                @foreach($days as $d)
                <div class="flex flex-col items-center gap-1 flex-1">
                    <div class="w-full bg-ink-{{ $loop->last ? '400' : ($d['h'] > 60 ? '500' : '200') }} rounded-t" style="height:{{ $d['h'] }}%"></div>
                    <span class="text-xs {{ $loop->last ? 'text-slate-800 font-medium' : 'text-slate-400' }}">{{ $d['l'] }}</span>
                </div>
                @endforeach
            </div>
        </div>

    </div>

    {{-- RIGHT: Info Panel --}}
    <div class="space-y-4">

        {{-- Tahun Akademik Aktif --}}
        @php $semAktif = \App\Models\Semester::aktif(); @endphp
        <div class="bg-gradient-to-br from-ink-500 to-ink-700 rounded-2xl p-5 text-white">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-calendar-check text-ink-200 text-sm"></i>
                    <h3 class="font-heading font-semibold text-sm">Semester Berjalan</h3>
                </div>
                <a href="{{ route('admin.tahun-akademik.index') }}" class="text-xs bg-white/20 hover:bg-white/30 px-2.5 py-1 rounded-lg transition-colors">
                    Kelola →
                </a>
            </div>
            @if($semAktif)
            <p class="text-2xl font-heading font-bold mb-1">{{ $semAktif->tahunAkademik->nama }}</p>
            <p class="text-ink-200 text-sm mb-3">Semester {{ ucfirst($semAktif->tipe) }}</p>
            <div class="flex items-center justify-between text-xs">
                <span class="bg-white/20 px-2.5 py-1 rounded-lg">
                    {{ $semAktif->tanggal_mulai ? $semAktif->tanggal_mulai->format('d M Y') : 'Mulai —' }}
                </span>
                <span class="bg-white/20 px-2.5 py-1 rounded-lg">
                    {{ $semAktif->tanggal_selesai ? $semAktif->tanggal_selesai->format('d M Y') : 'Selesai —' }}
                </span>
            </div>
            @else
            <p class="text-ink-200 text-sm">Belum ada semester aktif.</p>
            <a href="{{ route('admin.tahun-akademik.index') }}" class="inline-block mt-2 text-xs bg-white/20 hover:bg-white/30 px-3 py-1.5 rounded-lg transition-colors">
                Atur Sekarang
            </a>
            @endif
        </div>

        {{-- Distribusi Mahasiswa per Prodi --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200">
            <h3 class="font-heading font-semibold text-slate-800 text-sm mb-4">Distribusi Mahasiswa</h3>
            <div class="space-y-3">
                @php
                $prodi = [
                    ['nama'=>'Teknik Informatika', 'jml'=>420, 'max'=>500, 'color'=>'ink'],
                    ['nama'=>'Sistem Informasi', 'jml'=>380, 'max'=>500, 'color'=>'sage'],
                    ['nama'=>'Teknik Elektro', 'jml'=>260, 'max'=>400, 'color'=>'purple'],
                    ['nama'=>'Manajemen', 'jml'=>180, 'max'=>300, 'color'=>'ember'],
                ];
                @endphp
                @foreach($prodi as $p)
                <div>
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-slate-600 font-medium truncate pr-2">{{ $p['nama'] }}</span>
                        <span class="text-slate-400 flex-shrink-0">{{ $p['jml'] }}</span>
                    </div>
                    <div class="h-2 bg-slate-100 rounded-full">
                        <div class="h-2 bg-{{ $p['color'] }}-500 rounded-full progress-bar" style="width:{{ round($p['jml']/$p['max']*100) }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Pengumuman Terbaru --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-heading font-semibold text-slate-800 text-sm">Pengumuman</h3>
                <a href="#" class="text-xs text-ink-500 hover:text-ink-700 font-medium">+ Buat</a>
            </div>
            <div class="space-y-3">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-ink-50 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fa-solid fa-bullhorn text-ink-500 text-xs"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-slate-700 leading-snug">Jadwal UAS Semester Genap 2025/2026</p>
                        <p class="text-xs text-slate-400 mt-0.5">2 jam yang lalu</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-ember-50 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fa-solid fa-triangle-exclamation text-ember-500 text-xs"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-slate-700 leading-snug">Batas Upload RPS Pertemuan 1-8</p>
                        <p class="text-xs text-ember-500 mt-0.5">Deadline: 5 Jun 2026</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-sage-50 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fa-solid fa-circle-check text-sage-500 text-xs"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-slate-700 leading-snug">Import Data Mahasiswa Baru Selesai</p>
                        <p class="text-xs text-slate-400 mt-0.5">1 hari yang lalu</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tugas Sistem --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200">
            <h3 class="font-heading font-semibold text-slate-800 text-sm mb-3">Yang Perlu Dilakukan</h3>
            <div class="space-y-2.5">
                <label class="flex items-center gap-3 cursor-pointer group">
                    <div class="w-5 h-5 rounded-md border-2 border-ember-300 bg-ember-50 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-xmark text-ember-400 text-xs"></i>
                    </div>
                    <p class="text-xs text-slate-600 group-hover:text-slate-800 transition-colors">Aktifkan Tahun Akademik 2026/2027</p>
                </label>
                <label class="flex items-center gap-3 cursor-pointer group">
                    <div class="w-5 h-5 rounded-md border-2 border-ember-300 bg-ember-50 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-xmark text-ember-400 text-xs"></i>
                    </div>
                    <p class="text-xs text-slate-600 group-hover:text-slate-800 transition-colors">Tetapkan dosen pengampu Semester Ganjil</p>
                </label>
                <label class="flex items-center gap-3 cursor-pointer group">
                    <div class="w-5 h-5 rounded-md border-2 border-sage-400 bg-sage-50 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-check text-sage-500 text-xs"></i>
                    </div>
                    <p class="text-xs text-slate-400 line-through">Import data mahasiswa 2025/2026</p>
                </label>
            </div>
        </div>

    </div>
</div>
