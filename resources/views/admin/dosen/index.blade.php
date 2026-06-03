<x-app-layout>
    <x-slot name="title">Manajemen Dosen</x-slot>
    <x-slot name="pageTitle">Manajemen Dosen</x-slot>

    {{-- Flash --}}
    @if(session('success'))
    <div id="flash-ok" class="mb-5 flex items-center gap-3 bg-sage-50 border border-sage-200 text-sage-700 rounded-2xl px-4 py-3 text-sm">
        <i class="fa-solid fa-circle-check text-sage-500 flex-shrink-0"></i>
        <span>{{ session('success') }}</span>
        <button onclick="this.closest('#flash-ok').remove()" class="ml-auto text-sage-400 hover:text-sage-600"><i class="fa-solid fa-xmark"></i></button>
    </div>
    @endif
    @if(session('error'))
    <div id="flash-err" class="mb-5 flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 rounded-2xl px-4 py-3 text-sm">
        <i class="fa-solid fa-circle-exclamation text-red-500 flex-shrink-0"></i>
        <span>{{ session('error') }}</span>
        <button onclick="this.closest('#flash-err').remove()" class="ml-auto text-red-400 hover:text-red-600"><i class="fa-solid fa-xmark"></i></button>
    </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 md:gap-4 mb-6">
        <div class="bg-white rounded-2xl p-4 md:p-5 border border-slate-200 card-hover">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-ink-50 flex items-center justify-center">
                    <i class="fa-solid fa-chalkboard-user text-ink-500"></i>
                </div>
                <span class="text-xs text-ink-600 bg-ink-50 px-2 py-0.5 rounded-full font-medium badge hidden sm:block">Total</span>
            </div>
            <p class="text-2xl font-heading font-semibold text-slate-800">{{ $total }}</p>
            <p class="text-sm text-slate-400 mt-0.5">Total Dosen</p>
        </div>
        <div class="bg-white rounded-2xl p-4 md:p-5 border border-slate-200 card-hover">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-sage-50 flex items-center justify-center">
                    <i class="fa-solid fa-circle-check text-sage-500"></i>
                </div>
                <span class="text-xs text-sage-600 bg-sage-50 px-2 py-0.5 rounded-full font-medium badge hidden sm:block">Aktif</span>
            </div>
            <p class="text-2xl font-heading font-semibold text-slate-800">{{ $total }}</p>
            <p class="text-sm text-slate-400 mt-0.5">Dosen Aktif</p>
        </div>
        <div class="bg-white rounded-2xl p-4 md:p-5 border border-slate-200 card-hover col-span-2 lg:col-span-1">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-ember-50 flex items-center justify-center">
                    <i class="fa-solid fa-user-plus text-ember-500"></i>
                </div>
                <span class="text-xs text-ember-600 bg-ember-50 px-2 py-0.5 rounded-full font-medium badge hidden sm:block">Bulan ini</span>
            </div>
            <p class="text-2xl font-heading font-semibold text-slate-800">{{ $bulanIni }}</p>
            <p class="text-sm text-slate-400 mt-0.5">Baru Bulan Ini</p>
        </div>
    </div>

    {{-- Toolbar --}}
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-5 py-4 border-b border-slate-100">
            {{-- Search --}}
            <form method="GET" action="{{ route('admin.dosen.index') }}" class="flex-1 max-w-sm">
                <div class="relative">
                    <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama, NIP, atau email..."
                        class="w-full pl-9 pr-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200 focus:border-ink-300 text-slate-700 placeholder:text-slate-400">
                </div>
            </form>
            {{-- Actions --}}
            <div class="flex items-center gap-2 flex-shrink-0">
                <label for="importExcel"
                    class="inline-flex items-center gap-2 bg-sage-50 hover:bg-sage-100 text-sage-700 text-sm font-medium px-4 py-2.5 rounded-xl transition-colors cursor-pointer border border-sage-200">
                    <i class="fa-solid fa-file-excel text-sage-600 text-xs"></i>
                    Import Excel
                </label>
                <input type="file" id="importExcel" accept=".xlsx,.xls,.csv" class="hidden"
                    onchange="previewImport(this)">
                <button onclick="openModal('modal-tambah')"
                    class="inline-flex items-center gap-2 bg-ink-500 hover:bg-ink-600 text-white text-sm font-medium px-4 py-2.5 rounded-xl transition-colors">
                    <i class="fa-solid fa-plus text-xs"></i>
                    Tambah Dosen
                </button>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide w-8">#</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Nama Dosen</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden md:table-cell">NIP</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden lg:table-cell">Email</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden lg:table-cell">Bergabung</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide text-center w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($dosen as $d)
                    <tr class="hover:bg-slate-50 transition-colors group">
                        <td class="px-5 py-3.5 text-slate-400 text-xs">
                            {{ ($dosen->currentPage() - 1) * $dosen->perPage() + $loop->iteration }}
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-ink-100 flex items-center justify-center flex-shrink-0 text-ink-600 font-semibold text-sm">
                                    {{ strtoupper(substr($d->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="font-medium text-slate-800 truncate">{{ $d->name }}</p>
                                    <p class="text-xs text-slate-400 md:hidden truncate">{{ $d->nip_nim ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3.5 text-slate-600 hidden md:table-cell">
                            <span class="font-mono text-xs bg-slate-100 px-2 py-1 rounded-lg">{{ $d->nip_nim ?? '&mdash;' }}</span>
                        </td>
                        <td class="px-4 py-3.5 text-slate-500 text-xs hidden lg:table-cell">{{ $d->email }}</td>
                        <td class="px-4 py-3.5 text-slate-400 text-xs hidden lg:table-cell">
                            {{ $d->created_at->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center justify-center gap-1.5">
                                {{-- Edit --}}
                                <button onclick="openEdit({{ $d->id }}, '{{ addslashes($d->name) }}', '{{ $d->email }}', '{{ $d->nip_nim }}')"
                                    title="Edit"
                                    class="w-8 h-8 rounded-lg bg-ink-50 hover:bg-ink-100 text-ink-500 flex items-center justify-center transition-colors">
                                    <i class="fa-solid fa-pen text-xs"></i>
                                </button>
                                {{-- Reset Password --}}
                                <form action="{{ route('admin.dosen.reset-password', $d) }}" method="POST"
                                    onsubmit="return confirm('Reset password {{ addslashes($d->name) }} ke \'password\'?')">
                                    @csrf @method('PATCH')
                                    <button type="submit" title="Reset Password"
                                        class="w-8 h-8 rounded-lg bg-ember-50 hover:bg-ember-100 text-ember-500 flex items-center justify-center transition-colors">
                                        <i class="fa-solid fa-key text-xs"></i>
                                    </button>
                                </form>
                                {{-- Hapus --}}
                                <form action="{{ route('admin.dosen.destroy', $d) }}" method="POST"
                                    onsubmit="return confirm('Hapus dosen {{ addslashes($d->name) }}? Aksi ini tidak dapat dibatalkan.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" title="Hapus"
                                        class="w-8 h-8 rounded-lg bg-red-50 hover:bg-red-100 text-red-400 flex items-center justify-center transition-colors">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-16 text-center">
                            <i class="fa-solid fa-chalkboard-user text-slate-200 text-4xl mb-3 block"></i>
                            <p class="text-slate-400 text-sm">
                                @if(request('search'))
                                    Tidak ada dosen yang cocok dengan pencarian "<strong>{{ request('search') }}</strong>"
                                @else
                                    Belum ada data dosen. Klik "Tambah Dosen" untuk memulai.
                                @endif
                            </p>
                            @if(request('search'))
                            <a href="{{ route('admin.dosen.index') }}" class="text-ink-500 text-sm mt-2 inline-block hover:underline">Hapus filter</a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($dosen->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between text-sm text-slate-500">
            <span>Menampilkan {{ $dosen->firstItem() }}"“{{ $dosen->lastItem() }} dari {{ $dosen->total() }} dosen</span>
            <div class="flex items-center gap-1">
                @if($dosen->onFirstPage())
                <span class="w-8 h-8 rounded-lg bg-slate-50 text-slate-300 flex items-center justify-center cursor-not-allowed">
                    <i class="fa-solid fa-chevron-left text-xs"></i>
                </span>
                @else
                <a href="{{ $dosen->previousPageUrl() }}" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-ink-50 hover:text-ink-500 text-slate-500 flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-chevron-left text-xs"></i>
                </a>
                @endif

                @foreach($dosen->getUrlRange(max(1, $dosen->currentPage()-2), min($dosen->lastPage(), $dosen->currentPage()+2)) as $page => $url)
                <a href="{{ $url }}" class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-medium transition-colors
                    {{ $page == $dosen->currentPage() ? 'bg-ink-500 text-white' : 'bg-slate-100 hover:bg-ink-50 hover:text-ink-500 text-slate-600' }}">
                    {{ $page }}
                </a>
                @endforeach

                @if($dosen->hasMorePages())
                <a href="{{ $dosen->nextPageUrl() }}" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-ink-50 hover:text-ink-500 text-slate-500 flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </a>
                @else
                <span class="w-8 h-8 rounded-lg bg-slate-50 text-slate-300 flex items-center justify-center cursor-not-allowed">
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </span>
                @endif
            </div>
        </div>
        @endif
    </div>

    {{-- ===== MODAL: Tambah Dosen ===== --}}
    <div id="modal-tambah" class="hidden fixed inset-0 z-50 items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" onclick="closeModal('modal-tambah')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-heading font-semibold text-slate-800">Tambah Dosen Baru</h3>
                <button onclick="closeModal('modal-tambah')" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <form action="{{ route('admin.dosen.store') }}" method="POST" class="px-6 py-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Lengkap <span class="text-red-400">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="contoh: Dr. Budi Santoso, M.Kom"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200 @error('name') border-red-300 @enderror">
                    @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">NIP <span class="text-red-400">*</span></label>
                    <input type="text" name="nip_nim" value="{{ old('nip_nim') }}" required placeholder="18 digit NIP"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-ink-200 @error('nip_nim') border-red-300 @enderror">
                    @error('nip_nim')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Email <span class="text-red-400">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="dosen@lmszila.ac.id"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200 @error('email') border-red-300 @enderror">
                    @error('email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Password <span class="text-red-400">*</span></label>
                    <div class="relative">
                        <input type="password" name="password" id="pw-tambah" required placeholder="Minimal 8 karakter"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200 pr-10 @error('password') border-red-300 @enderror">
                        <button type="button" onclick="togglePw('pw-tambah','eye-tambah')" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                            <i id="eye-tambah" class="fa-solid fa-eye text-sm"></i>
                        </button>
                    </div>
                    @error('password')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="flex gap-3 pt-1">
                    <button type="button" onclick="closeModal('modal-tambah')"
                        class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50">Batal</button>
                    <button type="submit"
                        class="flex-1 px-4 py-2.5 rounded-xl bg-ink-500 hover:bg-ink-600 text-white text-sm font-medium transition-colors">
                        <i class="fa-solid fa-plus mr-1.5 text-xs"></i>Tambah
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== MODAL: Edit Dosen ===== --}}
    <div id="modal-edit" class="hidden fixed inset-0 z-50 items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" onclick="closeModal('modal-edit')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-heading font-semibold text-slate-800">Edit Data Dosen</h3>
                <button onclick="closeModal('modal-edit')" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <form id="form-edit" method="POST" class="px-6 py-5 space-y-4">
                @csrf @method('PATCH')
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Lengkap <span class="text-red-400">*</span></label>
                    <input type="text" name="name" id="edit-name" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">NIP <span class="text-red-400">*</span></label>
                    <input type="text" name="nip_nim" id="edit-nip" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-ink-200">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Email <span class="text-red-400">*</span></label>
                    <input type="email" name="email" id="edit-email" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200">
                </div>
                <p class="text-xs text-slate-400 bg-slate-50 rounded-xl px-3 py-2.5">
                    <i class="fa-solid fa-circle-info text-slate-400 mr-1"></i>
                    Untuk mengubah password, gunakan tombol <span class="font-medium text-amber-600">Reset Password</span> pada baris dosen.
                </p>
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
        function openModal(id) { const el = document.getElementById(id); el.classList.remove('hidden'); el.classList.add('flex'); }
        function closeModal(id) { const el = document.getElementById(id); el.classList.add('hidden'); el.classList.remove('flex'); }

        function openEdit(id, name, email, nip) {
            document.getElementById('form-edit').action = '/admin/dosen/' + id;
            document.getElementById('edit-name').value  = name;
            document.getElementById('edit-email').value = email;
            document.getElementById('edit-nip').value   = nip;
            openModal('modal-edit');
        }

        function togglePw(inputId, iconId) {
            const inp  = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (inp.type === 'password') {
                inp.type = 'text';
                icon.className = 'fa-solid fa-eye-slash text-sm';
            } else {
                inp.type = 'password';
                icon.className = 'fa-solid fa-eye text-sm';
            }
        }

        function previewImport(input) {
            if (input.files && input.files[0]) {
                const fname = input.files[0].name;
                if (confirm('Import data dosen dari file "' + fname + '"?\n\nFitur ini memerlukan konfigurasi tambahan.')) {
                    // TODO: submit form import
                }
                input.value = '';
            }
        }

        @if($errors->any())
            openModal('modal-tambah');
        @endif
    </script>
    @endpush

</x-app-layout>
