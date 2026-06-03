<x-app-layout>
    <x-slot name="title">Nilai — {{ $kelas->mataKuliah->nama }}</x-slot>
    <x-slot name="pageTitle">Nilai</x-slot>

    <div class="mb-6">
        <nav class="flex items-center gap-1.5 text-xs text-slate-400 mb-1">
            <a href="{{ route('mahasiswa.kelas.index') }}" class="hover:text-ink-500">Mata Kuliah Saya</a>
            <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
            <span class="text-slate-600">{{ $kelas->mataKuliah->nama }}</span>
            <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
            <span class="text-ink-500 font-medium">Nilai</span>
        </nav>
        <h2 class="font-heading font-semibold text-slate-800 text-lg">
            {{ $kelas->mataKuliah->nama }}
            <span class="text-slate-400 font-normal text-sm">— Kelas {{ $kelas->nama_kelas }}</span>
        </h2>
    </div>

    @if(!$penilaian)
    <div class="bg-white rounded-2xl border border-slate-200 p-16 text-center">
        <i class="fa-solid fa-chart-bar text-slate-200 text-4xl mb-3"></i>
        <p class="text-slate-400 text-sm">Nilai belum diinput oleh dosen.</p>
    </div>
    @else
    <div class="max-w-xl space-y-4">
        {{-- Grade utama --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-6 text-center">
            @php
                $grade = $penilaian->grade ?? '—';
                $na = $penilaian->nilai_akhir;
                $gradeColor = match(substr($grade,0,1)) {
                    'A' => 'text-sage-600 bg-sage-50',
                    'B' => 'text-blue-600 bg-blue-50',
                    'C' => 'text-amber-600 bg-amber-50',
                    default => 'text-red-600 bg-red-50'
                };
            @endphp
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl {{ $gradeColor }} mb-3">
                <span class="text-4xl font-heading font-black">{{ $grade }}</span>
            </div>
            <p class="text-2xl font-heading font-bold text-slate-800">{{ $na !== null ? number_format($na, 1) : '—' }}</p>
            <p class="text-sm text-slate-400 mt-0.5">Nilai Akhir</p>
        </div>

        {{-- Komponen nilai --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <h3 class="font-heading font-semibold text-slate-800 text-sm mb-4">Rincian Komponen Nilai</h3>
            <div class="space-y-3">
                @foreach([
                    'kehadiran' => ['Kehadiran', $config?->bobot_kehadiran ?? 0, 'bg-sage-500'],
                    'tugas'     => ['Tugas', $config?->bobot_tugas ?? 0, 'bg-blue-500'],
                    'quiz'      => ['Quiz', $config?->bobot_quiz ?? 0, 'bg-purple-500'],
                    'uts'       => ['UTS', $config?->bobot_uts ?? 0, 'bg-ember-500'],
                    'uas'       => ['UAS', $config?->bobot_uas ?? 0, 'bg-red-500'],
                ] as $key => [$label, $bobot, $color])
                @php $nilai = $penilaian->{"nilai_$key"}; @endphp
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-sm text-slate-600">{{ $label }} <span class="text-xs text-slate-400">({{ $bobot }}%)</span></span>
                        <span class="text-sm font-semibold text-slate-800">{{ $nilai !== null ? number_format($nilai, 1) : '—' }}</span>
                    </div>
                    <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                        @if($nilai !== null)
                        <div class="h-2 {{ $color }} rounded-full transition-all" style="width:{{ min(100,$nilai) }}%"></div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</x-app-layout>
