<x-app-layout>
    <x-slot name="title">{{ $ujian->judul }}</x-slot>
    <x-slot name="pageTitle">Ujian</x-slot>

    @include('admin._flash')

    <div class="mb-6">
        <nav class="flex items-center gap-1.5 text-xs text-slate-400 mb-1">
            <a href="{{ route('mahasiswa.kelas.index') }}" class="hover:text-ink-500">Mata Kuliah Saya</a>
            <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
            <span class="text-slate-600">{{ $kelas->mataKuliah->nama }}</span>
            <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
            <a href="{{ route('mahasiswa.ujian.index', $kelas) }}" class="hover:text-ink-500">Ujian</a>
            <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
            <span class="text-ink-500 font-medium">{{ $ujian->judul }}</span>
        </nav>
    </div>

    <div class="max-w-lg mx-auto">
        <div class="bg-white rounded-2xl border border-slate-200 p-6 text-center">
            @php
                $typeColor = match($ujian->tipe) { 'uts' => 'bg-ember-500', 'uas' => 'bg-red-500', default => 'bg-purple-500' };
            @endphp
            <div class="w-16 h-16 rounded-2xl {{ $typeColor }} flex items-center justify-center text-white font-heading font-bold text-xl mx-auto mb-4">
                {{ strtoupper($ujian->tipe) }}
            </div>
            <h2 class="font-heading font-semibold text-slate-800 text-xl mb-1">{{ $ujian->judul }}</h2>
            @if($ujian->deskripsi)
            <p class="text-sm text-slate-500 mb-5">{{ $ujian->deskripsi }}</p>
            @endif

            <div class="grid grid-cols-3 gap-3 mb-6">
                <div class="bg-slate-50 rounded-xl p-3">
                    <p class="text-2xl font-heading font-bold text-slate-800">{{ $ujian->durasi }}</p>
                    <p class="text-xs text-slate-400">Menit</p>
                </div>
                <div class="bg-slate-50 rounded-xl p-3">
                    <p class="text-2xl font-heading font-bold text-slate-800">{{ $ujian->jumlah_soal }}</p>
                    <p class="text-xs text-slate-400">Soal</p>
                </div>
                <div class="bg-slate-50 rounded-xl p-3">
                    <p class="text-2xl font-heading font-bold text-slate-800">{{ $ujian->batas_percobaan }}</p>
                    <p class="text-xs text-slate-400">Percobaan</p>
                </div>
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 text-left mb-5 text-xs text-amber-700 space-y-1">
                <p class="font-semibold flex items-center gap-1.5"><i class="fa-solid fa-triangle-exclamation"></i>Perhatian sebelum mulai:</p>
                <p>• Pastikan koneksi internet stabil selama ujian berlangsung.</p>
                <p>• Ujian akan otomatis disubmit ketika waktu habis.</p>
                <p>• Jangan tutup atau refresh halaman selama mengerjakan.</p>
            </div>

            @if($sesi && !$sesi->is_selesai)
            <a href="{{ route('mahasiswa.ujian.kerjakan', [$kelas, $ujian]) }}"
                class="block w-full py-3 rounded-xl bg-ink-500 hover:bg-ink-600 text-white font-medium text-sm transition-colors">
                <i class="fa-solid fa-play mr-1.5"></i>Lanjutkan Mengerjakan
            </a>
            @else
            <form action="{{ route('mahasiswa.ujian.mulai', [$kelas, $ujian]) }}" method="POST">
                @csrf
                <button type="submit"
                    class="w-full py-3 rounded-xl bg-ink-500 hover:bg-ink-600 text-white font-medium text-sm transition-colors">
                    <i class="fa-solid fa-play mr-1.5"></i>Mulai Ujian
                </button>
            </form>
            @endif
        </div>
    </div>
</x-app-layout>
