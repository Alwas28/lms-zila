<x-app-layout>
    <x-slot name="title">Pengampu Mata Kuliah</x-slot>
    <x-slot name="pageTitle">Pengampu Mata Kuliah</x-slot>

    @include('admin._flash')

    {{-- Filter --}}
    <div class="flex flex-wrap items-center gap-3 mb-5">
        <form method="GET" action="{{ route('admin.pengampu.index') }}" class="flex items-center gap-2">
            <label class="text-sm text-slate-500 flex-shrink-0">Semester:</label>
            <select name="semester_id" onchange="this.form.submit()"
                class="px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200 bg-white text-slate-700">
                <option value="">Semua</option>
                @foreach($semua as $sm)
                <option value="{{ $sm->id }}" {{ $semesterId == $sm->id ? 'selected' : '' }}>
                    Semester {{ ucfirst($sm->tipe) }} &mdash; {{ $sm->tahunAkademik->nama }}{{ $sm->is_aktif ? ' (Aktif)' : '' }}
                </option>
                @endforeach
            </select>
        </form>
        <form method="GET" action="{{ route('admin.pengampu.index') }}" class="flex-1 max-w-xs">
            <input type="hidden" name="semester_id" value="{{ $semesterId }}">
            <div class="relative">
                <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kelas..."
                    class="w-full pl-9 pr-4 py-2.5 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200">
            </div>
        </form>
    </div>

    {{-- Daftar Kelas beserta Pengampu --}}
    <div class="space-y-3">
        @forelse($kelas as $k)
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
            {{-- Header Kelas --}}
            <div class="flex items-center justify-between px-5 py-3.5 bg-slate-50 border-b border-slate-100">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-xl bg-ink-100 flex items-center justify-center flex-shrink-0 font-heading font-bold text-ink-600 text-sm">
                        {{ $k->nama_kelas }}
                    </div>
                    <div class="min-w-0">
                        <p class="font-heading font-semibold text-slate-800 text-sm truncate">{{ $k->mataKuliah->nama }}</p>
                        <p class="text-xs text-slate-400">{{ $k->mataKuliah->kode }} · {{ ucfirst($k->semester->tipe) }} {{ $k->semester->tahunAkademik->nama }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <span class="text-xs text-slate-400">{{ $k->dosen_count }} pengampu</span>
                    <button onclick="openTambahPengampu({{ $k->id }}, '{{ addslashes($k->nama_lengkap) }}')"
                        class="inline-flex items-center gap-1.5 bg-ink-500 hover:bg-ink-600 text-white text-xs font-medium px-3 py-1.5 rounded-xl transition-colors">
                        <i class="fa-solid fa-plus text-xs"></i> Tambah Dosen
                    </button>
                </div>
            </div>

            {{-- Daftar Pengampu --}}
            @if($k->dosen->count() > 0)
            <div class="divide-y divide-slate-50">
                @foreach($k->dosen as $d)
                <div class="flex items-center gap-3 px-5 py-3">
                    <div class="w-8 h-8 rounded-xl bg-ink-100 flex items-center justify-center flex-shrink-0 font-semibold text-ink-600 text-xs">
                        {{ strtoupper(substr($d->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-700 truncate">{{ $d->name }}</p>
                        <p class="text-xs text-slate-400 truncate">{{ $d->nip_nim ?? $d->email }}</p>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        @if($d->pivot->is_koordinator)
                        <span class="text-xs bg-ink-50 text-ink-600 px-2.5 py-1 rounded-full font-medium">Koordinator</span>
                        @else
                        <form action="{{ route('admin.pengampu.koordinator', $d->pivot->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="text-xs bg-slate-100 hover:bg-ink-50 hover:text-ink-600 text-slate-500 px-2.5 py-1 rounded-full font-medium transition-colors">
                                Jadikan Koordinator
                            </button>
                        </form>
                        @endif
                        <form action="{{ route('admin.pengampu.destroy', $d->pivot->id) }}" method="POST"
                            onsubmit="return confirm('Hapus {{ addslashes($d->name) }} dari pengampu?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-7 h-7 rounded-lg bg-red-50 hover:bg-red-100 text-red-400 flex items-center justify-center transition-colors">
                                <i class="fa-solid fa-xmark text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="px-5 py-5 text-center">
                <p class="text-xs text-slate-400">Belum ada dosen pengampu. Klik "Tambah Dosen" untuk menetapkan.</p>
            </div>
            @endif
        </div>
        @empty
        <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
            <i class="fa-solid fa-user-tie text-slate-200 text-4xl mb-3 block"></i>
            <p class="text-slate-400 text-sm">Tidak ada kelas ditemukan untuk filter ini.</p>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($kelas->hasPages())
    <div class="mt-4 flex items-center justify-between text-sm text-slate-500">
        <span>{{ $kelas->firstItem() }}"“{{ $kelas->lastItem() }} dari {{ $kelas->total() }} kelas</span>
        <div class="flex gap-1">
            @if(!$kelas->onFirstPage())<a href="{{ $kelas->previousPageUrl() }}" class="w-8 h-8 rounded-lg bg-white border border-slate-200 hover:bg-ink-50 hover:text-ink-500 flex items-center justify-center"><i class="fa-solid fa-chevron-left text-xs"></i></a>@endif
            @foreach($kelas->getUrlRange(max(1,$kelas->currentPage()-2),min($kelas->lastPage(),$kelas->currentPage()+2)) as $page=>$url)
            <a href="{{ $url }}" class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-medium {{ $page==$kelas->currentPage() ? 'bg-ink-500 text-white' : 'bg-white border border-slate-200 hover:bg-ink-50 hover:text-ink-500 text-slate-600' }}">{{ $page }}</a>
            @endforeach
            @if($kelas->hasMorePages())<a href="{{ $kelas->nextPageUrl() }}" class="w-8 h-8 rounded-lg bg-white border border-slate-200 hover:bg-ink-50 hover:text-ink-500 flex items-center justify-center"><i class="fa-solid fa-chevron-right text-xs"></i></a>@endif
        </div>
    </div>
    @endif

    {{-- Modal Tambah Pengampu --}}
    <div id="modal-tambah-pengampu" class="hidden fixed inset-0 z-50 items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" onclick="closeModal('modal-tambah-pengampu')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-heading font-semibold text-slate-800">Tambah Pengampu</h3>
                    <p id="modal-kelas-label" class="text-xs text-slate-400 mt-0.5"></p>
                </div>
                <button onclick="closeModal('modal-tambah-pengampu')" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <form id="form-tambah-pengampu" action="{{ route('admin.pengampu.store') }}" method="POST" class="px-6 py-5 space-y-4">
                @csrf
                <input type="hidden" name="kelas_id" id="input-kelas-id">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Pilih Dosen <span class="text-red-400">*</span></label>
                    <select name="user_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200">
                        <option value="">&mdash; Pilih Dosen &mdash;</option>
                        @foreach($semuaDosen as $d)
                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl">
                    <input type="checkbox" name="is_koordinator" id="cb-koord" value="1" class="w-4 h-4 rounded text-ink-500 accent-ink-500">
                    <label for="cb-koord" class="text-sm text-slate-700 cursor-pointer">
                        Jadikan Koordinator Mata Kuliah
                        <span class="text-xs text-slate-400 block">Koordinator lama akan digantikan otomatis.</span>
                    </label>
                </div>
                <div class="flex gap-3 pt-1">
                    <button type="button" onclick="closeModal('modal-tambah-pengampu')" class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl bg-ink-500 hover:bg-ink-600 text-white text-sm font-medium transition-colors"><i class="fa-solid fa-user-plus mr-1.5 text-xs"></i>Tetapkan</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function openModal(id) { const el = document.getElementById(id); el.classList.remove('hidden'); el.classList.add('flex'); }
        function closeModal(id) { const el = document.getElementById(id); el.classList.add('hidden'); el.classList.remove('flex'); }
        function openTambahPengampu(kelasId, kelasNama) {
            document.getElementById('input-kelas-id').value     = kelasId;
            document.getElementById('modal-kelas-label').textContent = kelasNama;
            openModal('modal-tambah-pengampu');
        }
    </script>
    @endpush
</x-app-layout>
