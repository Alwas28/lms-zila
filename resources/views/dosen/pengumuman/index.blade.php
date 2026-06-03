<x-app-layout>
    <x-slot name="title">Pengumuman — {{ $kelas->mataKuliah->nama }}</x-slot>
    <x-slot name="pageTitle">Pengumuman</x-slot>

    @include('admin._flash')

    {{-- Breadcrumb --}}
    <div class="mb-6">
        <nav class="flex items-center gap-1.5 text-xs text-slate-400 mb-1">
            <a href="{{ route('dosen.mata-kuliah.index') }}" class="hover:text-ink-500">Mata Kuliah Saya</a>
            <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
            <span class="text-slate-600">{{ $kelas->mataKuliah->nama }}</span>
            <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
            <span class="text-ink-500 font-medium">Pengumuman</span>
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
                <i class="fa-solid fa-plus text-xs"></i> Pengumuman Baru
            </button>
        </div>
    </div>

    {{-- List --}}
    @if($pengumuman->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 p-16 text-center">
        <div class="w-16 h-16 rounded-2xl bg-ember-50 flex items-center justify-center mx-auto mb-4">
            <i class="fa-solid fa-bullhorn text-ember-300 text-2xl"></i>
        </div>
        <p class="font-heading font-semibold text-slate-600 mb-1">Belum ada pengumuman</p>
        <p class="text-slate-400 text-sm">Buat pengumuman untuk mahasiswa kelas ini.</p>
    </div>
    @else
    <div class="space-y-3">
        @foreach($pengumuman as $p)
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
            <div class="px-5 py-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1 flex-wrap">
                            @if($p->is_pinned)
                            <span class="text-xs bg-ember-50 text-ember-600 px-2 py-0.5 rounded-full font-medium">
                                <i class="fa-solid fa-thumbtack text-xs mr-0.5"></i>Disematkan
                            </span>
                            @endif
                            <h3 class="font-heading font-semibold text-slate-800">{{ $p->judul }}</h3>
                        </div>
                        <p class="text-xs text-slate-400 mb-3">
                            <i class="fa-regular fa-clock mr-1"></i>{{ $p->created_at->isoFormat('D MMM YYYY, HH:mm') }}
                            &middot; {{ $p->penulis->name }}
                        </p>
                        <div id="isi-{{ $p->id }}">
                            <p class="text-sm text-slate-600 leading-relaxed whitespace-pre-line">{{ $p->isi }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1 flex-shrink-0">
                        <button onclick="openEditModal({{ $p->id }}, {{ json_encode($p->judul) }}, {{ json_encode($p->isi) }}, {{ $p->is_pinned ? 'true' : 'false' }})"
                            class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:bg-ink-50 hover:text-ink-500 transition-colors">
                            <i class="fa-solid fa-pen text-xs"></i>
                        </button>
                        <form method="POST" action="{{ route('dosen.pengumuman.destroy', [$kelas, $p]) }}"
                            onsubmit="return confirm('Hapus pengumuman ini?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-400 hover:bg-red-50 hover:text-red-500 transition-colors">
                                <i class="fa-solid fa-trash text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Modal Buat --}}
    <div id="modal-buat" class="hidden fixed inset-0 z-50 items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" onclick="closeModal('modal-buat')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-heading font-semibold text-slate-800">Pengumuman Baru</h3>
                <button onclick="closeModal('modal-buat')" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <form action="{{ route('dosen.pengumuman.store', $kelas) }}" method="POST" class="px-6 py-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Judul</label>
                    <input type="text" name="judul" required placeholder="Judul pengumuman..."
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Isi Pengumuman</label>
                    <textarea name="isi" rows="6" required placeholder="Tulis isi pengumuman di sini..."
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200 resize-none"></textarea>
                </div>
                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="checkbox" name="is_pinned" value="1" class="w-4 h-4 rounded accent-ink-500">
                    <span class="text-sm text-slate-600">Sematkan pengumuman ini</span>
                </label>
                <div class="flex gap-3 pt-1">
                    <button type="button" onclick="closeModal('modal-buat')"
                        class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50">Batal</button>
                    <button type="submit"
                        class="flex-1 px-4 py-2.5 rounded-xl bg-ink-500 hover:bg-ink-600 text-white text-sm font-medium transition-colors">
                        <i class="fa-solid fa-bullhorn mr-1.5 text-xs"></i>Terbitkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit --}}
    <div id="modal-edit" class="hidden fixed inset-0 z-50 items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" onclick="closeModal('modal-edit')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-heading font-semibold text-slate-800">Edit Pengumuman</h3>
                <button onclick="closeModal('modal-edit')" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <form id="form-edit" method="POST" class="px-6 py-5 space-y-4">
                @csrf @method('PATCH')
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Judul</label>
                    <input type="text" id="edit-judul" name="judul" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Isi Pengumuman</label>
                    <textarea id="edit-isi" name="isi" rows="6" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200 resize-none"></textarea>
                </div>
                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="checkbox" id="edit-pinned" name="is_pinned" value="1" class="w-4 h-4 rounded accent-ink-500">
                    <span class="text-sm text-slate-600">Sematkan pengumuman ini</span>
                </label>
                <div class="flex gap-3 pt-1">
                    <button type="button" onclick="closeModal('modal-edit')"
                        class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50">Batal</button>
                    <button type="submit"
                        class="flex-1 px-4 py-2.5 rounded-xl bg-ink-500 hover:bg-ink-600 text-white text-sm font-medium transition-colors">
                        <i class="fa-solid fa-floppy-disk mr-1.5 text-xs"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function openModal(id)  { const el = document.getElementById(id); el.classList.remove('hidden'); el.classList.add('flex'); }
        function closeModal(id) { const el = document.getElementById(id); el.classList.add('hidden'); el.classList.remove('flex'); }

        function openEditModal(id, judul, isi, pinned) {
            document.getElementById('edit-judul').value  = judul;
            document.getElementById('edit-isi').value    = isi;
            document.getElementById('edit-pinned').checked = pinned;
            document.getElementById('form-edit').action = `/dosen/kelas/{{ $kelas->id }}/pengumuman/${id}`;
            openModal('modal-edit');
        }
    </script>
    @endpush
</x-app-layout>
