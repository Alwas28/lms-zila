<x-app-layout>
    <x-slot name="title">Hasil Ujian — {{ $ujian->judul }}</x-slot>
    <x-slot name="pageTitle">Hasil Ujian</x-slot>

    <div class="mb-6">
        <nav class="flex items-center gap-1.5 text-xs text-slate-400 mb-1 flex-wrap">
            <a href="{{ route('mahasiswa.kelas.index') }}" class="hover:text-ink-500">Mata Kuliah Saya</a>
            <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
            <span class="text-slate-600">{{ $kelas->mataKuliah->nama }}</span>
            <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
            <a href="{{ route('mahasiswa.ujian.index', $kelas) }}" class="hover:text-ink-500">Ujian</a>
            <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
            <span class="text-ink-500 font-medium">Hasil</span>
        </nav>
    </div>

    <div class="max-w-2xl space-y-5">
        {{-- Skor --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-6 text-center">
            @php
                $nilai  = $sesi->nilai ?? 0;
                $lulus  = $nilai >= 60;
                $benar  = $jawaban->where('is_benar', true)->count();
                $total  = $soalList->count();
            @endphp
            <div class="inline-flex items-center justify-center w-24 h-24 rounded-2xl {{ $lulus ? 'bg-sage-50' : 'bg-red-50' }} mb-3">
                <span class="text-4xl font-heading font-black {{ $lulus ? 'text-sage-600' : 'text-red-600' }}">{{ number_format($nilai, 0) }}</span>
            </div>
            <p class="text-lg font-heading font-semibold {{ $lulus ? 'text-sage-700' : 'text-red-600' }} mb-1">
                {{ $lulus ? 'Lulus' : 'Tidak Lulus' }}
            </p>
            <p class="text-sm text-slate-400">{{ $benar }} benar dari {{ $total }} soal pilihan ganda</p>
            <div class="flex items-center justify-center gap-4 mt-3 text-xs text-slate-500">
                <span>Mulai: {{ $sesi->mulai_at->isoFormat('HH:mm') }}</span>
                <span>·</span>
                <span>Selesai: {{ $sesi->selesai_at?->isoFormat('HH:mm') }}</span>
                <span>·</span>
                <span>Percobaan ke-{{ $sesi->percobaan_ke }}</span>
            </div>
        </div>

        {{-- Detail jawaban --}}
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
            <div class="px-5 py-3 bg-slate-50 border-b border-slate-100">
                <p class="font-heading font-semibold text-slate-800 text-sm">Pembahasan Jawaban</p>
            </div>
            <div class="divide-y divide-slate-50">
                @foreach($soalList as $idx => $ujianSoal)
                @php
                    $soal = $ujianSoal->bankSoal;
                    $jw   = $jawaban[$soal->id] ?? null;
                    $benarSoal = $jw?->is_benar;
                @endphp
                <div class="px-5 py-4">
                    <div class="flex items-start gap-3 mb-3">
                        <span class="w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5
                            @if($benarSoal === null) bg-slate-100 text-slate-500
                            @elseif($benarSoal) bg-sage-100 text-sage-700
                            @else bg-red-100 text-red-700 @endif">
                            @if($benarSoal === null) {{ $idx+1 }}
                            @elseif($benarSoal) <i class="fa-solid fa-check text-xs"></i>
                            @else <i class="fa-solid fa-xmark text-xs"></i>
                            @endif
                        </span>
                        <p class="text-sm text-slate-800 flex-1">{{ $soal->pertanyaan }}</p>
                    </div>
                    @if($soal->tipe === 'pilihan_ganda')
                    <div class="ml-9 space-y-1.5">
                        @foreach($soal->pilihan as $p)
                        <div class="flex items-center gap-2 text-xs px-3 py-1.5 rounded-lg
                            @if($p->is_benar) bg-sage-50 border border-sage-200 text-sage-700 font-medium
                            @elseif($jw && $jw->pilihan_soal_id == $p->id && !$p->is_benar) bg-red-50 border border-red-200 text-red-700
                            @else bg-slate-50 text-slate-600 @endif">
                            @if($p->is_benar)<i class="fa-solid fa-circle-check text-sage-500 flex-shrink-0"></i>
                            @elseif($jw && $jw->pilihan_soal_id == $p->id)<i class="fa-solid fa-circle-xmark text-red-400 flex-shrink-0"></i>
                            @else<i class="fa-regular fa-circle text-slate-300 flex-shrink-0"></i>@endif
                            {{ $p->teks }}
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="ml-9 text-xs text-slate-500 bg-slate-50 rounded-xl px-3 py-2">
                        <p class="font-medium mb-0.5">Jawaban Anda:</p>
                        <p>{{ $jw?->jawaban_essay ?? '(tidak dijawab)' }}</p>
                        <p class="text-xs text-amber-600 mt-1"><i class="fa-solid fa-info-circle mr-1"></i>Penilaian essay dilakukan manual oleh dosen.</p>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        <div class="flex justify-center">
            <a href="{{ route('mahasiswa.ujian.index', $kelas) }}"
                class="px-5 py-2.5 rounded-xl bg-ink-500 hover:bg-ink-600 text-white text-sm font-medium transition-colors">
                <i class="fa-solid fa-arrow-left mr-1.5 text-xs"></i>Kembali ke Daftar Ujian
            </a>
        </div>
    </div>
</x-app-layout>
