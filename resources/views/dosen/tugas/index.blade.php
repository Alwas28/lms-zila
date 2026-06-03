<x-app-layout>
    <x-slot name="title">Tugas &mdash; {{ $kelas->mataKuliah->nama }}</x-slot>
    <x-slot name="pageTitle">Tugas</x-slot>

    @include('admin._flash')

    {{-- Breadcrumb + Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
        <div>
            <nav class="flex items-center gap-1.5 text-xs text-slate-400 mb-1">
                <a href="{{ route('dosen.mata-kuliah.index') }}" class="hover:text-ink-500 transition-colors">Mata Kuliah Saya</a>
                <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
                <span class="text-slate-600">{{ $kelas->mataKuliah->nama }}</span>
                <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
                <span class="text-ink-500 font-medium">Tugas</span>
            </nav>
            <h2 class="font-heading font-semibold text-slate-800 text-lg">
                {{ $kelas->mataKuliah->nama }}
                <span class="text-slate-400 font-normal text-sm">&mdash; Kelas {{ $kelas->nama_kelas }}</span>
            </h2>
        </div>
        <button onclick="openModal('modal-tambah')"
            class="inline-flex items-center gap-2 bg-ink-500 hover:bg-ink-600 text-white text-sm font-medium px-4 py-2.5 rounded-xl transition-colors flex-shrink-0">
            <i class="fa-solid fa-plus text-xs"></i> Buat Tugas
        </button>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Tugas</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide w-24 hidden sm:table-cell">Tipe</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide w-32 hidden md:table-cell">Deadline</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide w-28">Dikumpulkan</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide w-24 hidden lg:table-cell">Rata-rata</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($tugas as $t)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-3.5">
                            <p class="font-medium text-slate-800 leading-snug">{{ $t->judul }}</p>
                            @if($t->pertemuan)
                            <p class="text-xs text-slate-400 mt-0.5">Pertemuan {{ $t->pertemuan->nomor }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-center hidden sm:table-cell">
                            @if($t->tipe === 'individu')
                            <span class="text-xs bg-ink-50 text-ink-600 px-2 py-1 rounded-full font-medium">Individu</span>
                            @else
                            <span class="text-xs bg-sage-50 text-sage-600 px-2 py-1 rounded-full font-medium">Kelompok</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 hidden md:table-cell">
                            @if($t->deadline)
                            <span class="text-xs {{ $t->is_expired ? 'text-red-500 font-medium' : 'text-slate-600' }}">
                                {{ $t->deadline->format('d M Y') }}
                                @if($t->is_expired)
                                <span class="block text-red-400">Berakhir</span>
                                @else
                                <span class="block text-slate-400">{{ $t->deadline->format('H:i') }}</span>
                                @endif
                            </span>
                            @else
                            <span class="text-xs text-slate-300">&mdash;</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            <span class="text-sm font-semibold {{ $t->submissions_count >= $totalMahasiswa ? 'text-sage-600' : 'text-slate-700' }}">
                                {{ $t->submissions_count }}/{{ $totalMahasiswa }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-center hidden lg:table-cell">
                            @php
                                $avg = $t->submissions()->whereNotNull('nilai')->avg('nilai');
                            @endphp
                            @if($avg !== null)
                            <span class="text-sm font-semibold text-slate-700">{{ number_format($avg, 1) }}</span>
                            @else
                            <span class="text-xs text-slate-300">&mdash;</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('dosen.tugas.show', [$kelas, $t]) }}"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-ink-50 hover:bg-ink-100 text-ink-600 text-xs font-medium transition-colors">
                                    <i class="fa-solid fa-eye text-xs"></i> Detail
                                </a>
                                <form action="{{ route('dosen.tugas.destroy', [$kelas, $t]) }}" method="POST"
                                    onsubmit="return confirm('Hapus tugas {{ addslashes($t->judul) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-7 h-7 rounded-lg bg-red-50 hover:bg-red-100 text-red-400 flex items-center justify-center transition-colors" title="Hapus">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-16 text-center">
                            <i class="fa-solid fa-list-check text-slate-200 text-4xl mb-3 block"></i>
                            <p class="text-slate-400 text-sm">Belum ada tugas untuk kelas ini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tugas->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between text-sm text-slate-500">
            <span>{{ $tugas->firstItem() }}"“{{ $tugas->lastItem() }} dari {{ $tugas->total() }}</span>
            <div class="flex gap-1">
                @if(!$tugas->onFirstPage())<a href="{{ $tugas->previousPageUrl() }}" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-ink-50 hover:text-ink-500 flex items-center justify-center text-slate-500"><i class="fa-solid fa-chevron-left text-xs"></i></a>@endif
                @if($tugas->hasMorePages())<a href="{{ $tugas->nextPageUrl() }}" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-ink-50 hover:text-ink-500 flex items-center justify-center text-slate-500"><i class="fa-solid fa-chevron-right text-xs"></i></a>@endif
            </div>
        </div>
        @endif
    </div>

    {{-- Modal Tambah Tugas --}}
    <div id="modal-tambah" class="hidden fixed inset-0 z-50 items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" onclick="closeModal('modal-tambah')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-heading font-semibold text-slate-800">Buat Tugas Baru</h3>
                <button onclick="closeModal('modal-tambah')" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <form action="{{ route('dosen.tugas.store', $kelas) }}" method="POST" class="px-6 py-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Judul Tugas <span class="text-red-400">*</span></label>
                    <input type="text" name="judul" required value="{{ old('judul') }}" placeholder="Judul tugas..."
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Deskripsi</label>
                    <textarea name="deskripsi" rows="3" placeholder="Deskripsi dan instruksi tugas..."
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200 resize-none">{{ old('deskripsi') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Tipe Tugas <span class="text-red-400">*</span></label>
                    <div class="flex gap-3">
                        <label class="flex-1 flex items-center gap-2 p-3 rounded-xl border border-slate-200 cursor-pointer hover:border-ink-300 has-[:checked]:border-ink-500 has-[:checked]:bg-ink-50 transition-all">
                            <input type="radio" name="tipe" value="individu" required {{ old('tipe','individu') === 'individu' ? 'checked' : '' }} class="text-ink-500">
                            <span class="text-sm font-medium text-slate-700">Individu</span>
                        </label>
                        <label class="flex-1 flex items-center gap-2 p-3 rounded-xl border border-slate-200 cursor-pointer hover:border-ink-300 has-[:checked]:border-ink-500 has-[:checked]:bg-ink-50 transition-all">
                            <input type="radio" name="tipe" value="kelompok" {{ old('tipe') === 'kelompok' ? 'checked' : '' }} class="text-ink-500">
                            <span class="text-sm font-medium text-slate-700">Kelompok</span>
                        </label>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Pertemuan (opsional)</label>
                    <select name="pertemuan_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200">
                        <option value="">&mdash; Tidak terkait pertemuan &mdash;</option>
                        @foreach($pertemuan as $p)
                        <option value="{{ $p->id }}" {{ old('pertemuan_id') == $p->id ? 'selected' : '' }}>
                            Pertemuan {{ $p->nomor }}{{ $p->topik ? ' &mdash; ' . $p->topik : '' }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Deadline <span class="text-red-400">*</span></label>
                        <input type="datetime-local" name="deadline" required value="{{ old('deadline') }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Nilai Maks <span class="text-red-400">*</span></label>
                        <input type="number" name="nilai_maks" required value="{{ old('nilai_maks', 100) }}" min="1" max="1000"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200">
                    </div>
                </div>
                <div class="flex gap-3 pt-1">
                    <button type="button" onclick="closeModal('modal-tambah')" class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl bg-ink-500 hover:bg-ink-600 text-white text-sm font-medium transition-colors">
                        <i class="fa-solid fa-plus mr-1.5 text-xs"></i>Buat
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function openModal(id) { const el = document.getElementById(id); el.classList.remove('hidden'); el.classList.add('flex'); }
        function closeModal(id) { const el = document.getElementById(id); el.classList.add('hidden'); el.classList.remove('flex'); }
        @if($errors->any()) openModal('modal-tambah'); @endif
    </script>
    @endpush
</x-app-layout>
