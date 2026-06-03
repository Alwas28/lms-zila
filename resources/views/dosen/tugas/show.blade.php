<x-app-layout>
    <x-slot name="title">{{ $tugas->judul }}</x-slot>
    <x-slot name="pageTitle">Detail Tugas</x-slot>

    @include('admin._flash')

    {{-- Breadcrumb --}}
    <div class="mb-6">
        <nav class="flex items-center gap-1.5 text-xs text-slate-400 mb-1">
            <a href="{{ route('dosen.mata-kuliah.index') }}" class="hover:text-ink-500 transition-colors">Mata Kuliah Saya</a>
            <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
            <span class="text-slate-600">{{ $kelas->mataKuliah->nama }}</span>
            <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
            <a href="{{ route('dosen.tugas.index', $kelas) }}" class="hover:text-ink-500 transition-colors">Tugas</a>
            <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
            <span class="text-ink-500 font-medium truncate max-w-xs">{{ $tugas->judul }}</span>
        </nav>
    </div>

    {{-- Tugas Info Card --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-5 mb-5">
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
            <div class="flex-1">
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    @if($tugas->tipe === 'individu')
                    <span class="text-xs bg-ink-50 text-ink-600 px-2.5 py-1 rounded-full font-medium">Individu</span>
                    @else
                    <span class="text-xs bg-sage-50 text-sage-600 px-2.5 py-1 rounded-full font-medium">Kelompok</span>
                    @endif
                    @if($tugas->is_expired)
                    <span class="text-xs bg-red-50 text-red-500 px-2.5 py-1 rounded-full font-medium">Berakhir</span>
                    @endif
                </div>
                <h2 class="font-heading font-bold text-slate-800 text-xl mb-1">{{ $tugas->judul }}</h2>
                @if($tugas->deskripsi)
                <p class="text-sm text-slate-500 leading-relaxed">{{ $tugas->deskripsi }}</p>
                @endif
            </div>
            <div class="flex-shrink-0 text-right">
                <p class="text-xs text-slate-400 mb-0.5">Deadline</p>
                <p class="text-sm font-semibold {{ $tugas->is_expired ? 'text-red-500' : 'text-slate-700' }}">
                    {{ $tugas->deadline ? $tugas->deadline->format('d M Y, H:i') : '—' }}
                </p>
                <p class="text-xs text-slate-400 mt-1">Nilai Maks: <strong class="text-slate-700">{{ $tugas->nilai_maks }}</strong></p>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        <div class="bg-white rounded-2xl p-4 border border-slate-200 card-hover">
            <p class="text-2xl font-heading font-bold text-slate-800">{{ $submissions->count() + $belumKumpul->count() }}</p>
            <p class="text-xs text-slate-400 mt-0.5">Total Peserta</p>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-sage-200 card-hover">
            <p class="text-2xl font-heading font-bold text-sage-600">{{ $submissions->count() }}</p>
            <p class="text-xs text-slate-400 mt-0.5">Sudah Kumpul</p>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-red-100 card-hover">
            <p class="text-2xl font-heading font-bold text-red-500">{{ $belumKumpul->count() }}</p>
            <p class="text-xs text-slate-400 mt-0.5">Belum Kumpul</p>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-ink-100 card-hover">
            <p class="text-2xl font-heading font-bold text-ink-600">{{ $rataRata !== null ? number_format($rataRata, 1) : '—' }}</p>
            <p class="text-xs text-slate-400 mt-0.5">Rata-rata Nilai</p>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="mb-4">
        <div class="flex gap-1 bg-slate-100 p-1 rounded-xl w-fit">
            <button onclick="switchTab('tab-kumpul')" id="btn-tab-kumpul"
                class="tab-btn px-4 py-2 rounded-lg text-sm font-medium transition-all bg-white text-slate-800 shadow-sm">
                Sudah Mengumpulkan ({{ $submissions->count() }})
            </button>
            <button onclick="switchTab('tab-belum')" id="btn-tab-belum"
                class="tab-btn px-4 py-2 rounded-lg text-sm font-medium transition-all text-slate-500 hover:text-slate-700">
                Belum Mengumpulkan ({{ $belumKumpul->count() }})
            </button>
        </div>
    </div>

    {{-- Tab: Sudah Kumpul --}}
    <div id="tab-kumpul" class="tab-panel">
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
            @if($submissions->isEmpty())
            <div class="px-5 py-12 text-center">
                <i class="fa-solid fa-inbox text-slate-200 text-4xl mb-3 block"></i>
                <p class="text-slate-400 text-sm">Belum ada mahasiswa yang mengumpulkan.</p>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Mahasiswa</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide w-32 hidden md:table-cell">Waktu Submit</th>
                            <th class="text-center px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide w-24">File</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Nilai & Feedback</th>
                            <th class="px-4 py-3 w-20"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($submissions as $sub)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3.5">
                                <p class="font-medium text-slate-800">{{ $sub->mahasiswa->name }}</p>
                                <p class="text-xs text-slate-400 font-mono">{{ $sub->mahasiswa->nip_nim ?? '—' }}</p>
                            </td>
                            <td class="px-4 py-3.5 hidden md:table-cell">
                                <p class="text-xs text-slate-600">{{ $sub->submitted_at ? $sub->submitted_at->format('d M Y') : '—' }}</p>
                                <p class="text-xs text-slate-400">{{ $sub->submitted_at ? $sub->submitted_at->format('H:i') : '' }}</p>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                @if($sub->file_path)
                                <a href="{{ asset('storage/' . $sub->file_path) }}" target="_blank"
                                    class="w-8 h-8 rounded-lg bg-ink-50 hover:bg-ink-100 text-ink-500 inline-flex items-center justify-center transition-colors" title="Unduh">
                                    <i class="fa-solid fa-download text-xs"></i>
                                </a>
                                @else
                                <span class="text-slate-300 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5">
                                <form action="{{ route('dosen.tugas.nilai', [$kelas, $tugas, $sub]) }}" method="POST" class="flex items-start gap-2">
                                    @csrf @method('PATCH')
                                    <div class="flex flex-col gap-1.5">
                                        <input type="number" name="nilai" value="{{ $sub->nilai }}"
                                            min="0" max="{{ $tugas->nilai_maks }}" step="0.5"
                                            placeholder="0 – {{ $tugas->nilai_maks }}"
                                            class="w-24 px-3 py-1.5 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200 text-center">
                                        <textarea name="feedback" rows="1" placeholder="Feedback..."
                                            class="w-40 px-3 py-1.5 rounded-lg border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-ink-200 resize-none">{{ $sub->feedback }}</textarea>
                                    </div>
                                    <button type="submit" class="mt-0.5 px-3 py-1.5 rounded-lg bg-ink-500 hover:bg-ink-600 text-white text-xs font-medium transition-colors flex-shrink-0">
                                        Simpan
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

    {{-- Tab: Belum Kumpul --}}
    <div id="tab-belum" class="tab-panel hidden">
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
            @if($belumKumpul->isEmpty())
            <div class="px-5 py-12 text-center">
                <i class="fa-solid fa-circle-check text-sage-200 text-4xl mb-3 block"></i>
                <p class="text-slate-400 text-sm">Semua mahasiswa sudah mengumpulkan tugas.</p>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Mahasiswa</th>
                            <th class="text-center px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($belumKumpul as $mhs)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3.5">
                                <p class="font-medium text-slate-800">{{ $mhs->name }}</p>
                                <p class="text-xs text-slate-400 font-mono">{{ $mhs->nip_nim ?? '—' }}</p>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <span class="text-xs bg-red-50 text-red-500 px-2.5 py-1 rounded-full font-medium">Belum Kumpul</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        function switchTab(active) {
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
            document.getElementById(active).classList.remove('hidden');
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.remove('bg-white', 'text-slate-800', 'shadow-sm');
                b.classList.add('text-slate-500');
            });
            const btn = active === 'tab-kumpul' ? 'btn-tab-kumpul' : 'btn-tab-belum';
            const el = document.getElementById(btn);
            el.classList.add('bg-white', 'text-slate-800', 'shadow-sm');
            el.classList.remove('text-slate-500');
        }
    </script>
    @endpush
</x-app-layout>
