<x-app-layout>
    <x-slot name="title">Ujian — {{ $kelas->mataKuliah->nama }}</x-slot>
    <x-slot name="pageTitle">Ujian</x-slot>

    <div class="mb-6">
        <nav class="flex items-center gap-1.5 text-xs text-slate-400 mb-1">
            <a href="{{ route('mahasiswa.kelas.index') }}" class="hover:text-ink-500">Mata Kuliah Saya</a>
            <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
            <span class="text-slate-600">{{ $kelas->mataKuliah->nama }}</span>
            <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
            <span class="text-ink-500 font-medium">Ujian</span>
        </nav>
        <h2 class="font-heading font-semibold text-slate-800 text-lg">
            {{ $kelas->mataKuliah->nama }}
            <span class="text-slate-400 font-normal text-sm">— Kelas {{ $kelas->nama_kelas }}</span>
        </h2>
    </div>

    @if($ujian->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 p-16 text-center">
        <i class="fa-solid fa-clipboard-question text-slate-200 text-4xl mb-3"></i>
        <p class="text-slate-400 text-sm">Belum ada ujian tersedia.</p>
    </div>
    @else
    <div class="space-y-3">
        @foreach($ujian as $u)
        @php
            $sesi  = $sesiMap[$u->id] ?? null;
            $selesai = $sesi && $sesi->is_selesai;
            $aktif   = $u->status === 'aktif';
            $now     = now();
            $bisa    = $aktif && (!$u->mulai_at || $now->gte($u->mulai_at)) && (!$u->selesai_at || $now->lte($u->selesai_at));
            $typeColor = match($u->tipe) { 'uts' => 'bg-ember-500', 'uas' => 'bg-red-500', default => 'bg-purple-500' };
        @endphp
        <div class="bg-white rounded-2xl border border-slate-200 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl {{ $typeColor }} flex items-center justify-center text-white font-heading font-bold text-sm flex-shrink-0">
                {{ strtoupper($u->tipe) }}
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="font-heading font-semibold text-slate-800 mb-0.5">{{ $u->judul }}</h3>
                <div class="flex items-center gap-3 text-xs text-slate-400 flex-wrap">
                    <span><i class="fa-regular fa-clock mr-1"></i>{{ $u->durasi }} menit</span>
                    <span>·</span>
                    <span>{{ $u->jumlah_soal }} soal</span>
                    @if($u->mulai_at)
                    <span>·</span>
                    <span>{{ $u->mulai_at->isoFormat('D MMM, HH:mm') }} – {{ $u->selesai_at?->isoFormat('HH:mm') }}</span>
                    @endif
                    @if($selesai)
                    <span>·</span>
                    <span class="text-sage-600 font-semibold">Nilai: {{ number_format($sesi->nilai, 1) }}</span>
                    @endif
                </div>
            </div>
            <div class="flex-shrink-0">
                @if($selesai)
                <a href="{{ route('mahasiswa.ujian.hasil', [$kelas, $u]) }}"
                    class="px-4 py-2 rounded-xl bg-sage-50 text-sage-700 text-xs font-medium hover:bg-sage-100 transition-colors">
                    <i class="fa-solid fa-chart-bar mr-1"></i>Lihat Hasil
                </a>
                @elseif($bisa)
                <a href="{{ route('mahasiswa.ujian.show', [$kelas, $u]) }}"
                    class="px-4 py-2 rounded-xl bg-ink-500 text-white text-xs font-medium hover:bg-ink-600 transition-colors">
                    <i class="fa-solid fa-play mr-1"></i>Mulai
                </a>
                @elseif($u->status === 'selesai')
                <span class="text-xs text-slate-400 bg-slate-100 px-3 py-1.5 rounded-xl">Sudah Berakhir</span>
                @else
                <span class="text-xs text-slate-400 bg-slate-100 px-3 py-1.5 rounded-xl">Belum Dibuka</span>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif
</x-app-layout>
