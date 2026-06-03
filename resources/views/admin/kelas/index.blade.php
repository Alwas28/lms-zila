<x-app-layout>
    <x-slot name="title">Kelas</x-slot>
    <x-slot name="pageTitle">Manajemen Kelas</x-slot>

    @include('admin._flash')

    {{-- Filter Semester + Salin Kelas --}}
    <div class="flex flex-col sm:flex-row gap-3 mb-5">
        {{-- Pilih Semester --}}
        <form method="GET" action="{{ route('admin.kelas.index') }}" class="flex items-center gap-2">
            <label class="text-sm text-slate-500 flex-shrink-0">Semester:</label>
            <select name="semester_id" onchange="this.form.submit()"
                class="px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200 bg-white text-slate-700">
                <option value="">Semua</option>
                @foreach($semua as $sm)
                <option value="{{ $sm->id }}" {{ $semesterId == $sm->id ? 'selected' : '' }}>
                    Semester {{ ucfirst($sm->tipe) }} &mdash; {{ $sm->tahunAkademik->nama }}
                    {{ $sm->is_aktif ? '(Aktif)' : '' }}
                </option>
                @endforeach
            </select>
        </form>

        {{-- Salin kelas --}}
        <button onclick="openModal('modal-salin')"
            class="inline-flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium px-4 py-2 rounded-xl transition-colors ml-auto">
            <i class="fa-solid fa-copy text-slate-500 text-xs"></i> Salin Kelas
        </button>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 gap-3 mb-5">
        <div class="bg-white rounded-2xl p-4 border border-slate-200 card-hover flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-ink-50 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-door-open text-ink-500"></i>
            </div>
            <div>
                <p class="text-xl font-heading font-semibold text-slate-800">{{ $kelas->total() }}</p>
                <p class="text-xs text-slate-400">Total Kelas</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-slate-200 card-hover flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-sage-50 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-circle-check text-sage-500"></i>
            </div>
            <div>
                <p class="text-xl font-heading font-semibold text-slate-800">{{ $totalAktif }}</p>
                <p class="text-xs text-slate-400">Kelas Aktif</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        {{-- Toolbar --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-5 py-4 border-b border-slate-100">
            <form method="GET" action="{{ route('admin.kelas.index') }}" class="flex-1 max-w-sm flex gap-2">
                <input type="hidden" name="semester_id" value="{{ $semesterId }}">
                <div class="relative flex-1">
                    <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari mata kuliah..."
                        class="w-full pl-9 pr-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200 text-slate-700 placeholder:text-slate-400">
                </div>
            </form>
            <button onclick="openModal('modal-tambah')"
                class="inline-flex items-center gap-2 bg-ink-500 hover:bg-ink-600 text-white text-sm font-medium px-4 py-2.5 rounded-xl transition-colors flex-shrink-0">
                <i class="fa-solid fa-plus text-xs"></i> Buat Kelas
            </button>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Kelas</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden md:table-cell">Semester</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide w-24">Peserta</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide w-20 hidden sm:table-cell">Dosen</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide w-24">Status</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide text-center w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($kelas as $k)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-ink-100 flex items-center justify-center flex-shrink-0 font-heading font-bold text-ink-600 text-sm">
                                    {{ $k->nama_kelas }}
                                </div>
                                <div>
                                    <p class="font-medium text-slate-800 leading-snug">{{ $k->mataKuliah->nama }}</p>
                                    <p class="text-xs text-slate-400 font-mono">{{ $k->mataKuliah->kode }} · {{ $k->mataKuliah->sks }} SKS</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3.5 hidden md:table-cell">
                            <p class="text-xs text-slate-600">{{ ucfirst($k->semester->tipe) }}</p>
                            <p class="text-xs text-slate-400">{{ $k->semester->tahunAkademik->nama }}</p>
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            <div class="flex flex-col items-center">
                                <span class="text-sm font-semibold {{ $k->mahasiswa_count >= $k->kapasitas ? 'text-red-500' : 'text-slate-700' }}">
                                    {{ $k->mahasiswa_count }}/{{ $k->kapasitas }}
                                </span>
                                <div class="w-12 h-1 bg-slate-100 rounded-full mt-1">
                                    @php $pct = $k->kapasitas > 0 ? min(100, round($k->mahasiswa_count/$k->kapasitas*100)) : 0; @endphp
                                    <div class="h-1 rounded-full {{ $pct >= 90 ? 'bg-red-400' : ($pct >= 70 ? 'bg-ember-400' : 'bg-sage-400') }}" style="width:{{ $pct }}%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3.5 text-center text-slate-600 hidden sm:table-cell">
                            <span class="text-sm font-semibold">{{ $k->dosen_count }}</span>
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            @if($k->is_aktif)
                            <span class="text-xs bg-sage-50 text-sage-600 px-2.5 py-1 rounded-full font-medium">Aktif</span>
                            @else
                            <span class="text-xs bg-slate-100 text-slate-400 px-2.5 py-1 rounded-full font-medium">Tutup</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center justify-center gap-1.5">
                                <button onclick="openEditKelas({{ $k->id }}, {{ $k->kapasitas }})"
                                    class="w-8 h-8 rounded-lg bg-ink-50 hover:bg-ink-100 text-ink-500 flex items-center justify-center transition-colors" title="Edit Kapasitas">
                                    <i class="fa-solid fa-pen text-xs"></i>
                                </button>

                                @if($k->is_aktif)
                                <form action="{{ route('admin.kelas.tutup', $k) }}" method="POST"
                                    onsubmit="return confirm('Tutup kelas {{ addslashes($k->nama_lengkap) }}?')">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="w-8 h-8 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-500 flex items-center justify-center transition-colors" title="Tutup Kelas">
                                        <i class="fa-solid fa-lock text-xs"></i>
                                    </button>
                                </form>
                                @else
                                <form action="{{ route('admin.kelas.buka', $k) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="w-8 h-8 rounded-lg bg-sage-50 hover:bg-sage-100 text-sage-500 flex items-center justify-center transition-colors" title="Buka Kelas">
                                        <i class="fa-solid fa-lock-open text-xs"></i>
                                    </button>
                                </form>
                                @endif

                                <form action="{{ route('admin.kelas.destroy', $k) }}" method="POST"
                                    onsubmit="return confirm('Hapus kelas {{ addslashes($k->nama_lengkap) }}? Semua data pengampu dan enroll ikut terhapus.')">
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
                        <td colspan="6" class="px-5 py-16 text-center">
                            <i class="fa-solid fa-door-open text-slate-200 text-4xl mb-3 block"></i>
                            <p class="text-slate-400 text-sm">Belum ada kelas untuk semester ini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($kelas->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between text-sm text-slate-500">
            <span>{{ $kelas->firstItem() }}"“{{ $kelas->lastItem() }} dari {{ $kelas->total() }}</span>
            <div class="flex gap-1">
                @if(!$kelas->onFirstPage())<a href="{{ $kelas->previousPageUrl() }}" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-ink-50 hover:text-ink-500 flex items-center justify-center text-slate-500"><i class="fa-solid fa-chevron-left text-xs"></i></a>@endif
                @foreach($kelas->getUrlRange(max(1,$kelas->currentPage()-2),min($kelas->lastPage(),$kelas->currentPage()+2)) as $page=>$url)
                <a href="{{ $url }}" class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-medium {{ $page==$kelas->currentPage() ? 'bg-ink-500 text-white' : 'bg-slate-100 hover:bg-ink-50 hover:text-ink-500 text-slate-600' }}">{{ $page }}</a>
                @endforeach
                @if($kelas->hasMorePages())<a href="{{ $kelas->nextPageUrl() }}" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-ink-50 hover:text-ink-500 flex items-center justify-center text-slate-500"><i class="fa-solid fa-chevron-right text-xs"></i></a>@endif
            </div>
        </div>
        @endif
    </div>

    {{-- Modal Tambah Kelas --}}
    <div id="modal-tambah" class="hidden fixed inset-0 z-50 items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" onclick="closeModal('modal-tambah')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-heading font-semibold text-slate-800">Buat Kelas Baru</h3>
                <button onclick="closeModal('modal-tambah')" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <form action="{{ route('admin.kelas.store') }}" method="POST" class="px-6 py-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Mata Kuliah <span class="text-red-400">*</span></label>
                    <select name="mata_kuliah_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200 @error('mata_kuliah_id') border-red-300 @enderror">
                        <option value="">&mdash; Pilih Mata Kuliah &mdash;</option>
                        @foreach($mataKuliah as $mk)
                        <option value="{{ $mk->id }}" {{ old('mata_kuliah_id')==$mk->id ? 'selected' : '' }}>
                            {{ $mk->kode }} &mdash; {{ $mk->nama }} ({{ $mk->sks }} SKS)
                        </option>
                        @endforeach
                    </select>
                    @error('mata_kuliah_id')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Semester <span class="text-red-400">*</span></label>
                    <select name="semester_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200 @error('semester_id') border-red-300 @enderror">
                        <option value="">&mdash; Pilih Semester &mdash;</option>
                        @foreach($semua as $sm)
                        <option value="{{ $sm->id }}" {{ (old('semester_id', $semesterId))==$sm->id ? 'selected' : '' }}>
                            Semester {{ ucfirst($sm->tipe) }} &mdash; {{ $sm->tahunAkademik->nama }}{{ $sm->is_aktif ? ' (Aktif)' : '' }}
                        </option>
                        @endforeach
                    </select>
                    @error('semester_id')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Kelas <span class="text-red-400">*</span></label>
                        <input type="text" name="nama_kelas" value="{{ old('nama_kelas') }}" required placeholder="A" maxlength="5"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm uppercase font-mono focus:outline-none focus:ring-2 focus:ring-ink-200 @error('nama_kelas') border-red-300 @enderror">
                        @error('nama_kelas')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Kapasitas <span class="text-red-400">*</span></label>
                        <input type="number" name="kapasitas" value="{{ old('kapasitas', 40) }}" required min="1" max="200"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200 @error('kapasitas') border-red-300 @enderror">
                    </div>
                </div>
                <div class="flex gap-3 pt-1">
                    <button type="button" onclick="closeModal('modal-tambah')" class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl bg-ink-500 hover:bg-ink-600 text-white text-sm font-medium transition-colors"><i class="fa-solid fa-plus mr-1.5 text-xs"></i>Buat</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit Kapasitas --}}
    <div id="modal-edit" class="hidden fixed inset-0 z-50 items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" onclick="closeModal('modal-edit')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-xs">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-heading font-semibold text-slate-800">Edit Kapasitas</h3>
                <button onclick="closeModal('modal-edit')" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <form id="form-edit-kelas" method="POST" class="px-6 py-5 space-y-4">
                @csrf @method('PATCH')
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Kapasitas Mahasiswa <span class="text-red-400">*</span></label>
                    <input type="number" name="kapasitas" id="edit-kapasitas" min="1" max="200" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200">
                </div>
                <div class="flex gap-3">
                    <button type="button" onclick="closeModal('modal-edit')" class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl bg-ink-500 hover:bg-ink-600 text-white text-sm font-medium transition-colors"><i class="fa-solid fa-floppy-disk mr-1.5 text-xs"></i>Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Salin Kelas --}}
    <div id="modal-salin" class="hidden fixed inset-0 z-50 items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" onclick="closeModal('modal-salin')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-heading font-semibold text-slate-800">Salin Kelas dari Semester Lain</h3>
                <button onclick="closeModal('modal-salin')" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <form action="{{ route('admin.kelas.salin') }}" method="POST" class="px-6 py-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Salin Dari Semester <span class="text-red-400">*</span></label>
                    <select name="dari_semester_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200">
                        <option value="">&mdash; Pilih semester asal &mdash;</option>
                        @foreach($semua as $sm)
                        <option value="{{ $sm->id }}">Semester {{ ucfirst($sm->tipe) }} &mdash; {{ $sm->tahunAkademik->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Ke Semester <span class="text-red-400">*</span></label>
                    <select name="ke_semester_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200">
                        <option value="">&mdash; Pilih semester tujuan &mdash;</option>
                        @foreach($semua as $sm)
                        <option value="{{ $sm->id }}" {{ $sm->is_aktif ? 'selected' : '' }}>
                            Semester {{ ucfirst($sm->tipe) }} &mdash; {{ $sm->tahunAkademik->nama }}{{ $sm->is_aktif ? ' (Aktif)' : '' }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <p class="text-xs text-slate-400 bg-slate-50 rounded-xl px-3 py-2.5">
                    <i class="fa-solid fa-circle-info text-slate-400 mr-1"></i>
                    Kelas yang sudah ada di semester tujuan tidak akan ditimpa.
                </p>
                <div class="flex gap-3 pt-1">
                    <button type="button" onclick="closeModal('modal-salin')" class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl bg-ink-500 hover:bg-ink-600 text-white text-sm font-medium transition-colors"><i class="fa-solid fa-copy mr-1.5 text-xs"></i>Salin</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function openModal(id) { const el = document.getElementById(id); el.classList.remove('hidden'); el.classList.add('flex'); }
        function closeModal(id) { const el = document.getElementById(id); el.classList.add('hidden'); el.classList.remove('flex'); }
        function openEditKelas(id, kapasitas) {
            document.getElementById('form-edit-kelas').action = '/admin/kelas/' + id;
            document.getElementById('edit-kapasitas').value   = kapasitas;
            openModal('modal-edit');
        }
        @if($errors->any()) openModal('modal-tambah'); @endif
    </script>
    @endpush
</x-app-layout>
