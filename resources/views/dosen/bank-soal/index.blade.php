<x-app-layout>
    <x-slot name="title">Bank Soal</x-slot>
    <x-slot name="pageTitle">Bank Soal</x-slot>

    @include('admin._flash')

    {{-- Stats --}}
    <div class="grid grid-cols-3 gap-3 md:gap-4 mb-6">
        <div class="bg-white rounded-2xl p-4 md:p-5 border border-slate-200 card-hover">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-ink-50 flex items-center justify-center">
                    <i class="fa-solid fa-circle-question text-ink-500"></i>
                </div>
                <span class="text-xs text-ink-600 bg-ink-50 px-2 py-0.5 rounded-full font-medium badge hidden sm:block">Total</span>
            </div>
            <p class="text-2xl font-heading font-semibold text-slate-800">{{ $total }}</p>
            <p class="text-sm text-slate-400 mt-0.5">Total Soal</p>
        </div>
        <div class="bg-white rounded-2xl p-4 md:p-5 border border-slate-200 card-hover">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-sage-50 flex items-center justify-center">
                    <i class="fa-solid fa-list-ol text-sage-500"></i>
                </div>
                <span class="text-xs text-sage-600 bg-sage-50 px-2 py-0.5 rounded-full font-medium badge hidden sm:block">PG</span>
            </div>
            <p class="text-2xl font-heading font-semibold text-slate-800">{{ $totalPg }}</p>
            <p class="text-sm text-slate-400 mt-0.5">Pilihan Ganda</p>
        </div>
        <div class="bg-white rounded-2xl p-4 md:p-5 border border-slate-200 card-hover">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center">
                    <i class="fa-solid fa-pen-nib text-purple-500"></i>
                </div>
                <span class="text-xs text-purple-600 bg-purple-50 px-2 py-0.5 rounded-full font-medium badge hidden sm:block">Essay</span>
            </div>
            <p class="text-2xl font-heading font-semibold text-slate-800">{{ $totalEssay }}</p>
            <p class="text-sm text-slate-400 mt-0.5">Essay</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">

        {{-- Toolbar --}}
        <div class="flex flex-col gap-3 px-5 py-4 border-b border-slate-100">
            <div class="flex flex-col sm:flex-row gap-3">
                {{-- Search --}}
                <form method="GET" action="{{ route('dosen.bank-soal.index') }}" class="flex-1 flex flex-wrap gap-2" id="filter-form">
                    <div class="relative flex-1 min-w-[180px]">
                        <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari soal..."
                            class="w-full pl-9 pr-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200 text-slate-700 placeholder:text-slate-400">
                    </div>
                    {{-- Filter Tipe --}}
                    <select name="tipe" onchange="document.getElementById('filter-form').submit()"
                        class="px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200 text-slate-700">
                        <option value="">Semua Tipe</option>
                        <option value="pilihan_ganda" {{ request('tipe')==='pilihan_ganda' ? 'selected' : '' }}>Pilihan Ganda</option>
                        <option value="essay" {{ request('tipe')==='essay' ? 'selected' : '' }}>Essay</option>
                    </select>
                    {{-- Filter Kategori --}}
                    <select name="kategori" onchange="document.getElementById('filter-form').submit()"
                        class="px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200 text-slate-700">
                        <option value="">Semua Kategori</option>
                        @foreach($kategori as $kat)
                        <option value="{{ $kat }}" {{ request('kategori')===$kat ? 'selected' : '' }}>{{ $kat }}</option>
                        @endforeach
                    </select>
                    {{-- Filter Kelas --}}
                    <select name="kelas_id" onchange="document.getElementById('filter-form').submit()"
                        class="px-3 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200 text-slate-700">
                        <option value="">Semua Kelas</option>
                        @foreach($kelasDiampu as $k)
                        <option value="{{ $k->id }}" {{ request('kelas_id')==$k->id ? 'selected' : '' }}>
                            {{ $k->mataKuliah->kode }} &mdash; Kelas {{ $k->nama_kelas }}
                        </option>
                        @endforeach
                    </select>
                </form>
                {{-- Aksi --}}
                <div class="flex items-center gap-2 flex-shrink-0">
                    <label for="importSoal"
                        class="inline-flex items-center gap-2 bg-sage-50 hover:bg-sage-100 text-sage-700 text-sm font-medium px-4 py-2.5 rounded-xl transition-colors cursor-pointer border border-sage-200">
                        <i class="fa-solid fa-file-excel text-sage-600 text-xs"></i>
                        <span class="hidden sm:inline">Import</span>
                    </label>
                    <input type="file" id="importSoal" accept=".xlsx,.xls,.csv" class="hidden"
                        onchange="alert('Import soal dari \''+this.files[0].name+'\'\nFitur ini memerlukan konfigurasi tambahan.');">
                    <button onclick="openModal('modal-tambah')"
                        class="inline-flex items-center gap-2 bg-ink-500 hover:bg-ink-600 text-white text-sm font-medium px-4 py-2.5 rounded-xl transition-colors">
                        <i class="fa-solid fa-plus text-xs"></i>
                        <span class="hidden sm:inline">Tambah Soal</span>
                    </button>
                </div>
            </div>
            {{-- Active filter chips --}}
            @if(request()->hasAny(['search','tipe','kategori','kelas_id']))
            <div class="flex flex-wrap gap-2">
                @if(request('search'))
                <a href="{{ request()->fullUrlWithoutQuery('search') }}" class="inline-flex items-center gap-1.5 text-xs bg-ink-50 text-ink-600 px-2.5 py-1 rounded-full hover:bg-ink-100 transition-colors">
                    <span>"{{ request('search') }}"</span><i class="fa-solid fa-xmark"></i>
                </a>
                @endif
                @if(request('tipe'))
                <a href="{{ request()->fullUrlWithoutQuery('tipe') }}" class="inline-flex items-center gap-1.5 text-xs bg-ink-50 text-ink-600 px-2.5 py-1 rounded-full hover:bg-ink-100 transition-colors">
                    <span>{{ request('tipe') === 'pilihan_ganda' ? 'Pilihan Ganda' : 'Essay' }}</span><i class="fa-solid fa-xmark"></i>
                </a>
                @endif
                @if(request('kategori'))
                <a href="{{ request()->fullUrlWithoutQuery('kategori') }}" class="inline-flex items-center gap-1.5 text-xs bg-ink-50 text-ink-600 px-2.5 py-1 rounded-full hover:bg-ink-100 transition-colors">
                    <span>{{ request('kategori') }}</span><i class="fa-solid fa-xmark"></i>
                </a>
                @endif
            </div>
            @endif
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide w-8">#</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Pertanyaan</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide w-28">Tipe</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden md:table-cell w-28">Kategori</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden sm:table-cell w-16">Bobot</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden lg:table-cell">Kelas</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($soal as $s)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-3.5 text-slate-400 text-xs">
                            {{ ($soal->currentPage()-1)*$soal->perPage()+$loop->iteration }}
                        </td>
                        <td class="px-4 py-3.5">
                            {{-- Pertanyaan + expand --}}
                            <div>
                                <p class="text-sm text-slate-800 leading-snug">{{ $s->pertanyaan_singkat }}</p>
                                {{-- Pilihan (PG): tampil toggle --}}
                                @if($s->tipe === 'pilihan_ganda' && $s->pilihan->count())
                                <button onclick="toggleSoal({{ $s->id }})"
                                    class="text-xs text-ink-500 hover:text-ink-700 mt-1 inline-flex items-center gap-1">
                                    <i class="fa-solid fa-chevron-down text-xs" id="icon-{{ $s->id }}"></i>
                                    <span id="label-{{ $s->id }}">Lihat Pilihan</span>
                                </button>
                                <div id="detail-{{ $s->id }}" class="hidden mt-2 space-y-1 pl-2 border-l-2 border-slate-200">
                                    @foreach($s->pilihan as $p)
                                    <div class="flex items-start gap-2">
                                        <span class="flex-shrink-0 w-5 h-5 rounded-full {{ $p->is_benar ? 'bg-sage-500 text-white' : 'bg-slate-200 text-slate-500' }} flex items-center justify-center text-xs font-bold">
                                            {{ chr(65 + $loop->index) }}
                                        </span>
                                        <p class="text-xs {{ $p->is_benar ? 'text-sage-700 font-medium' : 'text-slate-500' }} leading-snug pt-0.5">{{ $p->teks }}</p>
                                    </div>
                                    @endforeach
                                </div>
                                @elseif($s->tipe === 'essay' && $s->rubrik)
                                <button onclick="toggleSoal({{ $s->id }})"
                                    class="text-xs text-purple-500 hover:text-purple-700 mt-1 inline-flex items-center gap-1">
                                    <i class="fa-solid fa-chevron-down text-xs" id="icon-{{ $s->id }}"></i>
                                    <span id="label-{{ $s->id }}">Lihat Rubrik</span>
                                </button>
                                <div id="detail-{{ $s->id }}" class="hidden mt-2 p-3 bg-purple-50 rounded-xl border border-purple-100">
                                    <p class="text-xs font-medium text-purple-700 mb-1">Rubrik Penilaian:</p>
                                    <p class="text-xs text-slate-600 whitespace-pre-line">{{ $s->rubrik }}</p>
                                </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            @if($s->tipe === 'pilihan_ganda')
                            <span class="text-xs bg-sage-50 text-sage-700 px-2.5 py-1 rounded-full font-medium whitespace-nowrap">
                                <i class="fa-solid fa-list-ol mr-1"></i>PG
                            </span>
                            @else
                            <span class="text-xs bg-purple-50 text-purple-700 px-2.5 py-1 rounded-full font-medium">
                                <i class="fa-solid fa-pen-nib mr-1"></i>Essay
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 hidden md:table-cell">
                            @if($s->kategori)
                            <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded-lg">{{ $s->kategori }}</span>
                            @else
                            <span class="text-xs text-slate-300">&mdash;</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-center hidden sm:table-cell">
                            <span class="text-sm font-semibold font-mono text-slate-700">{{ $s->bobot }}</span>
                        </td>
                        <td class="px-4 py-3.5 hidden lg:table-cell">
                            @if($s->kelas)
                            <p class="text-xs text-slate-600">{{ $s->kelas->mataKuliah->kode }} &mdash; Kelas {{ $s->kelas->nama_kelas }}</p>
                            @else
                            <span class="text-xs text-slate-300">Umum</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center justify-center gap-1.5">
                                <button onclick="openEdit({{ $s->id }})"
                                    class="w-8 h-8 rounded-lg bg-ink-50 hover:bg-ink-100 text-ink-500 flex items-center justify-center transition-colors" title="Edit">
                                    <i class="fa-solid fa-pen text-xs"></i>
                                </button>
                                <form action="{{ route('dosen.bank-soal.destroy', $s) }}" method="POST"
                                    onsubmit="return confirm('Hapus soal ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="w-8 h-8 rounded-lg bg-red-50 hover:bg-red-100 text-red-400 flex items-center justify-center transition-colors" title="Hapus">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-16 text-center">
                            <i class="fa-solid fa-circle-question text-slate-200 text-5xl mb-4 block"></i>
                            <p class="text-slate-500 font-medium mb-1">Bank soal masih kosong</p>
                            <p class="text-slate-400 text-sm">
                                @if(request()->hasAny(['search','tipe','kategori','kelas_id']))
                                    Tidak ada soal yang sesuai filter.
                                    <a href="{{ route('dosen.bank-soal.index') }}" class="text-ink-500 hover:underline">Hapus filter</a>
                                @else
                                    Klik "Tambah Soal" untuk membuat soal pertama Anda.
                                @endif
                            </p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($soal->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between text-sm text-slate-500">
            <span>{{ $soal->firstItem() }}"“{{ $soal->lastItem() }} dari {{ $soal->total() }} soal</span>
            <div class="flex gap-1">
                @if(!$soal->onFirstPage())
                <a href="{{ $soal->previousPageUrl() }}" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-ink-50 hover:text-ink-500 flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-chevron-left text-xs"></i>
                </a>
                @endif
                @foreach($soal->getUrlRange(max(1,$soal->currentPage()-2), min($soal->lastPage(),$soal->currentPage()+2)) as $page => $url)
                <a href="{{ $url }}" class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-medium transition-colors {{ $page==$soal->currentPage() ? 'bg-ink-500 text-white' : 'bg-slate-100 hover:bg-ink-50 hover:text-ink-500 text-slate-600' }}">{{ $page }}</a>
                @endforeach
                @if($soal->hasMorePages())
                <a href="{{ $soal->nextPageUrl() }}" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-ink-50 hover:text-ink-500 flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </a>
                @endif
            </div>
        </div>
        @endif
    </div>

    {{-- ===================== MODAL TAMBAH SOAL ===================== --}}
    <div id="modal-tambah" class="hidden fixed inset-0 z-50 items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" onclick="closeModal('modal-tambah')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between sticky top-0 bg-white z-10">
                <h3 class="font-heading font-semibold text-slate-800">Tambah Soal Baru</h3>
                <button onclick="closeModal('modal-tambah')" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <form action="{{ route('dosen.bank-soal.store') }}" method="POST" class="px-6 py-5 space-y-5">
                @csrf

                {{-- Tipe Soal --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Tipe Soal <span class="text-red-400">*</span></label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center gap-3 p-3.5 rounded-xl border-2 border-slate-200 cursor-pointer hover:border-sage-300 transition-colors has-[:checked]:border-sage-500 has-[:checked]:bg-sage-50">
                            <input type="radio" name="tipe" value="pilihan_ganda" class="accent-sage-500" onchange="switchTipe('pg')" checked>
                            <div>
                                <p class="text-sm font-medium text-slate-700">Pilihan Ganda</p>
                                <p class="text-xs text-slate-400">4 opsi, 1 kunci jawaban</p>
                            </div>
                        </label>
                        <label class="flex items-center gap-3 p-3.5 rounded-xl border-2 border-slate-200 cursor-pointer hover:border-purple-300 transition-colors has-[:checked]:border-purple-500 has-[:checked]:bg-purple-50">
                            <input type="radio" name="tipe" value="essay" class="accent-purple-500" onchange="switchTipe('essay')">
                            <div>
                                <p class="text-sm font-medium text-slate-700">Essay</p>
                                <p class="text-xs text-slate-400">Jawaban bebas + rubrik</p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Pertanyaan --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Pertanyaan <span class="text-red-400">*</span></label>
                    <textarea name="pertanyaan" rows="4" required placeholder="Tulis pertanyaan di sini..."
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200 resize-none @error('pertanyaan') border-red-300 @enderror">{{ old('pertanyaan') }}</textarea>
                    @error('pertanyaan')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Opsi Pilihan Ganda --}}
                <div id="section-pg" class="space-y-3">
                    <label class="block text-sm font-medium text-slate-700">Pilihan Jawaban <span class="text-red-400">*</span></label>
                    <p class="text-xs text-slate-400 -mt-1">Pilih radio button di kiri untuk menandai jawaban benar.</p>
                    @foreach(['A','B','C','D'] as $i => $huruf)
                    <div class="flex items-center gap-3">
                        <label class="flex-shrink-0 flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="kunci" value="{{ $i }}" class="accent-sage-500 w-4 h-4"
                                {{ $i===0 ? 'checked' : '' }}>
                            <span class="w-7 h-7 rounded-lg bg-slate-100 flex items-center justify-center text-xs font-bold text-slate-600">{{ $huruf }}</span>
                        </label>
                        <input type="text" name="pilihan[{{ $i }}]" placeholder="Opsi {{ $huruf }}..."
                            class="flex-1 px-3 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200">
                    </div>
                    @endforeach
                </div>

                {{-- Rubrik Essay --}}
                <div id="section-essay" class="hidden">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Rubrik Penilaian</label>
                    <textarea name="rubrik" rows="4" placeholder="Jelaskan kriteria penilaian untuk jawaban essay ini..."
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200 resize-none">{{ old('rubrik') }}</textarea>
                </div>

                {{-- Metadata --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Kategori</label>
                        <input type="text" name="kategori" value="{{ old('kategori') }}" placeholder="contoh: Pertemuan 1, Bab 2..."
                            list="kategori-list"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200">
                        <datalist id="kategori-list">
                            @foreach($kategori as $kat)
                            <option value="{{ $kat }}">
                            @endforeach
                        </datalist>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Bobot Nilai <span class="text-red-400">*</span></label>
                        <input type="number" name="bobot" value="{{ old('bobot', 1) }}" min="1" max="100" required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-ink-200">
                    </div>
                </div>

                {{-- Kelas (opsional) --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">
                        Kaitkan ke Kelas
                        <span class="text-slate-400 font-normal text-xs ml-1">(opsional &mdash; kosongkan jika soal umum)</span>
                    </label>
                    <select name="kelas_id"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200 bg-white text-slate-700">
                        <option value="">&mdash; Soal Umum / Tanpa Kelas &mdash;</option>
                        @foreach($kelasDiampu as $k)
                        <option value="{{ $k->id }}" {{ old('kelas_id')==$k->id ? 'selected' : '' }}>
                            {{ $k->mataKuliah->kode }} &mdash; {{ $k->mataKuliah->nama }} Kelas {{ $k->nama_kelas }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-3 pt-2 border-t border-slate-100">
                    <button type="button" onclick="closeModal('modal-tambah')"
                        class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50">Batal</button>
                    <button type="submit"
                        class="flex-1 px-4 py-2.5 rounded-xl bg-ink-500 hover:bg-ink-600 text-white text-sm font-medium transition-colors">
                        <i class="fa-solid fa-plus mr-1.5 text-xs"></i>Simpan Soal
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===================== MODAL EDIT SOAL ===================== --}}
    <div id="modal-edit" class="hidden fixed inset-0 z-50 items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" onclick="closeModal('modal-edit')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between sticky top-0 bg-white z-10">
                <div>
                    <h3 class="font-heading font-semibold text-slate-800">Edit Soal</h3>
                    <p id="edit-tipe-label" class="text-xs text-slate-400 mt-0.5"></p>
                </div>
                <button onclick="closeModal('modal-edit')" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <form id="form-edit" method="POST" class="px-6 py-5 space-y-5">
                @csrf @method('PATCH')

                {{-- Pertanyaan --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Pertanyaan <span class="text-red-400">*</span></label>
                    <textarea name="pertanyaan" id="edit-pertanyaan" rows="4" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200 resize-none"></textarea>
                </div>

                {{-- Opsi PG (edit) --}}
                <div id="edit-section-pg" class="space-y-3">
                    <label class="block text-sm font-medium text-slate-700">Pilihan Jawaban</label>
                    @foreach(['A','B','C','D'] as $i => $huruf)
                    <div class="flex items-center gap-3">
                        <label class="flex-shrink-0 flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="kunci" value="{{ $i }}" class="accent-sage-500 w-4 h-4" id="edit-kunci-{{ $i }}">
                            <span class="w-7 h-7 rounded-lg bg-slate-100 flex items-center justify-center text-xs font-bold text-slate-600">{{ $huruf }}</span>
                        </label>
                        <input type="text" name="pilihan[{{ $i }}]" id="edit-pilihan-{{ $i }}"
                            placeholder="Opsi {{ $huruf }}..."
                            class="flex-1 px-3 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200">
                    </div>
                    @endforeach
                </div>

                {{-- Rubrik Essay (edit) --}}
                <div id="edit-section-essay" class="hidden">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Rubrik Penilaian</label>
                    <textarea name="rubrik" id="edit-rubrik" rows="4"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200 resize-none"></textarea>
                </div>

                {{-- Metadata --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Kategori</label>
                        <input type="text" name="kategori" id="edit-kategori"
                            list="kategori-list"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Bobot Nilai <span class="text-red-400">*</span></label>
                        <input type="number" name="bobot" id="edit-bobot" min="1" max="100" required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-ink-200">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Kaitkan ke Kelas</label>
                    <select name="kelas_id" id="edit-kelas"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200 bg-white text-slate-700">
                        <option value="">&mdash; Soal Umum &mdash;</option>
                        @foreach($kelasDiampu as $k)
                        <option value="{{ $k->id }}">
                            {{ $k->mataKuliah->kode }} &mdash; {{ $k->mataKuliah->nama }} Kelas {{ $k->nama_kelas }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-3 pt-2 border-t border-slate-100">
                    <button type="button" onclick="closeModal('modal-edit')"
                        class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50">Batal</button>
                    <button type="submit"
                        class="flex-1 px-4 py-2.5 rounded-xl bg-ink-500 hover:bg-ink-600 text-white text-sm font-medium transition-colors">
                        <i class="fa-solid fa-floppy-disk mr-1.5 text-xs"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Data soal untuk JS (edit modal) --}}
    <script id="soal-data" type="application/json">
        {!! json_encode($soal->map(function($s) {
            return [
                'id'          => $s->id,
                'tipe'        => $s->tipe,
                'pertanyaan'  => $s->pertanyaan,
                'rubrik'      => $s->rubrik,
                'bobot'       => $s->bobot,
                'kategori'    => $s->kategori,
                'kelas_id'    => $s->kelas_id,
                'pilihan'     => $s->pilihan->map(fn($p) => ['teks' => $p->teks, 'is_benar' => $p->is_benar])->values(),
            ];
        })) !!}
    </script>

    @push('scripts')
    <script>
    const soalData = JSON.parse(document.getElementById('soal-data').textContent);

    function openModal(id) { const el = document.getElementById(id); el.classList.remove('hidden'); el.classList.add('flex'); }
    function closeModal(id) { const el = document.getElementById(id); el.classList.add('hidden'); el.classList.remove('flex'); }

    // Toggle expand pilihan/rubrik di tabel
    function toggleSoal(id) {
        const detail = document.getElementById('detail-' + id);
        const icon   = document.getElementById('icon-'   + id);
        const label  = document.getElementById('label-'  + id);
        const open   = detail.classList.toggle('hidden');
        icon.style.transform = open ? '' : 'rotate(180deg)';
        if (label) label.textContent = open ? (detail.querySelector('.border-l-2') ? 'Lihat Pilihan' : 'Lihat Rubrik') : 'Sembunyikan';
    }

    // Switch form tambah antara PG dan Essay
    function switchTipe(tipe) {
        document.getElementById('section-pg').classList.toggle('hidden', tipe !== 'pg');
        document.getElementById('section-essay').classList.toggle('hidden', tipe !== 'essay');
    }

    // Buka modal edit dengan data soal
    function openEdit(id) {
        const s = soalData.find(x => x.id === id);
        if (!s) return;

        document.getElementById('form-edit').action = '/dosen/bank-soal/' + id;
        document.getElementById('edit-tipe-label').textContent  = s.tipe === 'pilihan_ganda' ? 'Pilihan Ganda' : 'Essay';
        document.getElementById('edit-pertanyaan').value = s.pertanyaan;
        document.getElementById('edit-rubrik').value     = s.rubrik ?? '';
        document.getElementById('edit-bobot').value      = s.bobot;
        document.getElementById('edit-kategori').value   = s.kategori ?? '';
        document.getElementById('edit-kelas').value      = s.kelas_id ?? '';

        // Show/hide section
        const isPg = s.tipe === 'pilihan_ganda';
        document.getElementById('edit-section-pg').classList.toggle('hidden', !isPg);
        document.getElementById('edit-section-essay').classList.toggle('hidden', isPg);

        // Isi pilihan PG
        if (isPg) {
            s.pilihan.forEach((p, i) => {
                const inp = document.getElementById('edit-pilihan-' + i);
                if (inp) inp.value = p.teks;
                const radio = document.getElementById('edit-kunci-' + i);
                if (radio) radio.checked = p.is_benar;
            });
        }

        openModal('modal-edit');
    }

    @if($errors->any())
        openModal('modal-tambah');
    @endif
    </script>
    @endpush

</x-app-layout>
