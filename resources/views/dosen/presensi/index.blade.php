<x-app-layout>
    <x-slot name="title">Presensi &mdash; {{ $kelas->mataKuliah->nama }}</x-slot>
    <x-slot name="pageTitle">Presensi</x-slot>

    @include('admin._flash')

    {{-- Breadcrumb + Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
        <div>
            <nav class="flex items-center gap-1.5 text-xs text-slate-400 mb-1">
                <a href="{{ route('dosen.mata-kuliah.index') }}" class="hover:text-ink-500 transition-colors">Mata Kuliah Saya</a>
                <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
                <span class="text-slate-600">{{ $kelas->mataKuliah->nama }}</span>
                <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
                <span class="text-ink-500 font-medium">Presensi</span>
            </nav>
            <h2 class="font-heading font-semibold text-slate-800 text-lg">
                {{ $kelas->mataKuliah->nama }}
                <span class="text-slate-400 font-normal text-sm">&mdash; Kelas {{ $kelas->nama_kelas }}</span>
            </h2>
        </div>
    </div>

    {{-- Summary Stats --}}
    @php
        $totalPertemuan = $pertemuan->count();
        $aktif = $pertemuan->filter(fn($p) => $p->presensiSesi && $p->presensiSesi->is_aktif)->count();
        $totalHadir = 0;
        $totalPossible = 0;
        foreach ($pertemuan as $p) {
            if ($p->presensiSesi) {
                $totalHadir += $p->presensiSesi->presensi->where('status', 'hadir')->count();
                $totalPossible += $p->presensiSesi->presensi->count();
            }
        }
        $avgKehadiran = $totalPossible > 0 ? round($totalHadir / $totalPossible * 100) : 0;
    @endphp
    <div class="grid grid-cols-3 gap-3 mb-6">
        <div class="bg-white rounded-2xl p-4 border border-slate-200 card-hover flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-ink-50 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-calendar-days text-ink-500"></i>
            </div>
            <div>
                <p class="text-xl font-heading font-semibold text-slate-800">{{ $totalPertemuan }}</p>
                <p class="text-xs text-slate-400">Total Pertemuan</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-slate-200 card-hover flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-ember-50 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-signal text-ember-500"></i>
            </div>
            <div>
                <p class="text-xl font-heading font-semibold text-slate-800">{{ $aktif }}</p>
                <p class="text-xs text-slate-400">Sesi Aktif</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-slate-200 card-hover flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-sage-50 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-user-check text-sage-500"></i>
            </div>
            <div>
                <p class="text-xl font-heading font-semibold text-slate-800">{{ $avgKehadiran }}%</p>
                <p class="text-xs text-slate-400">Rata-rata Hadir</p>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide w-12">No</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Topik</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide w-28 hidden md:table-cell">Tanggal</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide w-24">Hadir</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide w-28">Status Sesi</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide text-center w-48">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pertemuan as $p)
                    @php
                        $sesi = $p->presensiSesi;
                        $hadirCount = $sesi ? $sesi->presensi->where('status','hadir')->count() : 0;
                        $totalMhs = $mahasiswa->count();
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3.5 text-center">
                            <span class="text-sm font-semibold text-slate-400">{{ $p->nomor }}</span>
                        </td>
                        <td class="px-5 py-3.5">
                            <p class="font-medium text-slate-800">{{ $p->topik ?: 'Belum ada topik' }}</p>
                            <p class="text-xs text-slate-400">Pertemuan {{ $p->nomor }}</p>
                        </td>
                        <td class="px-4 py-3.5 hidden md:table-cell">
                            <p class="text-xs text-slate-600">{{ $p->tanggal ? $p->tanggal->format('d M Y') : '&mdash;' }}</p>
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            @if($sesi)
                            <span class="text-sm font-semibold {{ $hadirCount >= $totalMhs ? 'text-sage-600' : 'text-slate-700' }}">
                                {{ $hadirCount }}/{{ $totalMhs }}
                            </span>
                            @else
                            <span class="text-slate-300 text-xs">&mdash;</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-center">
                            @if(!$sesi)
                            <span class="text-xs bg-slate-100 text-slate-400 px-2 py-1 rounded-full font-medium">Belum Dibuka</span>
                            @elseif($sesi->is_aktif)
                            <div class="flex flex-col items-center gap-0.5">
                                <span class="text-xs bg-ember-50 text-ember-600 px-2 py-1 rounded-full font-medium">
                                    <i class="fa-solid fa-signal text-xs mr-0.5"></i> Aktif
                                </span>
                                <span class="text-xs text-slate-400 font-mono">{{ $sesi->kode }}</span>
                            </div>
                            @else
                            <span class="text-xs bg-sage-50 text-sage-600 px-2 py-1 rounded-full font-medium">Tutup</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center justify-center gap-1.5">
                                @if(!$sesi || !$sesi->is_aktif)
                                <form action="{{ route('dosen.presensi.buka', [$kelas, $p]) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-ember-50 hover:bg-ember-100 text-ember-600 text-xs font-medium transition-colors">
                                        <i class="fa-solid fa-play text-xs"></i> Buka
                                    </button>
                                </form>
                                @else
                                <form action="{{ route('dosen.presensi.tutup', [$kelas, $sesi]) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-medium transition-colors">
                                        <i class="fa-solid fa-stop text-xs"></i> Tutup
                                    </button>
                                </form>
                                @endif

                                @if($sesi)
                                <button onclick="openKelola({{ $sesi->id }}, {{ $p->nomor }})"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-ink-50 hover:bg-ink-100 text-ink-600 text-xs font-medium transition-colors">
                                    <i class="fa-solid fa-users text-xs"></i> Kelola
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-16 text-center">
                            <i class="fa-solid fa-calendar-days text-slate-200 text-4xl mb-3 block"></i>
                            <p class="text-slate-400 text-sm">Belum ada pertemuan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Kelola Presensi --}}
    @foreach($pertemuan as $p)
    @if($p->presensiSesi)
    @php $sesi = $p->presensiSesi; @endphp
    <div id="modal-kelola-{{ $sesi->id }}" class="hidden fixed inset-0 z-50 items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" onclick="closeModal('modal-kelola-{{ $sesi->id }}')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-heading font-semibold text-slate-800">Kelola Presensi</h3>
                    <p class="text-xs text-slate-400">Pertemuan {{ $p->nomor }} &mdash; {{ $p->topik }}</p>
                </div>
                <button onclick="closeModal('modal-kelola-{{ $sesi->id }}')" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <form action="{{ route('dosen.presensi.status', [$kelas, $sesi]) }}" method="POST">
                @csrf @method('PATCH')
                <div class="px-6 py-4">
                    @if($mahasiswa->isEmpty())
                    <p class="text-sm text-slate-400 text-center py-8">Belum ada mahasiswa terdaftar.</p>
                    @else
                    <div class="space-y-2">
                        @foreach($mahasiswa as $mhs)
                        @php
                            $pres = $sesi->presensi->firstWhere('user_id', $mhs->id);
                            $status = $pres ? $pres->status : 'alpha';
                        @endphp
                        <div class="flex items-center justify-between gap-3 py-2.5 border-b border-slate-50">
                            <div>
                                <p class="text-sm font-medium text-slate-800">{{ $mhs->name }}</p>
                                <p class="text-xs text-slate-400 font-mono">{{ $mhs->nip_nim ?? '&mdash;' }}</p>
                            </div>
                            <div class="flex gap-1.5">
                                @foreach([
                                    'hadir'  => ['bg-sage-50 text-sage-600 border-sage-300',  'peer-checked:bg-sage-500'],
                                    'izin'   => ['bg-ember-50 text-ember-600 border-ember-300', 'peer-checked:bg-ember-500'],
                                    'sakit'  => ['bg-blue-50 text-blue-600 border-blue-300',   'peer-checked:bg-blue-500'],
                                    'alpha'  => ['bg-red-50 text-red-600 border-red-300',       'peer-checked:bg-red-500'],
                                ] as $s => $cls)
                                <label class="cursor-pointer">
                                    <input type="radio" name="status[{{ $mhs->id }}]" value="{{ $s }}" {{ $status === $s ? 'checked' : '' }} class="sr-only peer">
                                    <span class="px-2.5 py-1 rounded-lg border text-xs font-medium transition-all {{ $cls[0] }} {{ $cls[1] }} peer-checked:text-white peer-checked:border-transparent">
                                        {{ ucfirst($s) }}
                                    </span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                <div class="px-6 py-4 border-t border-slate-100 flex gap-3">
                    <button type="button" onclick="closeModal('modal-kelola-{{ $sesi->id }}')" class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50">Tutup</button>
                    @if(!$mahasiswa->isEmpty())
                    <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl bg-ink-500 hover:bg-ink-600 text-white text-sm font-medium transition-colors">
                        <i class="fa-solid fa-floppy-disk mr-1.5 text-xs"></i>Simpan
                    </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
    @endif
    @endforeach

    @push('scripts')
    <script>
        function openModal(id) { const el = document.getElementById(id); el.classList.remove('hidden'); el.classList.add('flex'); }
        function closeModal(id) { const el = document.getElementById(id); el.classList.add('hidden'); el.classList.remove('flex'); }
        function openKelola(sesiId, nomor) {
            openModal('modal-kelola-' + sesiId);
        }
    </script>
    @endpush
</x-app-layout>
