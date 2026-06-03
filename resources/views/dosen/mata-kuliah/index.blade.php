<x-app-layout>
    <x-slot name="title">Mata Kuliah Saya</x-slot>
    <x-slot name="pageTitle">Mata Kuliah Saya</x-slot>

    @include('admin._flash')

    {{-- Header Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-6">
        <div class="bg-white rounded-2xl p-4 border border-slate-200 card-hover flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-ink-50 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-book-open text-ink-500"></i>
            </div>
            <div>
                <p class="text-xl font-heading font-semibold text-slate-800">{{ $kelas->count() }}</p>
                <p class="text-xs text-slate-400">Total Kelas</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-slate-200 card-hover flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-sage-50 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-circle-check text-sage-500"></i>
            </div>
            <div>
                <p class="text-xl font-heading font-semibold text-slate-800">{{ $kelas->where('is_aktif', true)->count() }}</p>
                <p class="text-xs text-slate-400">Kelas Aktif</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-slate-200 card-hover flex items-center gap-3 col-span-2 sm:col-span-1">
            <div class="w-10 h-10 rounded-xl bg-ember-50 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-user-graduate text-ember-500"></i>
            </div>
            <div>
                <p class="text-xl font-heading font-semibold text-slate-800">{{ $kelas->sum('mahasiswa_count') }}</p>
                <p class="text-xs text-slate-400">Total Mahasiswa</p>
            </div>
        </div>
    </div>

    @if($kelas->isEmpty())
    {{-- Empty State --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-16 text-center">
        <div class="w-20 h-20 rounded-2xl bg-ink-50 flex items-center justify-center mx-auto mb-4">
            <i class="fa-solid fa-book-open text-ink-300 text-3xl"></i>
        </div>
        <h3 class="font-heading font-semibold text-slate-600 text-lg mb-2">Belum Ada Kelas</h3>
        <p class="text-slate-400 text-sm">Anda belum diampu di kelas manapun. Hubungi administrator untuk mendapatkan akses kelas.</p>
    </div>
    @else
    {{-- Kelas Cards Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($kelas as $k)
        @php
            $selesai = $k->pertemuan_count > 0 ? $k->pertemuan_count : 0;
            $pct = min(100, round($selesai / 16 * 100));
            $warna = ['bg-ink-500', 'bg-sage-500', 'bg-ember-500', 'bg-blue-500', 'bg-purple-500'];
            $w = $warna[$loop->index % count($warna)];
        @endphp
        <div class="bg-white rounded-2xl border border-slate-200 card-hover overflow-hidden flex flex-col">
            {{-- Card Header --}}
            <div class="px-5 pt-5 pb-4">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-11 h-11 rounded-xl {{ $w }} flex items-center justify-center flex-shrink-0 shadow-sm">
                        <i class="fa-solid fa-book text-white text-base"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-heading font-semibold text-slate-800 text-sm leading-snug truncate">{{ $k->mataKuliah->nama }}</h3>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="text-xs text-slate-400 font-mono">{{ $k->mataKuliah->kode }}</span>
                            <span class="text-slate-200">·</span>
                            <span class="text-xs text-slate-400">{{ $k->mataKuliah->sks }} SKS</span>
                            <span class="text-slate-200">·</span>
                            <span class="text-xs font-semibold text-ink-500">Kelas {{ $k->nama_kelas }}</span>
                        </div>
                    </div>
                    @if($k->is_aktif)
                    <span class="flex-shrink-0 text-xs bg-sage-50 text-sage-600 px-2 py-0.5 rounded-full font-medium">Aktif</span>
                    @else
                    <span class="flex-shrink-0 text-xs bg-slate-100 text-slate-400 px-2 py-0.5 rounded-full font-medium">Tutup</span>
                    @endif
                </div>

                {{-- Semester --}}
                <p class="text-xs text-slate-400 mb-3">
                    <i class="fa-solid fa-calendar-alt mr-1"></i>
                    Semester {{ ucfirst($k->semester->tipe) }} — {{ $k->semester->tahunAkademik->nama }}
                </p>

                {{-- Stats Row --}}
                <div class="flex items-center gap-4 mb-3">
                    <div class="flex items-center gap-1.5 text-xs text-slate-500">
                        <i class="fa-solid fa-user-graduate text-slate-400"></i>
                        <span><strong class="text-slate-700">{{ $k->mahasiswa_count }}</strong> Mahasiswa</span>
                    </div>
                    <div class="flex items-center gap-1.5 text-xs text-slate-500">
                        <i class="fa-solid fa-calendar-days text-slate-400"></i>
                        <span><strong class="text-slate-700">{{ $k->pertemuan_count }}</strong>/16 Pertemuan</span>
                    </div>
                </div>

                {{-- Progress Bar --}}
                <div>
                    <div class="flex justify-between text-xs text-slate-400 mb-1">
                        <span>Progress Pertemuan</span>
                        <span>{{ $pct }}%</span>
                    </div>
                    <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-2 {{ $w }} rounded-full progress-bar" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
            </div>

            {{-- Card Actions --}}
            <div class="px-4 pb-4 mt-auto">
                <div class="grid grid-cols-4 gap-1.5">
                    <a href="{{ route('dosen.pertemuan.index', $k) }}"
                        class="flex flex-col items-center gap-1 py-2 rounded-xl bg-slate-50 hover:bg-ink-50 hover:text-ink-600 text-slate-500 transition-colors text-xs font-medium">
                        <i class="fa-solid fa-calendar-days text-sm"></i>
                        <span>Pertemuan</span>
                    </a>
                    <a href="{{ route('dosen.materi.index', $k) }}"
                        class="flex flex-col items-center gap-1 py-2 rounded-xl bg-slate-50 hover:bg-ink-50 hover:text-ink-600 text-slate-500 transition-colors text-xs font-medium">
                        <i class="fa-solid fa-folder-open text-sm"></i>
                        <span>Materi</span>
                    </a>
                    <a href="{{ route('dosen.rps.show', $k) }}"
                        class="flex flex-col items-center gap-1 py-2 rounded-xl bg-slate-50 hover:bg-ink-50 hover:text-ink-600 text-slate-500 transition-colors text-xs font-medium">
                        <i class="fa-solid fa-file-lines text-sm"></i>
                        <span>RPS</span>
                    </a>
                    <a href="{{ route('dosen.tugas.index', $k) }}"
                        class="flex flex-col items-center gap-1 py-2 rounded-xl bg-slate-50 hover:bg-ember-50 hover:text-ember-600 text-slate-500 transition-colors text-xs font-medium">
                        <i class="fa-solid fa-list-check text-sm"></i>
                        <span>Tugas</span>
                    </a>
                    <a href="{{ route('dosen.ujian.index', $k) }}"
                        class="flex flex-col items-center gap-1 py-2 rounded-xl bg-slate-50 hover:bg-purple-50 hover:text-purple-600 text-slate-500 transition-colors text-xs font-medium">
                        <i class="fa-solid fa-clipboard-question text-sm"></i>
                        <span>Ujian</span>
                    </a>
                    <a href="{{ route('dosen.presensi.index', $k) }}"
                        class="flex flex-col items-center gap-1 py-2 rounded-xl bg-slate-50 hover:bg-sage-50 hover:text-sage-600 text-slate-500 transition-colors text-xs font-medium">
                        <i class="fa-solid fa-user-check text-sm"></i>
                        <span>Presensi</span>
                    </a>
                    <a href="{{ route('dosen.penilaian.index', $k) }}"
                        class="flex flex-col items-center gap-1 py-2 rounded-xl bg-slate-50 hover:bg-ink-50 hover:text-ink-600 text-slate-500 transition-colors text-xs font-medium">
                        <i class="fa-solid fa-star-half-stroke text-sm"></i>
                        <span>Penilaian</span>
                    </a>
                    <a href="{{ route('dosen.capaian-cpmk.index', $k) }}"
                        class="flex flex-col items-center gap-1 py-2 rounded-xl bg-slate-50 hover:bg-sage-50 hover:text-sage-600 text-slate-500 transition-colors text-xs font-medium">
                        <i class="fa-solid fa-bullseye text-sm"></i>
                        <span>Capaian</span>
                    </a>
                    <a href="{{ route('dosen.pengumuman.index', $k) }}"
                        class="flex flex-col items-center gap-1 py-2 rounded-xl bg-slate-50 hover:bg-ember-50 hover:text-ember-600 text-slate-500 transition-colors text-xs font-medium">
                        <i class="fa-solid fa-bullhorn text-sm"></i>
                        <span>Pengumuman</span>
                    </a>
                    <a href="{{ route('dosen.forum.index', $k) }}"
                        class="flex flex-col items-center gap-1 py-2 rounded-xl bg-slate-50 hover:bg-ink-50 hover:text-ink-600 text-slate-500 transition-colors text-xs font-medium">
                        <i class="fa-solid fa-comments text-sm"></i>
                        <span>Forum</span>
                    </a>
                    <a href="{{ route('dosen.laporan.index', $k) }}"
                        class="flex flex-col items-center gap-1 py-2 rounded-xl bg-slate-50 hover:bg-red-50 hover:text-red-600 text-slate-500 transition-colors text-xs font-medium">
                        <i class="fa-solid fa-file-export text-sm"></i>
                        <span>Laporan</span>
                    </a>
                    <a href="{{ route('dosen.bank-soal.index') }}"
                        class="flex flex-col items-center gap-1 py-2 rounded-xl bg-slate-50 hover:bg-ink-50 hover:text-ink-600 text-slate-500 transition-colors text-xs font-medium">
                        <i class="fa-solid fa-pen-to-square text-sm"></i>
                        <span>Bank Soal</span>
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

</x-app-layout>
