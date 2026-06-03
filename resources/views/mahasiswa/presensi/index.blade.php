<x-app-layout>
    <x-slot name="title">Presensi — {{ $kelas->mataKuliah->nama }}</x-slot>
    <x-slot name="pageTitle">Presensi</x-slot>

    @include('admin._flash')

    <div class="mb-6">
        <nav class="flex items-center gap-1.5 text-xs text-slate-400 mb-1">
            <a href="{{ route('mahasiswa.kelas.index') }}" class="hover:text-ink-500">Mata Kuliah Saya</a>
            <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
            <span class="text-slate-600">{{ $kelas->mataKuliah->nama }}</span>
            <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
            <span class="text-ink-500 font-medium">Presensi</span>
        </nav>
        <h2 class="font-heading font-semibold text-slate-800 text-lg">
            {{ $kelas->mataKuliah->nama }}
            <span class="text-slate-400 font-normal text-sm">— Kelas {{ $kelas->nama_kelas }}</span>
        </h2>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Kolom kiri: check-in + rekap --}}
        <div class="space-y-4">
            {{-- Status sesi (info saja, tidak ada form check-in) --}}
            @if($sesiAktif)
            <div class="bg-sage-50 border border-sage-200 rounded-2xl p-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-sage-500 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-circle-dot text-white animate-pulse"></i>
                    </div>
                    <div>
                        <p class="font-heading font-semibold text-sage-800 text-sm">Presensi Sedang Berlangsung</p>
                        <p class="text-xs text-sage-600 mt-0.5">Dosen sedang mencatat kehadiran. Status Anda akan diperbarui oleh dosen.</p>
                    </div>
                </div>
            </div>
            @else
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-5 text-center">
                <i class="fa-solid fa-door-closed text-slate-300 text-2xl mb-2"></i>
                <p class="text-sm text-slate-500 font-medium">Tidak ada sesi aktif</p>
                <p class="text-xs text-slate-400 mt-0.5">Presensi dikelola oleh dosen</p>
            </div>
            @endif

            {{-- Rekap --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-5">
                <h3 class="font-heading font-semibold text-slate-800 text-sm mb-3">Rekap Kehadiran</h3>
                @php $pct = $rekap['total'] > 0 ? round($rekap['hadir'] / $rekap['total'] * 100) : 0; @endphp
                <div class="mb-3">
                    <div class="flex justify-between text-xs text-slate-500 mb-1">
                        <span>Persentase Hadir</span>
                        <span class="font-semibold {{ $pct >= 75 ? 'text-sage-600' : 'text-red-500' }}">{{ $pct }}%</span>
                    </div>
                    <div class="h-2.5 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-2.5 rounded-full {{ $pct >= 75 ? 'bg-sage-500' : 'bg-red-400' }}" style="width:{{ $pct }}%"></div>
                    </div>
                    @if($pct < 75)
                    <p class="text-xs text-red-500 mt-1">⚠ Minimum kehadiran 75%</p>
                    @endif
                </div>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    @foreach(['hadir' => ['bg-sage-50 text-sage-700', 'Hadir'], 'izin' => ['bg-ember-50 text-ember-700', 'Izin'], 'sakit' => ['bg-blue-50 text-blue-700', 'Sakit'], 'alpha' => ['bg-red-50 text-red-700', 'Alpha']] as $k => $info)
                    <div class="flex items-center justify-between {{ $info[0] }} rounded-xl px-3 py-2">
                        <span>{{ $info[1] }}</span>
                        <span class="font-bold">{{ $rekap[$k] }}</span>
                    </div>
                    @endforeach
                </div>
                <p class="text-xs text-slate-400 mt-2 text-right">Total: {{ $rekap['total'] }} pertemuan</p>
            </div>
        </div>

        {{-- Kolom kanan: tabel detail --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                <div class="px-5 py-3 border-b border-slate-100">
                    <p class="font-heading font-semibold text-slate-800 text-sm">Detail Kehadiran Per Pertemuan</p>
                </div>
                <div class="divide-y divide-slate-50">
                    @foreach($pertemuan as $p)
                    @php
                        $sesi = $p->presensiSesi;
                        $pres = $sesi ? ($presensiMap[$sesi->id] ?? null) : null;
                        $status = $pres?->status;
                        $badgeMap = [
                            'hadir' => 'bg-sage-50 text-sage-700',
                            'izin'  => 'bg-ember-50 text-ember-700',
                            'sakit' => 'bg-blue-50 text-blue-700',
                            'alpha' => 'bg-red-50 text-red-700',
                        ];
                        $labelMap = ['hadir' => 'Hadir', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alpha' => 'Alpha'];
                    @endphp
                    <div class="flex items-center gap-3 px-5 py-3">
                        <div class="w-7 h-7 rounded-lg bg-ink-500 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                            {{ $p->nomor }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-slate-700 truncate">{{ $p->topik }}</p>
                            @if($p->tanggal)
                            <p class="text-xs text-slate-400">{{ $p->tanggal->isoFormat('D MMM YYYY') }}</p>
                            @endif
                        </div>
                        @if(!$sesi)
                        <span class="text-xs text-slate-400 px-2 py-1 rounded-lg bg-slate-50">Belum ada sesi</span>
                        @elseif($status)
                        <span class="text-xs font-medium px-2.5 py-1 rounded-lg {{ $badgeMap[$status] }}">{{ $labelMap[$status] }}</span>
                        @if($pres?->waktu_masuk)
                        <span class="text-xs text-slate-400">{{ $pres->waktu_masuk->format('H:i') }}</span>
                        @endif
                        @else
                        <span class="text-xs font-medium px-2.5 py-1 rounded-lg bg-red-50 text-red-700">Alpha</span>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
