<x-app-layout>
    <x-slot name="title">Mata Kuliah Saya</x-slot>
    <x-slot name="pageTitle">Mata Kuliah Saya</x-slot>

    <div class="mb-6">
        <h2 class="font-heading font-semibold text-slate-800 text-lg">Mata Kuliah Saya</h2>
        <p class="text-xs text-slate-400 mt-0.5">Daftar mata kuliah yang Anda ikuti semester ini</p>
    </div>

    @if($kelas->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 p-16 text-center">
        <div class="w-20 h-20 rounded-2xl bg-ink-50 flex items-center justify-center mx-auto mb-4">
            <i class="fa-solid fa-book-open text-ink-300 text-3xl"></i>
        </div>
        <p class="font-heading font-semibold text-slate-600 text-lg mb-1">Belum terdaftar</p>
        <p class="text-slate-400 text-sm">Anda belum terdaftar di kelas manapun.</p>
    </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($kelas as $k)
        @php
            $warna = ['bg-ink-500','bg-sage-500','bg-ember-500','bg-blue-500','bg-purple-500'];
            $w = $warna[$loop->index % count($warna)];
        @endphp
        <div class="bg-white rounded-2xl border border-slate-200 card-hover overflow-hidden flex flex-col">
            <div class="px-5 pt-5 pb-4 flex-1">
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
                </div>
                <p class="text-xs text-slate-400 mb-3">
                    <i class="fa-solid fa-calendar-alt mr-1"></i>
                    Semester {{ ucfirst($k->semester->tipe) }} — {{ $k->semester->tahunAkademik->nama }}
                </p>
                @if($k->koordinator())
                <p class="text-xs text-slate-500 mb-3">
                    <i class="fa-solid fa-chalkboard-user mr-1 text-slate-400"></i>
                    {{ $k->koordinator()->name }}
                </p>
                @endif

                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-1.5 text-xs {{ $k->pengumuman_count > 0 ? 'text-ember-600' : 'text-slate-400' }}">
                        <i class="fa-solid fa-bullhorn"></i>
                        <span>{{ $k->pengumuman_count }} pengumuman</span>
                    </div>
                    <div class="flex items-center gap-1.5 text-xs text-slate-400">
                        <i class="fa-solid fa-comments"></i>
                        <span>{{ $k->forum_topik_count }} diskusi</span>
                    </div>
                </div>
            </div>

            <div class="px-4 pb-4">
                <div class="grid grid-cols-4 gap-1.5">
                    <a href="{{ route('mahasiswa.materi.index', $k) }}"
                        class="flex flex-col items-center gap-1 py-2 rounded-xl bg-slate-50 hover:bg-blue-50 hover:text-blue-600 text-slate-500 transition-colors text-xs font-medium">
                        <i class="fa-solid fa-folder-open text-sm"></i><span>Materi</span>
                    </a>
                    <a href="{{ route('mahasiswa.tugas.index', $k) }}"
                        class="flex flex-col items-center gap-1 py-2 rounded-xl bg-slate-50 hover:bg-ink-50 hover:text-ink-600 text-slate-500 transition-colors text-xs font-medium">
                        <i class="fa-solid fa-list-check text-sm"></i><span>Tugas</span>
                    </a>
                    <a href="{{ route('mahasiswa.ujian.index', $k) }}"
                        class="flex flex-col items-center gap-1 py-2 rounded-xl bg-slate-50 hover:bg-purple-50 hover:text-purple-600 text-slate-500 transition-colors text-xs font-medium">
                        <i class="fa-solid fa-clipboard-question text-sm"></i><span>Ujian</span>
                    </a>
                    <a href="{{ route('mahasiswa.presensi.index', $k) }}"
                        class="flex flex-col items-center gap-1 py-2 rounded-xl bg-slate-50 hover:bg-sage-50 hover:text-sage-600 text-slate-500 transition-colors text-xs font-medium">
                        <i class="fa-solid fa-user-check text-sm"></i><span>Presensi</span>
                    </a>
                    <a href="{{ route('mahasiswa.nilai.index', $k) }}"
                        class="flex flex-col items-center gap-1 py-2 rounded-xl bg-slate-50 hover:bg-ember-50 hover:text-ember-600 text-slate-500 transition-colors text-xs font-medium">
                        <i class="fa-solid fa-chart-bar text-sm"></i><span>Nilai</span>
                    </a>
                    <a href="{{ route('mahasiswa.capaian-cpmk.index', $k) }}"
                        class="flex flex-col items-center gap-1 py-2 rounded-xl bg-slate-50 hover:bg-sage-50 hover:text-sage-600 text-slate-500 transition-colors text-xs font-medium">
                        <i class="fa-solid fa-bullseye text-sm"></i><span>Capaian</span>
                    </a>
                    <a href="{{ route('mahasiswa.forum.index', $k) }}"
                        class="flex flex-col items-center gap-1 py-2 rounded-xl bg-slate-50 hover:bg-ink-50 hover:text-ink-600 text-slate-500 transition-colors text-xs font-medium">
                        <i class="fa-solid fa-comments text-sm"></i><span>Forum</span>
                    </a>
                    <a href="{{ route('mahasiswa.pengumuman.index', $k) }}"
                        class="flex flex-col items-center gap-1 py-2 rounded-xl bg-slate-50 hover:bg-ember-50 hover:text-ember-600 text-slate-500 transition-colors text-xs font-medium">
                        <i class="fa-solid fa-bullhorn text-sm"></i><span>Pengumuman</span>
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</x-app-layout>
