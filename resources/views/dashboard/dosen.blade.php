{{-- ===== STATS ROW ===== --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-6">

    {{-- Mata Kuliah Diampu --}}
    <div class="bg-white rounded-2xl p-4 md:p-5 border border-slate-200 card-hover">
        <div class="flex items-start justify-between mb-3">
            <div class="w-9 h-9 md:w-10 md:h-10 rounded-xl bg-ink-50 flex items-center justify-center">
                <i class="fa-solid fa-book-open text-ink-500 text-sm md:text-base"></i>
            </div>
            <span class="text-xs text-ink-600 bg-ink-50 px-2 py-0.5 rounded-full font-medium badge hidden sm:block">Semester ini</span>
        </div>
        <p class="text-xl md:text-2xl font-heading font-semibold text-slate-800">{{ $jumlahKelas ?? 0 }}</p>
        <p class="text-xs md:text-sm text-slate-400 mt-0.5">Kelas Diampu</p>
    </div>

    {{-- Tugas Perlu Dinilai --}}
    <div class="bg-white rounded-2xl p-4 md:p-5 border border-slate-200 card-hover">
        <div class="flex items-start justify-between mb-3">
            <div class="w-9 h-9 md:w-10 md:h-10 rounded-xl bg-ember-50 flex items-center justify-center">
                <i class="fa-solid fa-pen-to-square text-ember-500 text-sm md:text-base"></i>
            </div>
            <span class="text-xs text-ember-700 bg-ember-50 px-2 py-0.5 rounded-full font-medium badge hidden sm:block">Segera</span>
        </div>
        <p class="text-xl md:text-2xl font-heading font-semibold text-slate-800">{{ $tugasPerluDinilai ?? 0 }}</p>
        <p class="text-xs md:text-sm text-slate-400 mt-0.5">Tugas Perlu Dinilai</p>
    </div>

    {{-- Pertemuan Hari Ini --}}
    <div class="bg-white rounded-2xl p-4 md:p-5 border border-slate-200 card-hover">
        <div class="flex items-start justify-between mb-3">
            <div class="w-9 h-9 md:w-10 md:h-10 rounded-xl bg-sage-50 flex items-center justify-center">
                <i class="fa-solid fa-calendar-day text-sage-500 text-sm md:text-base"></i>
            </div>
            <span class="text-xs text-sage-600 bg-sage-50 px-2 py-0.5 rounded-full font-medium badge hidden sm:block">Hari ini</span>
        </div>
        <p class="text-xl md:text-2xl font-heading font-semibold text-slate-800">2</p>
        <p class="text-xs md:text-sm text-slate-400 mt-0.5">Pertemuan Hari Ini</p>
    </div>

    {{-- Ujian Berlangsung --}}
    <div class="bg-white rounded-2xl p-4 md:p-5 border border-slate-200 card-hover">
        <div class="flex items-start justify-between mb-3">
            <div class="w-9 h-9 md:w-10 md:h-10 rounded-xl bg-purple-50 flex items-center justify-center">
                <i class="fa-solid fa-clipboard-question text-purple-500 text-sm md:text-base"></i>
            </div>
            <span class="text-xs text-purple-600 bg-purple-50 px-2 py-0.5 rounded-full font-medium badge hidden sm:block">Aktif</span>
        </div>
        <p class="text-xl md:text-2xl font-heading font-semibold text-slate-800">{{ $ujianAktif ?? 0 }}</p>
        <p class="text-xs md:text-sm text-slate-400 mt-0.5">Ujian Berlangsung</p>
    </div>

</div>

{{-- ===== TWO COLUMN ===== --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-4 md:gap-6">

    {{-- LEFT: Mata Kuliah yang Diampu --}}
    <div class="xl:col-span-2 space-y-4">

        {{-- Mata Kuliah Saya --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-heading font-semibold text-slate-800 text-sm md:text-base">Mata Kuliah yang Diampu</h2>
                <a href="#" class="text-xs text-ink-500 hover:text-ink-700 font-medium transition-colors">Lihat Semua →</a>
            </div>
            <div class="space-y-3">
                @php
                $matakuliah = [
                    ['kode'=>'IF301','nama'=>'Algoritma & Pemrograman','kelas'=>'A','sks'=>3,'pertemuan'=>8,'total'=>16,'mhs'=>32,'color'=>'ink','icon'=>'fa-code'],
                    ['kode'=>'IF402','nama'=>'Basis Data Lanjut','kelas'=>'B','sks'=>3,'pertemuan'=>6,'total'=>16,'mhs'=>28,'color'=>'sage','icon'=>'fa-database'],
                    ['kode'=>'IF501','nama'=>'Rekayasa Perangkat Lunak','kelas'=>'A','sks'=>4,'pertemuan'=>7,'total'=>16,'mhs'=>30,'color'=>'purple','icon'=>'fa-diagram-project'],
                ];
                @endphp
                @foreach($matakuliah as $mk)
                <div class="bg-white rounded-2xl p-4 border border-slate-200 card-hover flex gap-3 md:gap-4">
                    <div class="w-14 h-14 md:w-16 md:h-16 rounded-xl overflow-hidden flex-shrink-0">
                        <div class="w-full h-full bg-gradient-to-br from-{{ $mk['color'] }}-400 to-{{ $mk['color'] }}-600 flex items-center justify-center">
                            <i class="fa-solid {{ $mk['icon'] }} text-white text-xl md:text-2xl"></i>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <span class="text-xs bg-{{ $mk['color'] }}-50 text-{{ $mk['color'] }}-600 px-2 py-0.5 rounded-full badge font-medium">{{ $mk['kode'] }} — Kelas {{ $mk['kelas'] }}</span>
                                <h3 class="font-heading font-semibold text-slate-800 text-xs md:text-sm mt-1.5 leading-snug">{{ $mk['nama'] }}</h3>
                            </div>
                            <span class="text-xs text-slate-400 flex-shrink-0">{{ $mk['sks'] }} SKS</span>
                        </div>
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-2 text-xs text-slate-400">
                            <span><i class="fa-solid fa-users mr-1"></i>{{ $mk['mhs'] }} Mahasiswa</span>
                            <span><i class="fa-solid fa-layer-group mr-1"></i>{{ $mk['pertemuan'] }}/{{ $mk['total'] }} Pertemuan</span>
                        </div>
                        <div class="mt-2 md:mt-3">
                            <div class="flex justify-between text-xs mb-1">
                                <span class="text-slate-500">Progress Pertemuan</span>
                                <span class="font-medium text-{{ $mk['color'] }}-600">{{ round($mk['pertemuan']/$mk['total']*100) }}%</span>
                            </div>
                            <div class="h-1.5 bg-slate-100 rounded-full">
                                <div class="h-1.5 bg-{{ $mk['color'] }}-500 rounded-full progress-bar" style="width:{{ round($mk['pertemuan']/$mk['total']*100) }}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col items-end justify-between flex-shrink-0">
                        <span class="text-xs text-slate-400 whitespace-nowrap hidden sm:block">{{ $mk['pertemuan'] }}/{{ $mk['total'] }}</span>
                        <a href="#" class="w-8 h-8 md:w-9 md:h-9 rounded-xl bg-{{ $mk['color'] }}-500 hover:bg-{{ $mk['color'] }}-600 transition-colors flex items-center justify-center text-white">
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Tugas Perlu Dinilai --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-heading font-semibold text-slate-800 text-sm md:text-base">Tugas yang Perlu Dinilai</h2>
                <span class="text-xs bg-ember-50 text-ember-600 px-2 py-0.5 rounded-full font-medium badge">18 tugas</span>
            </div>
            <div class="space-y-3">
                @php
                $tugas = [
                    ['judul'=>'Tugas 3: Implementasi Algoritma Sorting','mk'=>'Algoritma & Pemrograman - A','dikumpul'=>32,'total'=>32,'deadline'=>'3 Jun 2026','status'=>'urgent'],
                    ['judul'=>'Laporan Praktikum ERD','mk'=>'Basis Data Lanjut - B','dikumpul'=>25,'total'=>28,'deadline'=>'5 Jun 2026','status'=>'normal'],
                    ['judul'=>'Dokumen SRS Proyek Kelompok','mk'=>'Rekayasa Perangkat Lunak - A','dikumpul'=>8,'total'=>10,'deadline'=>'10 Jun 2026','status'=>'normal'],
                ];
                @endphp
                @foreach($tugas as $t)
                <div class="flex items-center gap-3 p-3.5 rounded-xl border border-slate-200 hover:border-ink-200 hover:bg-ink-50/30 transition-colors">
                    <div class="w-9 h-9 rounded-xl bg-{{ $t['status']==='urgent' ? 'ember' : 'ink' }}-50 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-file-pen text-{{ $t['status']==='urgent' ? 'ember' : 'ink' }}-500 text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-700 truncate">{{ $t['judul'] }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $t['mk'] }} &middot; <span class="text-{{ $t['status']==='urgent' ? 'ember-500' : 'slate-400' }}">{{ $t['dikumpul'] }}/{{ $t['total'] }} dikumpulkan</span></p>
                    </div>
                    <a href="#" class="text-xs bg-ink-500 hover:bg-ink-600 text-white px-3 py-1.5 rounded-lg transition-colors flex-shrink-0 font-medium">
                        Nilai
                    </a>
                </div>
                @endforeach
            </div>
        </div>

    </div>

    {{-- RIGHT: Widgets --}}
    <div class="space-y-4">

        {{-- Jadwal Perkuliahan Hari Ini --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-heading font-semibold text-slate-800 text-sm">Jadwal Hari Ini</h3>
                <span class="text-xs text-slate-400">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('ddd, D MMM') }}</span>
            </div>
            <div class="space-y-3">
                <div class="flex gap-3">
                    <div class="flex flex-col items-center">
                        <span class="text-xs font-medium text-ink-500">08:00</span>
                        <div class="w-0.5 flex-1 bg-ink-200 my-1"></div>
                        <span class="text-xs font-medium text-ink-500">09:40</span>
                    </div>
                    <div class="flex-1 bg-ink-50 rounded-xl p-3 border border-ink-100">
                        <p class="text-sm font-medium text-slate-700">Algoritma & Pemrograman</p>
                        <p class="text-xs text-slate-400 mt-0.5">Kelas A &middot; Pertemuan 9</p>
                        <p class="text-xs text-ink-500 mt-1.5"><i class="fa-solid fa-location-dot mr-1"></i>Lab Komputer 2</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <div class="flex flex-col items-center">
                        <span class="text-xs font-medium text-sage-600">13:00</span>
                        <div class="w-0.5 flex-1 bg-sage-200 my-1"></div>
                        <span class="text-xs font-medium text-sage-600">14:40</span>
                    </div>
                    <div class="flex-1 bg-sage-50 rounded-xl p-3 border border-sage-100">
                        <p class="text-sm font-medium text-slate-700">Basis Data Lanjut</p>
                        <p class="text-xs text-slate-400 mt-0.5">Kelas B &middot; Pertemuan 7</p>
                        <p class="text-xs text-sage-600 mt-1.5"><i class="fa-solid fa-location-dot mr-1"></i>Ruang 301</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Ujian yang Berlangsung --}}
        <div class="bg-gradient-to-br from-purple-600 to-purple-800 rounded-2xl p-5 text-white">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-2 h-2 bg-sage-400 rounded-full animate-pulse"></div>
                <h3 class="font-heading font-semibold text-sm">Ujian Sedang Berlangsung</h3>
            </div>
            <p class="font-heading font-semibold text-base mb-1">Quiz Pertemuan 7</p>
            <p class="text-purple-200 text-sm mb-3">Rekayasa Perangkat Lunak - A</p>
            <div class="flex items-center justify-between text-xs mb-3">
                <span class="bg-white/20 px-2.5 py-1 rounded-lg">18/30 Peserta</span>
                <span class="bg-white/20 px-2.5 py-1 rounded-lg">Sisa 35 mnt</span>
            </div>
            <a href="#" class="block w-full text-center bg-white text-purple-700 text-sm font-semibold py-2 rounded-xl hover:bg-purple-50 transition-colors">
                Pantau Ujian
            </a>
        </div>

        {{-- Aktivitas Minggu Ini --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-heading font-semibold text-slate-800 text-sm">Rekap Minggu Ini</h3>
            </div>
            <div class="space-y-2.5">
                <div class="flex items-center justify-between p-2.5 rounded-lg bg-slate-50">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-user-check text-sage-500 text-xs w-4 text-center"></i>
                        <span class="text-xs text-slate-600">Presensi diinput</span>
                    </div>
                    <span class="text-xs font-medium text-slate-700">4 kali</span>
                </div>
                <div class="flex items-center justify-between p-2.5 rounded-lg bg-slate-50">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-folder-open text-ink-500 text-xs w-4 text-center"></i>
                        <span class="text-xs text-slate-600">Materi diupload</span>
                    </div>
                    <span class="text-xs font-medium text-slate-700">6 file</span>
                </div>
                <div class="flex items-center justify-between p-2.5 rounded-lg bg-slate-50">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-pen-to-square text-ember-500 text-xs w-4 text-center"></i>
                        <span class="text-xs text-slate-600">Tugas dinilai</span>
                    </div>
                    <span class="text-xs font-medium text-slate-700">45 tugas</span>
                </div>
                <div class="flex items-center justify-between p-2.5 rounded-lg bg-slate-50">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-comments text-purple-500 text-xs w-4 text-center"></i>
                        <span class="text-xs text-slate-600">Balasan forum</span>
                    </div>
                    <span class="text-xs font-medium text-slate-700">12 balasan</span>
                </div>
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
                    <div class="w-8 h-8 rounded-lg bg-ember-50 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fa-solid fa-triangle-exclamation text-ember-500 text-xs"></i>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-700 leading-snug">Perubahan jadwal UAS Algoritma</p>
                        <p class="text-xs text-ember-500 mt-0.5">Deadline buat: 3 Jun 2026</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-ink-50 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fa-solid fa-bullhorn text-ink-500 text-xs"></i>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-slate-700 leading-snug">Dari Admin: Upload RPS maksimal 5 Jun</p>
                        <p class="text-xs text-slate-400 mt-0.5">1 hari yang lalu</p>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
