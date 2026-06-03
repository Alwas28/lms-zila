<x-app-layout>
    <x-slot name="title">Forum — {{ $kelas->mataKuliah->nama }}</x-slot>
    <x-slot name="pageTitle">Forum Diskusi</x-slot>

    @include('admin._flash')

    {{-- Breadcrumb --}}
    <div class="mb-6">
        <nav class="flex items-center gap-1.5 text-xs text-slate-400 mb-1">
            <a href="{{ route('dosen.mata-kuliah.index') }}" class="hover:text-ink-500">Mata Kuliah Saya</a>
            <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
            <span class="text-slate-600">{{ $kelas->mataKuliah->nama }}</span>
            <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
            <span class="text-ink-500 font-medium">Forum Diskusi</span>
        </nav>
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="font-heading font-semibold text-slate-800 text-lg">
                    {{ $kelas->mataKuliah->nama }}
                    <span class="text-slate-400 font-normal text-sm">— Kelas {{ $kelas->nama_kelas }}</span>
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Semester {{ ucfirst($kelas->semester->tipe) }} — {{ $kelas->semester->tahunAkademik->nama }}</p>
            </div>
            <button onclick="openModal('modal-buat')"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-ink-500 hover:bg-ink-600 text-white text-sm font-medium transition-colors">
                <i class="fa-solid fa-plus text-xs"></i> Buat Topik
            </button>
        </div>
    </div>

    {{-- Topik list --}}
    @if($topik->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 p-16 text-center">
        <div class="w-16 h-16 rounded-2xl bg-ink-50 flex items-center justify-center mx-auto mb-4">
            <i class="fa-solid fa-comments text-ink-300 text-2xl"></i>
        </div>
        <p class="font-heading font-semibold text-slate-600 mb-1">Belum ada diskusi</p>
        <p class="text-slate-400 text-sm">Mulai dengan membuat topik diskusi pertama.</p>
    </div>
    @else
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="divide-y divide-slate-50">
            @foreach($topik as $t)
            <div class="flex items-start gap-4 px-5 py-4 hover:bg-slate-50/50 transition-colors group">
                {{-- Avatar --}}
                <div class="w-9 h-9 rounded-xl bg-ink-600 flex items-center justify-center text-white font-semibold text-sm flex-shrink-0 mt-0.5">
                    {{ strtoupper(substr($t->penulis->name, 0, 1)) }}
                </div>
                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap mb-0.5">
                        @if($t->is_pinned)
                        <span class="text-xs bg-ember-50 text-ember-600 px-2 py-0.5 rounded-full font-medium">
                            <i class="fa-solid fa-thumbtack text-xs mr-0.5"></i>Disematkan
                        </span>
                        @endif
                        @if($t->is_locked)
                        <span class="text-xs bg-slate-100 text-slate-500 px-2 py-0.5 rounded-full font-medium">
                            <i class="fa-solid fa-lock text-xs mr-0.5"></i>Terkunci
                        </span>
                        @endif
                        <a href="{{ route('dosen.forum.show', [$kelas, $t]) }}"
                            class="font-heading font-semibold text-slate-800 hover:text-ink-600 transition-colors text-sm leading-snug">
                            {{ $t->judul }}
                        </a>
                    </div>
                    <div class="flex items-center gap-3 text-xs text-slate-400 mt-1">
                        <span>{{ $t->penulis->name }}</span>
                        <span>&middot;</span>
                        <span>{{ $t->created_at->diffForHumans() }}</span>
                        <span>&middot;</span>
                        <span><i class="fa-regular fa-comment mr-0.5"></i>{{ $t->komentar->count() }} balasan</span>
                    </div>
                </div>
                {{-- Actions --}}
                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">
                    <form method="POST" action="{{ route('dosen.forum.pin', [$kelas, $t]) }}">
                        @csrf @method('PATCH')
                        <button type="submit" title="{{ $t->is_pinned ? 'Lepas sematan' : 'Sematkan' }}"
                            class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:bg-ember-50 hover:text-ember-500 transition-colors">
                            <i class="fa-solid fa-thumbtack text-xs"></i>
                        </button>
                    </form>
                    <form method="POST" action="{{ route('dosen.forum.lock', [$kelas, $t]) }}">
                        @csrf @method('PATCH')
                        <button type="submit" title="{{ $t->is_locked ? 'Buka kunci' : 'Kunci' }}"
                            class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors">
                            <i class="fa-solid {{ $t->is_locked ? 'fa-lock-open' : 'fa-lock' }} text-xs"></i>
                        </button>
                    </form>
                    <form method="POST" action="{{ route('dosen.forum.destroy', [$kelas, $t]) }}"
                        onsubmit="return confirm('Hapus topik ini beserta semua balasannya?')">
                        @csrf @method('DELETE')
                        <button type="submit"
                            class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:bg-red-50 hover:text-red-500 transition-colors">
                            <i class="fa-solid fa-trash text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Modal Buat Topik --}}
    <div id="modal-buat" class="hidden fixed inset-0 z-50 items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" onclick="closeModal('modal-buat')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-heading font-semibold text-slate-800">Buat Topik Baru</h3>
                <button onclick="closeModal('modal-buat')" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <form action="{{ route('dosen.forum.store', $kelas) }}" method="POST" class="px-6 py-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Judul Topik</label>
                    <input type="text" name="judul" required placeholder="Tuliskan judul diskusi..."
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Isi / Pertanyaan</label>
                    <textarea name="isi" rows="5" required placeholder="Jelaskan topik atau pertanyaan Anda..."
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200 resize-none"></textarea>
                </div>
                <div class="flex gap-3 pt-1">
                    <button type="button" onclick="closeModal('modal-buat')"
                        class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50">Batal</button>
                    <button type="submit"
                        class="flex-1 px-4 py-2.5 rounded-xl bg-ink-500 hover:bg-ink-600 text-white text-sm font-medium transition-colors">
                        <i class="fa-solid fa-paper-plane mr-1.5 text-xs"></i>Buat Topik
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function openModal(id)  { const el = document.getElementById(id); el.classList.remove('hidden'); el.classList.add('flex'); }
        function closeModal(id) { const el = document.getElementById(id); el.classList.add('hidden'); el.classList.remove('flex'); }
    </script>
    @endpush
</x-app-layout>
