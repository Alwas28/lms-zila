<x-app-layout>
    <x-slot name="title">RPS — {{ $kelas->mataKuliah->nama }}</x-slot>
    <x-slot name="pageTitle">Rencana Pembelajaran Semester</x-slot>

    <div class="mb-6">
        <nav class="flex items-center gap-1.5 text-xs text-slate-400 mb-1">
            <a href="{{ route('mahasiswa.kelas.index') }}" class="hover:text-ink-500">Mata Kuliah Saya</a>
            <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
            <span class="text-slate-600">{{ $kelas->mataKuliah->nama }}</span>
            <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
            <span class="text-ink-500 font-medium">RPS</span>
        </nav>
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-heading font-semibold text-slate-800 text-lg">{{ $kelas->mataKuliah->nama }}</h2>
                <p class="text-xs text-slate-400 mt-0.5">Semester {{ ucfirst($kelas->semester->tipe) }} — {{ $kelas->semester->tahunAkademik->nama }}</p>
            </div>
            @if($rps?->pdf_path)
            <a href="{{ asset('storage/'.$rps->pdf_path) }}" target="_blank"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 text-sm font-medium transition-colors">
                <i class="fa-solid fa-file-pdf text-xs"></i>Unduh PDF
            </a>
            @endif
        </div>
    </div>

    @if(!$rps || (!$rps->deskripsi_mk && !$rps->cpl && !$rps->cpmk))
    <div class="bg-white rounded-2xl border border-slate-200 p-16 text-center">
        <i class="fa-solid fa-file-lines text-slate-200 text-4xl mb-3"></i>
        <p class="text-slate-400 text-sm">RPS belum diisi oleh dosen.</p>
    </div>
    @else
    <div class="grid grid-cols-1 gap-4">
        @if($rps->deskripsi_mk)
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-7 h-7 rounded-lg bg-ink-50 flex items-center justify-center"><i class="fa-solid fa-book text-ink-500 text-xs"></i></div>
                <h3 class="font-heading font-semibold text-slate-800 text-sm">Deskripsi Mata Kuliah</h3>
            </div>
            <p class="text-sm text-slate-600 leading-relaxed whitespace-pre-line">{{ $rps->deskripsi_mk }}</p>
        </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @if($rps->cpl)
            <div class="bg-white rounded-2xl border border-slate-200 p-5">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-7 h-7 rounded-lg bg-sage-50 flex items-center justify-center"><i class="fa-solid fa-bullseye text-sage-500 text-xs"></i></div>
                    <h3 class="font-heading font-semibold text-slate-800 text-sm">CPL</h3>
                </div>
                <p class="text-sm text-slate-600 leading-relaxed whitespace-pre-line">{{ $rps->cpl }}</p>
            </div>
            @endif
            @if($rps->cpmk)
            <div class="bg-white rounded-2xl border border-slate-200 p-5">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-7 h-7 rounded-lg bg-ink-50 flex items-center justify-center"><i class="fa-solid fa-flag text-ink-500 text-xs"></i></div>
                    <h3 class="font-heading font-semibold text-slate-800 text-sm">CPMK</h3>
                </div>
                <p class="text-sm text-slate-600 leading-relaxed whitespace-pre-line">{{ $rps->cpmk }}</p>
            </div>
            @endif
        </div>

        @if($rps->metode_pembelajaran)
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center"><i class="fa-solid fa-chalkboard-user text-blue-500 text-xs"></i></div>
                <h3 class="font-heading font-semibold text-slate-800 text-sm">Metode Pembelajaran</h3>
            </div>
            <p class="text-sm text-slate-600 leading-relaxed whitespace-pre-line">{{ $rps->metode_pembelajaran }}</p>
        </div>
        @endif

        @if($rps->referensi)
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-7 h-7 rounded-lg bg-purple-50 flex items-center justify-center"><i class="fa-solid fa-book-bookmark text-purple-500 text-xs"></i></div>
                <h3 class="font-heading font-semibold text-slate-800 text-sm">Referensi</h3>
            </div>
            <p class="text-sm text-slate-600 leading-relaxed whitespace-pre-line">{{ $rps->referensi }}</p>
        </div>
        @endif
    </div>
    @endif
</x-app-layout>
