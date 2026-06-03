<x-app-layout>
    <x-slot name="title">Materi — {{ $kelas->mataKuliah->nama }}</x-slot>
    <x-slot name="pageTitle">Materi Pembelajaran</x-slot>

    <div class="mb-5">
        <nav class="flex items-center gap-1.5 text-xs text-slate-400 mb-1">
            <a href="{{ route('mahasiswa.kelas.index') }}" class="hover:text-ink-500">Mata Kuliah Saya</a>
            <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
            <span class="text-slate-600">{{ $kelas->mataKuliah->nama }}</span>
            <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
            <span class="text-ink-500 font-medium">Materi</span>
        </nav>
        <h2 class="font-heading font-semibold text-slate-800 text-lg">
            {{ $kelas->mataKuliah->nama }}
            <span class="text-slate-400 font-normal text-sm">— Kelas {{ $kelas->nama_kelas }}</span>
        </h2>
    </div>

    @php
        // Kumpulkan semua materi ke flat array dengan data pertemuan
        $allMateri = collect();
        foreach ($pertemuan as $p) {
            foreach ($p->materi as $m) {
                $allMateri->push(['pertemuan' => $p, 'materi' => $m]);
            }
        }

        // Helper: ekstrak YouTube video ID
        function youtubeId(?string $url): ?string {
            if (!$url) return null;
            preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([^&\n?#]+)/', $url, $m);
            return $m[1] ?? null;
        }
    @endphp

    @if($allMateri->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 p-16 text-center">
        <i class="fa-solid fa-folder-open text-slate-200 text-4xl mb-3"></i>
        <p class="text-slate-400 text-sm">Belum ada materi tersedia.</p>
    </div>
    @else

    <div class="flex flex-col lg:flex-row gap-4 h-full">

        {{-- ── PANEL KIRI: Daftar Materi ───────────────────── --}}
        <div class="lg:w-80 xl:w-96 flex-shrink-0">
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden sticky top-4">
                <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                    <p class="font-heading font-semibold text-slate-800 text-sm">Daftar Materi</p>
                    <span class="text-xs text-slate-400">{{ $allMateri->count() }} item</span>
                </div>
                <div class="overflow-y-auto max-h-[calc(100vh-220px)]">
                    @foreach($pertemuan as $p)
                    @if($p->materi->isNotEmpty())
                    {{-- Pertemuan header --}}
                    <div class="px-4 py-2 bg-slate-50 border-b border-slate-100 flex items-center gap-2 sticky top-0 z-10">
                        <div class="w-5 h-5 rounded-md bg-ink-500 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                            {{ $p->nomor }}
                        </div>
                        <p class="text-xs font-semibold text-slate-600 truncate">{{ $p->topik }}</p>
                    </div>
                    {{-- Materi items --}}
                    @foreach($p->materi as $m)
                    @php
                        $tipeColor = match($m->tipe) {
                            'pdf'     => 'text-red-500 bg-red-50',
                            'youtube' => 'text-red-600 bg-red-50',
                            'video'   => 'text-purple-500 bg-purple-50',
                            'audio'   => 'text-emerald-500 bg-emerald-50',
                            'ppt'     => 'text-orange-500 bg-orange-50',
                            'dokumen' => 'text-blue-500 bg-blue-50',
                            default   => 'text-slate-500 bg-slate-100',
                        };
                    @endphp
                    <button
                        onclick="tampilkanMateri('{{ $m->id }}')"
                        id="btn-{{ $m->id }}"
                        class="materi-btn w-full flex items-center gap-3 px-4 py-3 border-b border-slate-50 hover:bg-ink-50/50 transition-colors text-left group">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 {{ $tipeColor }}">
                            <i class="fa-solid {{ $m->icon }} text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-slate-700 group-hover:text-ink-600 truncate leading-snug">{{ $m->judul }}</p>
                            <p class="text-xs text-slate-400 capitalize">{{ $m->tipe }}</p>
                        </div>
                        @if($m->is_wajib)
                        <span class="text-xs text-ember-600 flex-shrink-0">●</span>
                        @endif
                    </button>
                    @endforeach
                    @endif
                    @endforeach
                </div>
                <div class="px-4 py-2 border-t border-slate-100">
                    <p class="text-xs text-slate-400"><span class="text-ember-600 font-medium">●</span> = Wajib dibaca</p>
                </div>
            </div>
        </div>

        {{-- ── PANEL KANAN: Viewer ─────────────────────────── --}}
        <div class="flex-1 min-w-0">

            {{-- Placeholder (tampil saat belum ada yang dipilih) --}}
            <div id="viewer-placeholder" class="bg-white rounded-2xl border border-slate-200 flex flex-col items-center justify-center h-[500px]">
                <i class="fa-solid fa-book-open text-slate-200 text-5xl mb-4"></i>
                <p class="font-heading font-semibold text-slate-500">Pilih materi untuk ditampilkan</p>
                <p class="text-sm text-slate-400 mt-1">Klik salah satu item di panel kiri</p>
            </div>

            {{-- Semua viewer cards (tersembunyi, ditampilkan satu per satu via JS) --}}
            @foreach($pertemuan as $p)
            @foreach($p->materi as $m)
            @php
                $ytId = youtubeId($m->url);
                $fileUrl = $m->file_path ? asset('storage/'.$m->file_path) : null;
                $srcUrl = $fileUrl ?? $m->url;
            @endphp
            <div id="viewer-{{ $m->id }}" class="viewer-panel hidden flex-col gap-4">

                {{-- Header info --}}
                <div class="bg-white rounded-2xl border border-slate-200 px-5 py-4 flex items-center justify-between gap-3">
                    <div class="flex items-start gap-3 flex-1 min-w-0">
                        @php
                            $tipeColor = match($m->tipe) {
                                'pdf'     => 'text-red-500 bg-red-50',
                                'youtube' => 'text-red-600 bg-red-50',
                                'video'   => 'text-purple-500 bg-purple-50',
                                'audio'   => 'text-emerald-500 bg-emerald-50',
                                'ppt'     => 'text-orange-500 bg-orange-50',
                                'dokumen' => 'text-blue-500 bg-blue-50',
                                default   => 'text-slate-500 bg-slate-100',
                            };
                        @endphp
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 {{ $tipeColor }}">
                            <i class="fa-solid {{ $m->icon }}"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-heading font-semibold text-slate-800">{{ $m->judul }}</h3>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-xs text-slate-400 capitalize">{{ $m->tipe }}</span>
                                <span class="text-slate-200">·</span>
                                <span class="text-xs text-slate-400">Pertemuan {{ $p->nomor }} — {{ $p->topik }}</span>
                                @if($m->is_wajib)
                                <span class="text-xs bg-ember-50 text-ember-600 px-1.5 py-0.5 rounded-full font-medium">Wajib</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    {{-- Download / Open link --}}
                    @if($fileUrl)
                    <a href="{{ $fileUrl }}" download
                        class="flex-shrink-0 flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-medium transition-colors">
                        <i class="fa-solid fa-download text-xs"></i> Unduh
                    </a>
                    @elseif($m->url)
                    <a href="{{ $m->url }}" target="_blank"
                        class="flex-shrink-0 flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-medium transition-colors">
                        <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i> Buka
                    </a>
                    @endif
                </div>

                {{-- Konten viewer --}}
                @if($m->tipe === 'youtube' && $ytId)
                {{-- YouTube embed --}}
                <div class="bg-black rounded-2xl overflow-hidden aspect-video w-full">
                    <iframe
                        src="https://www.youtube.com/embed/{{ $ytId }}"
                        class="w-full h-full"
                        allowfullscreen
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture">
                    </iframe>
                </div>

                @elseif($m->tipe === 'pdf' && $fileUrl)
                {{-- PDF iframe (native browser viewer) --}}
                <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                    <iframe
                        src="{{ $fileUrl }}"
                        class="w-full border-0"
                        style="height: 80vh; min-height: 500px;"
                        loading="lazy">
                    </iframe>
                </div>

                @elseif($m->tipe === 'video' && $fileUrl)
                {{-- HTML5 Video --}}
                <div class="bg-black rounded-2xl overflow-hidden">
                    <video controls class="w-full max-h-[75vh]" preload="metadata">
                        <source src="{{ $fileUrl }}">
                        Browser Anda tidak mendukung pemutaran video.
                    </video>
                </div>

                @elseif($m->tipe === 'audio' && $fileUrl)
                {{-- HTML5 Audio --}}
                <div class="bg-white rounded-2xl border border-slate-200 p-8 flex flex-col items-center gap-4">
                    <div class="w-20 h-20 rounded-2xl bg-emerald-50 flex items-center justify-center">
                        <i class="fa-solid fa-music text-emerald-500 text-3xl"></i>
                    </div>
                    <p class="font-heading font-semibold text-slate-700">{{ $m->judul }}</p>
                    <audio controls class="w-full max-w-md">
                        <source src="{{ $fileUrl }}">
                        Browser Anda tidak mendukung pemutaran audio.
                    </audio>
                </div>

                @elseif(in_array($m->tipe, ['ppt', 'dokumen']) && $fileUrl)
                {{-- Office viewer via Google Docs --}}
                <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                    <iframe
                        src="https://docs.google.com/gview?url={{ urlencode(url('storage/'.$m->file_path)) }}&embedded=true"
                        class="w-full"
                        style="height: 75vh; min-height: 500px;"
                        frameborder="0">
                    </iframe>
                </div>

                @elseif($m->tipe === 'website' && $m->url)
                {{-- Website: tampilkan preview card + link --}}
                <div class="bg-white rounded-2xl border border-slate-200 p-10 flex flex-col items-center gap-4 text-center">
                    <div class="w-20 h-20 rounded-2xl bg-blue-50 flex items-center justify-center">
                        <i class="fa-solid fa-globe text-blue-500 text-3xl"></i>
                    </div>
                    <div>
                        <p class="font-heading font-semibold text-slate-700 mb-1">{{ $m->judul }}</p>
                        <p class="text-sm text-slate-400 break-all">{{ $m->url }}</p>
                    </div>
                    <a href="{{ $m->url }}" target="_blank"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-ink-500 hover:bg-ink-600 text-white text-sm font-medium transition-colors">
                        <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                        Buka Website
                    </a>
                </div>

                @else
                {{-- Fallback --}}
                <div class="bg-white rounded-2xl border border-slate-200 p-10 flex flex-col items-center gap-4 text-center">
                    <div class="w-20 h-20 rounded-2xl bg-slate-50 flex items-center justify-center">
                        <i class="fa-solid fa-file text-slate-300 text-3xl"></i>
                    </div>
                    <p class="text-slate-500 text-sm">Preview tidak tersedia untuk tipe ini.</p>
                    @if($srcUrl)
                    <a href="{{ $srcUrl }}" target="_blank"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-ink-500 hover:bg-ink-600 text-white text-sm font-medium transition-colors">
                        <i class="fa-solid fa-download text-xs"></i> Unduh / Buka File
                    </a>
                    @endif
                </div>
                @endif

            </div>
            @endforeach
            @endforeach

        </div>
    </div>

    @endif

    @push('scripts')
    <script>
        function tampilkanMateri(id) {
            // Sembunyikan semua viewer dan placeholder
            document.getElementById('viewer-placeholder').classList.add('hidden');
            document.querySelectorAll('.viewer-panel').forEach(el => {
                el.classList.add('hidden');
                el.classList.remove('flex');
            });

            // Hapus active state semua tombol
            document.querySelectorAll('.materi-btn').forEach(btn => {
                btn.classList.remove('bg-ink-50', 'border-l-2', 'border-ink-500');
            });

            // Tampilkan viewer yang dipilih
            const viewer = document.getElementById('viewer-' + id);
            if (viewer) { viewer.classList.remove('hidden'); viewer.classList.add('flex'); }

            // Highlight tombol aktif
            const btn = document.getElementById('btn-' + id);
            if (btn) btn.classList.add('bg-ink-50', 'border-l-2', 'border-ink-500');

            // Scroll ke viewer di mobile
            if (window.innerWidth < 1024) {
                viewer?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }

        // Auto-tampilkan materi pertama
        const firstBtn = document.querySelector('.materi-btn');
        if (firstBtn) {
            const firstId = firstBtn.id.replace('btn-', '');
            tampilkanMateri(firstId);
        }
    </script>
    @endpush
</x-app-layout>
