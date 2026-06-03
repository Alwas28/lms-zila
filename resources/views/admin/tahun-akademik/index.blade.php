<x-app-layout>
    <x-slot name="title">Tahun Akademik</x-slot>
    <x-slot name="pageTitle">Manajemen Tahun Akademik</x-slot>

    {{-- Flash messages --}}
    @if(session('success'))
    <div id="flash-success" class="mb-4 flex items-center gap-3 bg-sage-50 border border-sage-200 text-sage-700 rounded-2xl px-4 py-3 text-sm">
        <i class="fa-solid fa-circle-check text-sage-500"></i>
        <span>{{ session('success') }}</span>
        <button onclick="document.getElementById('flash-success').remove()" class="ml-auto text-sage-400 hover:text-sage-600">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    @endif
    @if(session('error'))
    <div id="flash-error" class="mb-4 flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 rounded-2xl px-4 py-3 text-sm">
        <i class="fa-solid fa-circle-exclamation text-red-500"></i>
        <span>{{ session('error') }}</span>
        <button onclick="document.getElementById('flash-error').remove()" class="ml-auto text-red-400 hover:text-red-600">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    @endif

    {{-- Header + Tambah --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
        <div>
            <p class="text-sm text-slate-400 mt-0.5">Kelola tahun akademik dan semester aktif</p>
        </div>
        <button onclick="document.getElementById('modal-tambah').classList.remove('hidden')"
            class="inline-flex items-center gap-2 bg-ink-500 hover:bg-ink-600 text-white text-sm font-medium px-4 py-2.5 rounded-xl transition-colors">
            <i class="fa-solid fa-plus text-xs"></i>
            Tambah Tahun Akademik
        </button>
    </div>

    {{-- Semester Aktif Banner --}}
    @php $semesterAktif = \App\Models\Semester::aktif(); @endphp
    @if($semesterAktif)
    <div class="bg-gradient-to-r from-ink-500 to-ink-700 rounded-2xl p-4 md:p-5 mb-6 text-white flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                <i class="fa-solid fa-calendar-check text-white"></i>
            </div>
            <div>
                <p class="text-xs text-ink-200">Semester Berjalan Saat Ini</p>
                <p class="font-heading font-semibold text-base">{{ $semesterAktif->label }}</p>
                @if($semesterAktif->tanggal_mulai && $semesterAktif->tanggal_selesai)
                <p class="text-xs text-ink-200 mt-0.5">
                    {{ $semesterAktif->tanggal_mulai->format('d M Y') }} "“ {{ $semesterAktif->tanggal_selesai->format('d M Y') }}
                </p>
                @endif
            </div>
        </div>
        <span class="bg-white/20 text-white text-xs px-3 py-1.5 rounded-full font-medium">Aktif</span>
    </div>
    @else
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 mb-6 flex items-center gap-3 text-amber-700 text-sm">
        <i class="fa-solid fa-triangle-exclamation text-amber-500"></i>
        <span>Belum ada semester yang diaktifkan. Silakan aktifkan semester di bawah ini.</span>
    </div>
    @endif

    {{-- Daftar Tahun Akademik --}}
    <div class="space-y-4">
        @forelse($tahunAkademik as $ta)
        <div class="bg-white rounded-2xl border {{ $ta->is_aktif ? 'border-ink-200 shadow-sm shadow-ink-100' : 'border-slate-200' }} overflow-hidden">

            {{-- Header Tahun Akademik --}}
            <div class="flex items-center justify-between px-5 py-4 {{ $ta->is_aktif ? 'bg-ink-50' : 'bg-slate-50' }}">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl {{ $ta->is_aktif ? 'bg-ink-500' : 'bg-slate-300' }} flex items-center justify-center">
                        <i class="fa-solid fa-calendar-alt text-white text-sm"></i>
                    </div>
                    <div>
                        <h3 class="font-heading font-semibold text-slate-800 text-sm">Tahun Akademik {{ $ta->nama }}</h3>
                        <p class="text-xs text-slate-400">{{ $ta->semesters->count() }} semester</p>
                    </div>
                    @if($ta->is_aktif)
                    <span class="bg-ink-100 text-ink-600 text-xs font-medium px-2.5 py-0.5 rounded-full">Aktif</span>
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    @if(!$ta->is_aktif)
                    <form action="{{ route('admin.tahun-akademik.aktifkan', $ta) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit" class="text-xs bg-ink-50 hover:bg-ink-100 text-ink-600 px-3 py-1.5 rounded-xl transition-colors font-medium">
                            <i class="fa-solid fa-check mr-1"></i>Aktifkan
                        </button>
                    </form>
                    <form action="{{ route('admin.tahun-akademik.destroy', $ta) }}" method="POST"
                        onsubmit="return confirm('Hapus tahun akademik {{ $ta->nama }}? Semua data terkait akan ikut terhapus.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs bg-red-50 hover:bg-red-100 text-red-500 px-3 py-1.5 rounded-xl transition-colors font-medium">
                            <i class="fa-solid fa-trash-can mr-1"></i>Hapus
                        </button>
                    </form>
                    @else
                    <form action="{{ route('admin.tahun-akademik.nonaktifkan', $ta) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit" class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-600 px-3 py-1.5 rounded-xl transition-colors font-medium">
                            <i class="fa-solid fa-power-off mr-1"></i>Nonaktifkan
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            {{-- Daftar Semester --}}
            <div class="divide-y divide-slate-100">
                @foreach($ta->semesters->sortBy('tipe') as $sm)
                <div class="px-5 py-4 flex flex-col sm:flex-row sm:items-center gap-3">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <div class="w-8 h-8 rounded-lg {{ $sm->is_aktif ? 'bg-sage-100' : 'bg-slate-100' }} flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-{{ $sm->tipe === 'ganjil' ? 'sun' : 'snowflake' }} text-xs {{ $sm->is_aktif ? 'text-sage-600' : 'text-slate-400' }}"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-slate-700">Semester {{ ucfirst($sm->tipe) }}</p>
                            <p class="text-xs text-slate-400">
                                @if($sm->tanggal_mulai && $sm->tanggal_selesai)
                                    {{ $sm->tanggal_mulai->format('d M Y') }} "“ {{ $sm->tanggal_selesai->format('d M Y') }}
                                @else
                                    <span class="text-amber-500">Tanggal belum diatur</span>
                                @endif
                            </p>
                        </div>
                        @if($sm->is_aktif)
                        <span class="bg-sage-50 text-sage-600 text-xs font-medium px-2 py-0.5 rounded-full flex-shrink-0">Berjalan</span>
                        @endif
                    </div>

                    <div class="flex items-center gap-2 flex-shrink-0">
                        {{-- Edit tanggal --}}
                        <button onclick="openEditSemester({{ $sm->id }}, '{{ $sm->tipe_human }}', '{{ $sm->tanggal_mulai?->format('Y-m-d') ?? '' }}', '{{ $sm->tanggal_selesai?->format('Y-m-d') ?? '' }}')"
                            class="text-xs bg-slate-50 hover:bg-slate-100 text-slate-600 px-3 py-1.5 rounded-xl transition-colors font-medium">
                            <i class="fa-solid fa-calendar-days mr-1"></i>Atur Tanggal
                        </button>

                        @if(!$sm->is_aktif)
                        <form action="{{ route('admin.semester.aktifkan', $sm) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="text-xs bg-sage-50 hover:bg-sage-100 text-sage-600 px-3 py-1.5 rounded-xl transition-colors font-medium">
                                <i class="fa-solid fa-play mr-1"></i>Jadikan Aktif
                            </button>
                        </form>
                        @else
                        <form action="{{ route('admin.semester.nonaktifkan', $sm) }}" method="POST">
                            @csrf @method('PATCH')
                            <button type="submit" class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-500 px-3 py-1.5 rounded-xl transition-colors font-medium">
                                <i class="fa-solid fa-stop mr-1"></i>Nonaktifkan
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @empty
        <div class="bg-white rounded-2xl border border-slate-200 p-10 text-center">
            <i class="fa-solid fa-calendar-xmark text-slate-300 text-4xl mb-3"></i>
            <p class="text-slate-400 text-sm">Belum ada tahun akademik. Klik "Tambah" untuk memulai.</p>
        </div>
        @endforelse
    </div>

    {{-- ===== MODAL: Tambah Tahun Akademik ===== --}}
    <div id="modal-tambah" class="hidden fixed inset-0 z-50 items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" onclick="document.getElementById('modal-tambah').classList.add('hidden')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-heading font-semibold text-slate-800">Tambah Tahun Akademik</h3>
                <button onclick="document.getElementById('modal-tambah').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <form action="{{ route('admin.tahun-akademik.store') }}" method="POST" class="px-6 py-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Tahun Akademik</label>
                    <input type="text" name="nama" placeholder="contoh: 2026/2027"
                        pattern="\d{4}/\d{4}" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-300 focus:border-ink-300 @error('nama') border-red-300 @enderror"
                        value="{{ old('nama') }}">
                    @error('nama')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-slate-400 mt-1">Format: YYYY/YYYY (contoh: 2026/2027)</p>
                </div>
                <p class="text-xs text-slate-500 bg-slate-50 rounded-xl p-3">
                    <i class="fa-solid fa-circle-info text-ink-400 mr-1"></i>
                    Dua semester (Ganjil & Genap) akan otomatis dibuat. Atur tanggalnya setelah ditambahkan.
                </p>
                <div class="flex gap-3 pt-1">
                    <button type="button" onclick="document.getElementById('modal-tambah').classList.add('hidden')"
                        class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2.5 rounded-xl bg-ink-500 hover:bg-ink-600 text-white text-sm font-medium transition-colors">
                        Tambah
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== MODAL: Edit Tanggal Semester ===== --}}
    <div id="modal-edit-semester" class="hidden fixed inset-0 z-50 items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" onclick="document.getElementById('modal-edit-semester').classList.add('hidden')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-heading font-semibold text-slate-800">Atur Tanggal Semester <span id="modal-sm-label" class="text-ink-500"></span></h3>
                <button onclick="document.getElementById('modal-edit-semester').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <form id="form-edit-semester" method="POST" class="px-6 py-5 space-y-4">
                @csrf @method('PATCH')
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" id="input-mulai"
                            class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" id="input-selesai"
                            class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-300">
                    </div>
                </div>
                <div class="flex gap-3 pt-1">
                    <button type="button" onclick="document.getElementById('modal-edit-semester').classList.add('hidden')"
                        class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2.5 rounded-xl bg-ink-500 hover:bg-ink-600 text-white text-sm font-medium transition-colors">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function openEditSemester(id, tipe, mulai, selesai) {
            document.getElementById('modal-sm-label').textContent = tipe;
            document.getElementById('form-edit-semester').action = '/admin/semester/' + id;
            document.getElementById('input-mulai').value = mulai;
            document.getElementById('input-selesai').value = selesai;
            document.getElementById('modal-edit-semester').classList.remove('hidden');
        }

        // Buka modal jika ada error validasi
        @if($errors->any())
            document.getElementById('modal-tambah').classList.remove('hidden');
        @endif
    </script>
    @endpush

</x-app-layout>
