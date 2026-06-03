<x-app-layout>
    <x-slot name="title">{{ $topik->judul }}</x-slot>
    <x-slot name="pageTitle">Forum Diskusi</x-slot>

    {{-- ── Chat header ──────────────────────────────────────── --}}
    <div class="flex items-center gap-3 mb-4 -mt-1">
        <a href="{{ route('dosen.forum.index', $kelas) }}"
            class="w-9 h-9 rounded-xl border border-slate-200 bg-white flex items-center justify-center text-slate-500 hover:bg-slate-50 flex-shrink-0 transition-colors">
            <i class="fa-solid fa-arrow-left text-sm"></i>
        </a>
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
                <h2 class="font-heading font-semibold text-slate-800 text-sm truncate">{{ $topik->judul }}</h2>
                @if($topik->is_pinned)
                <span class="text-xs bg-ember-50 text-ember-600 px-2 py-0.5 rounded-full font-medium flex-shrink-0">
                    <i class="fa-solid fa-thumbtack text-xs mr-0.5"></i>Disematkan
                </span>
                @endif
                @if($topik->is_locked)
                <span class="text-xs bg-slate-100 text-slate-500 px-2 py-0.5 rounded-full font-medium flex-shrink-0">
                    <i class="fa-solid fa-lock text-xs mr-0.5"></i>Terkunci
                </span>
                @endif
            </div>
            <p class="text-xs text-slate-400 mt-0.5">{{ $kelas->mataKuliah->nama }} — Kelas {{ $kelas->nama_kelas }}</p>
        </div>
        {{-- Action buttons --}}
        <div class="flex items-center gap-1 flex-shrink-0">
            <form method="POST" action="{{ route('dosen.forum.pin', [$kelas, $topik]) }}">
                @csrf @method('PATCH')
                <button type="submit" title="{{ $topik->is_pinned ? 'Lepas sematan' : 'Sematkan' }}"
                    class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:bg-ember-50 hover:text-ember-500 transition-colors">
                    <i class="fa-solid fa-thumbtack text-xs"></i>
                </button>
            </form>
            <form method="POST" action="{{ route('dosen.forum.lock', [$kelas, $topik]) }}">
                @csrf @method('PATCH')
                <button type="submit" title="{{ $topik->is_locked ? 'Buka kunci' : 'Kunci' }}"
                    class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:bg-slate-100 transition-colors">
                    <i class="fa-solid {{ $topik->is_locked ? 'fa-lock-open' : 'fa-lock' }} text-xs"></i>
                </button>
            </form>
            <form method="POST" action="{{ route('dosen.forum.destroy', [$kelas, $topik]) }}"
                onsubmit="return confirm('Hapus topik ini beserta semua balasannya?')">
                @csrf @method('DELETE')
                <button type="submit"
                    class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:bg-red-50 hover:text-red-500 transition-colors">
                    <i class="fa-solid fa-trash text-xs"></i>
                </button>
            </form>
        </div>
    </div>

    {{-- ── Chat container ───────────────────────────────────── --}}
    <div class="bg-slate-50 rounded-2xl border border-slate-200 overflow-hidden flex flex-col" style="height: calc(100vh - 220px); min-height: 500px;">

        {{-- Topik awal (pinned header) --}}
        <div class="bg-white border-b border-slate-200 px-5 py-4">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-full {{ $topik->penulis->isDosen() ? 'bg-ink-600' : 'bg-sage-500' }} flex items-center justify-center text-white text-xs font-semibold flex-shrink-0">
                    {{ strtoupper(substr($topik->penulis->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                        <span class="text-xs font-semibold text-slate-700">{{ $topik->penulis->name }}</span>
                        @if($topik->penulis->isDosen())
                        <span class="text-xs bg-ink-50 text-ink-600 px-1.5 py-0.5 rounded-full font-medium">Dosen</span>
                        @endif
                        <span class="text-xs text-slate-400">{{ $topik->created_at->isoFormat('D MMM, HH:mm') }}</span>
                    </div>
                    <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">{{ $topik->isi }}</p>
                </div>
            </div>
        </div>

        {{-- Pesan / komentar --}}
        <div id="chat-messages" class="flex-1 overflow-y-auto px-4 py-4 space-y-3">

            @if($topik->komentar->isEmpty())
            <div class="flex items-center justify-center h-full">
                <div class="text-center">
                    <i class="fa-regular fa-comment-dots text-slate-300 text-4xl mb-2"></i>
                    <p class="text-sm text-slate-400">Belum ada balasan. Mulai diskusi!</p>
                </div>
            </div>
            @else
            @foreach($topik->komentar as $k)
            @php $isMe = $k->user_id === auth()->id(); @endphp

            <div class="flex items-end gap-2 {{ $isMe ? 'flex-row-reverse' : '' }} group">
                {{-- Avatar --}}
                <div class="w-7 h-7 rounded-full flex items-center justify-center text-white text-xs font-semibold flex-shrink-0 mb-5
                    {{ $k->penulis->isDosen() ? 'bg-ink-600' : 'bg-sage-500' }}">
                    {{ strtoupper(substr($k->penulis->name, 0, 1)) }}
                </div>

                <div class="max-w-[72%] {{ $isMe ? 'items-end' : 'items-start' }} flex flex-col">
                    {{-- Name + role --}}
                    @if(!$isMe)
                    <div class="flex items-center gap-1.5 mb-1 {{ $isMe ? 'justify-end' : '' }} px-1">
                        <span class="text-xs font-semibold text-slate-600">{{ $k->penulis->name }}</span>
                        @if($k->penulis->isDosen())
                        <span class="text-xs bg-ink-50 text-ink-600 px-1.5 py-0.5 rounded-full font-medium">Dosen</span>
                        @endif
                    </div>
                    @endif

                    {{-- Bubble --}}
                    <div class="relative {{ $isMe ? 'bg-ink-500 text-white rounded-2xl rounded-br-sm' : 'bg-white border border-slate-200 text-slate-800 rounded-2xl rounded-bl-sm' }} px-4 py-2.5 shadow-sm">
                        <p class="text-sm leading-relaxed whitespace-pre-line">{{ $k->isi }}</p>
                        {{-- Delete button (on hover) --}}
                        <form method="POST" action="{{ route('dosen.forum.komentar.destroy', [$kelas, $topik, $k]) }}"
                            onsubmit="return confirm('Hapus pesan ini?')"
                            class="absolute -top-2.5 {{ $isMe ? 'left-0' : 'right-0' }} opacity-0 group-hover:opacity-100 transition-opacity">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="w-6 h-6 rounded-lg bg-red-500 text-white flex items-center justify-center shadow-sm hover:bg-red-600 transition-colors">
                                <i class="fa-solid fa-xmark text-xs"></i>
                            </button>
                        </form>
                    </div>

                    {{-- Time --}}
                    <span class="text-xs text-slate-400 mt-1 px-1">{{ $k->created_at->isoFormat('HH:mm') }} · {{ $k->created_at->diffForHumans() }}</span>
                </div>
            </div>
            @endforeach
            @endif
        </div>

        {{-- Input bar --}}
        @if(!$topik->is_locked)
        <div class="bg-white border-t border-slate-200 px-4 py-3">
            <form action="{{ route('dosen.forum.komentar.store', [$kelas, $topik]) }}" method="POST"
                class="flex items-end gap-2">
                @csrf
                {{-- Own avatar --}}
                <div class="w-8 h-8 rounded-full bg-ink-600 flex items-center justify-center text-white text-xs font-semibold flex-shrink-0 mb-0.5">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="flex-1">
                    <textarea name="isi" id="msg-input" rows="1" required
                        placeholder="Tulis pesan..."
                        class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200 resize-none bg-slate-50 focus:bg-white transition-colors"
                        style="max-height: 120px;"
                        oninput="autoResize(this)"
                        onkeydown="submitOnEnter(event, this.form)"></textarea>
                </div>
                <button type="submit"
                    class="w-10 h-10 rounded-2xl bg-ink-500 hover:bg-ink-600 flex items-center justify-center text-white flex-shrink-0 transition-colors shadow-sm mb-0.5">
                    <i class="fa-solid fa-paper-plane text-sm"></i>
                </button>
            </form>
        </div>
        @else
        <div class="bg-white border-t border-slate-200 px-4 py-3 flex items-center gap-2 text-slate-400">
            <i class="fa-solid fa-lock text-sm"></i>
            <p class="text-sm">Topik ini dikunci. Balasan tidak bisa ditambahkan.</p>
        </div>
        @endif

    </div>

    @push('scripts')
    <script>
        // Auto-scroll ke pesan terbaru
        const chat = document.getElementById('chat-messages');
        if (chat) chat.scrollTop = chat.scrollHeight;

        // Auto-resize textarea
        function autoResize(el) {
            el.style.height = 'auto';
            el.style.height = Math.min(el.scrollHeight, 120) + 'px';
        }

        // Kirim dengan Enter (Shift+Enter = newline)
        function submitOnEnter(e, form) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                if (form.querySelector('[name="isi"]').value.trim()) form.submit();
            }
        }
    </script>
    @endpush
</x-app-layout>
