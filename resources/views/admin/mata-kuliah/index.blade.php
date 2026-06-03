<x-app-layout>
    <x-slot name="title">Mata Kuliah</x-slot>
    <x-slot name="pageTitle">Manajemen Mata Kuliah</x-slot>

    @include('admin._flash')

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4 mb-6">
        <div class="bg-white rounded-2xl p-4 md:p-5 border border-slate-200 card-hover">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-ink-50 flex items-center justify-center">
                    <i class="fa-solid fa-book text-ink-500"></i>
                </div>
                <span class="text-xs text-ink-600 bg-ink-50 px-2 py-0.5 rounded-full font-medium badge hidden sm:block">Total Aktif</span>
            </div>
            <p class="text-2xl font-heading font-semibold text-slate-800">{{ $totalAktif }}</p>
            <p class="text-sm text-slate-400 mt-0.5">Mata Kuliah Aktif</p>
        </div>
        <div class="bg-white rounded-2xl p-4 md:p-5 border border-slate-200 card-hover">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center">
                    <i class="fa-solid fa-box-archive text-slate-400"></i>
                </div>
                <span class="text-xs text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full font-medium badge hidden sm:block">Arsip</span>
            </div>
            <p class="text-2xl font-heading font-semibold text-slate-800">{{ $totalArsip }}</p>
            <p class="text-sm text-slate-400 mt-0.5">Diarsipkan</p>
        </div>
        <div class="bg-white rounded-2xl p-4 md:p-5 border border-slate-200 card-hover col-span-2 lg:col-span-1">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-sage-50 flex items-center justify-center">
                    <i class="fa-solid fa-layer-group text-sage-500"></i>
                </div>
                <span class="text-xs text-sage-600 bg-sage-50 px-2 py-0.5 rounded-full font-medium badge hidden sm:block">Total</span>
            </div>
            <p class="text-2xl font-heading font-semibold text-slate-800">{{ $totalAktif + $totalArsip }}</p>
            <p class="text-sm text-slate-400 mt-0.5">Semua Mata Kuliah</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">

        {{-- Toolbar --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-5 py-4 border-b border-slate-100">
            <form method="GET" action="{{ route('admin.mata-kuliah.index') }}" class="flex items-center gap-2 flex-1 max-w-sm">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <div class="relative flex-1">
                    <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari kode atau nama..."
                        class="w-full pl-9 pr-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200 text-slate-700 placeholder:text-slate-400">
                </div>
            </form>
            <button onclick="openModal('modal-tambah')"
                class="inline-flex items-center gap-2 bg-ink-500 hover:bg-ink-600 text-white text-sm font-medium px-4 py-2.5 rounded-xl transition-colors flex-shrink-0">
                <i class="fa-solid fa-plus text-xs"></i> Tambah Mata Kuliah
            </button>
        </div>

        {{-- Tabs Aktif / Arsip --}}
        <div class="flex gap-0 border-b border-slate-100 px-5">
            <a href="{{ route('admin.mata-kuliah.index', array_merge(request()->query(), ['tab'=>'aktif'])) }}"
                class="py-3 px-1 mr-5 text-sm font-medium border-b-2 transition-colors {{ $tab === 'aktif' ? 'border-ink-500 text-ink-600' : 'border-transparent text-slate-400 hover:text-slate-600' }}">
                Aktif <span class="ml-1 text-xs bg-ink-50 text-ink-500 px-1.5 py-0.5 rounded-full">{{ $totalAktif }}</span>
            </a>
            <a href="{{ route('admin.mata-kuliah.index', array_merge(request()->query(), ['tab'=>'arsip'])) }}"
                class="py-3 px-1 text-sm font-medium border-b-2 transition-colors {{ $tab === 'arsip' ? 'border-slate-500 text-slate-600' : 'border-transparent text-slate-400 hover:text-slate-600' }}">
                Arsip <span class="ml-1 text-xs bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded-full">{{ $totalArsip }}</span>
            </a>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Kode</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Nama Mata Kuliah</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide w-16">SKS</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden lg:table-cell">Deskripsi</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide text-center w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($mataKuliah as $mk)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-3.5">
                            <span class="font-mono text-xs font-semibold bg-ink-50 text-ink-600 px-2.5 py-1 rounded-lg">{{ $mk->kode }}</span>
                        </td>
                        <td class="px-4 py-3.5">
                            <p class="font-medium text-slate-800">{{ $mk->nama }}</p>
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            <span class="text-sm font-heading font-semibold text-slate-700">{{ $mk->sks }}</span>
                            <span class="text-xs text-slate-400"> sks</span>
                        </td>
                        <td class="px-4 py-3.5 text-slate-400 text-xs hidden lg:table-cell max-w-xs">
                            <p class="truncate">{{ $mk->deskripsi ?? '&mdash;' }}</p>
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center justify-center gap-1.5">
                                <button onclick="openEditMk({{ $mk->id }}, '{{ $mk->kode }}', '{{ addslashes($mk->nama) }}', {{ $mk->sks }}, '{{ addslashes($mk->deskripsi ?? '') }}')"
                                    class="w-8 h-8 rounded-lg bg-ink-50 hover:bg-ink-100 text-ink-500 flex items-center justify-center transition-colors" title="Edit">
                                    <i class="fa-solid fa-pen text-xs"></i>
                                </button>

                                @if($mk->is_arsip)
                                <form action="{{ route('admin.mata-kuliah.aktifkan', $mk) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="w-8 h-8 rounded-lg bg-sage-50 hover:bg-sage-100 text-sage-500 flex items-center justify-center transition-colors" title="Aktifkan">
                                        <i class="fa-solid fa-rotate-left text-xs"></i>
                                    </button>
                                </form>
                                @else
                                <form action="{{ route('admin.mata-kuliah.arsipkan', $mk) }}" method="POST"
                                    onsubmit="return confirm('Arsipkan {{ addslashes($mk->nama) }}?')">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-400 flex items-center justify-center transition-colors" title="Arsipkan">
                                        <i class="fa-solid fa-box-archive text-xs"></i>
                                    </button>
                                </form>
                                @endif

                                <form action="{{ route('admin.mata-kuliah.destroy', $mk) }}" method="POST"
                                    onsubmit="return confirm('Hapus mata kuliah {{ addslashes($mk->nama) }}? Semua kelas terkait ikut terhapus.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 hover:bg-red-100 text-red-400 flex items-center justify-center transition-colors" title="Hapus">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-16 text-center">
                            <i class="fa-solid fa-{{ $tab === 'arsip' ? 'box-archive' : 'book' }} text-slate-200 text-4xl mb-3 block"></i>
                            <p class="text-slate-400 text-sm">{{ $tab === 'arsip' ? 'Tidak ada mata kuliah diarsipkan.' : 'Belum ada mata kuliah. Klik "Tambah" untuk memulai.' }}</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($mataKuliah->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between text-sm text-slate-500">
            <span>{{ $mataKuliah->firstItem() }}"“{{ $mataKuliah->lastItem() }} dari {{ $mataKuliah->total() }}</span>
            <div class="flex gap-1">
                @if(!$mataKuliah->onFirstPage())
                <a href="{{ $mataKuliah->previousPageUrl() }}" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-ink-50 hover:text-ink-500 flex items-center justify-center text-slate-500 transition-colors"><i class="fa-solid fa-chevron-left text-xs"></i></a>
                @endif
                @foreach($mataKuliah->getUrlRange(max(1,$mataKuliah->currentPage()-2), min($mataKuliah->lastPage(),$mataKuliah->currentPage()+2)) as $page => $url)
                <a href="{{ $url }}" class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-medium {{ $page==$mataKuliah->currentPage() ? 'bg-ink-500 text-white' : 'bg-slate-100 hover:bg-ink-50 hover:text-ink-500 text-slate-600' }}">{{ $page }}</a>
                @endforeach
                @if($mataKuliah->hasMorePages())
                <a href="{{ $mataKuliah->nextPageUrl() }}" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-ink-50 hover:text-ink-500 flex items-center justify-center text-slate-500 transition-colors"><i class="fa-solid fa-chevron-right text-xs"></i></a>
                @endif
            </div>
        </div>
        @endif
    </div>

    {{-- Modal Tambah --}}
    <div id="modal-tambah" class="hidden fixed inset-0 z-50 items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" onclick="closeModal('modal-tambah')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-heading font-semibold text-slate-800">Tambah Mata Kuliah</h3>
                <button onclick="closeModal('modal-tambah')" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <form action="{{ route('admin.mata-kuliah.store') }}" method="POST" class="px-6 py-5 space-y-4">
                @csrf
                <div class="grid grid-cols-3 gap-3">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Kode MK <span class="text-red-400">*</span></label>
                        <input type="text" name="kode" value="{{ old('kode') }}" required placeholder="IF301"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-mono uppercase focus:outline-none focus:ring-2 focus:ring-ink-200 @error('kode') border-red-300 @enderror">
                        @error('kode')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">SKS <span class="text-red-400">*</span></label>
                        <select name="sks" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200">
                            @foreach([1,2,3,4,5,6] as $s)
                            <option value="{{ $s }}" {{ old('sks',3)==$s ? 'selected' : '' }}>{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Mata Kuliah <span class="text-red-400">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama') }}" required placeholder="contoh: Algoritma & Pemrograman"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200 @error('nama') border-red-300 @enderror">
                    @error('nama')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Deskripsi</label>
                    <textarea name="deskripsi" rows="3" placeholder="Deskripsi singkat mata kuliah (opsional)"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200 resize-none">{{ old('deskripsi') }}</textarea>
                </div>
                <div class="flex gap-3 pt-1">
                    <button type="button" onclick="closeModal('modal-tambah')" class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl bg-ink-500 hover:bg-ink-600 text-white text-sm font-medium transition-colors"><i class="fa-solid fa-plus mr-1.5 text-xs"></i>Tambah</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit --}}
    <div id="modal-edit" class="hidden fixed inset-0 z-50 items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" onclick="closeModal('modal-edit')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-heading font-semibold text-slate-800">Edit Mata Kuliah</h3>
                <button onclick="closeModal('modal-edit')" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <form id="form-edit-mk" method="POST" class="px-6 py-5 space-y-4">
                @csrf @method('PATCH')
                <div class="grid grid-cols-3 gap-3">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Kode MK <span class="text-red-400">*</span></label>
                        <input type="text" name="kode" id="edit-kode" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-mono uppercase focus:outline-none focus:ring-2 focus:ring-ink-200">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">SKS <span class="text-red-400">*</span></label>
                        <select name="sks" id="edit-sks" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200">
                            @foreach([1,2,3,4,5,6] as $s)<option value="{{ $s }}">{{ $s }}</option>@endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Mata Kuliah <span class="text-red-400">*</span></label>
                    <input type="text" name="nama" id="edit-nama" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Deskripsi</label>
                    <textarea name="deskripsi" id="edit-deskripsi" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200 resize-none"></textarea>
                </div>
                <div class="flex gap-3 pt-1">
                    <button type="button" onclick="closeModal('modal-edit')" class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl bg-ink-500 hover:bg-ink-600 text-white text-sm font-medium transition-colors"><i class="fa-solid fa-floppy-disk mr-1.5 text-xs"></i>Simpan</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function openModal(id) { const el = document.getElementById(id); el.classList.remove('hidden'); el.classList.add('flex'); }
        function closeModal(id) { const el = document.getElementById(id); el.classList.add('hidden'); el.classList.remove('flex'); }
        function openEditMk(id, kode, nama, sks, deskripsi) {
            document.getElementById('form-edit-mk').action = '/admin/mata-kuliah/' + id;
            document.getElementById('edit-kode').value      = kode;
            document.getElementById('edit-nama').value      = nama;
            document.getElementById('edit-sks').value       = sks;
            document.getElementById('edit-deskripsi').value = deskripsi;
            openModal('modal-edit');
        }
        @if($errors->any()) openModal('modal-tambah'); @endif
    </script>
    @endpush
</x-app-layout>
