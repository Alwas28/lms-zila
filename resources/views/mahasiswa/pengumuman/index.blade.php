<x-app-layout>
    <x-slot name="title">Pengumuman — {{ $kelas->mataKuliah->nama }}</x-slot>
    <x-slot name="pageTitle">Pengumuman</x-slot>

    <div class="mb-6">
        <nav class="flex items-center gap-1.5 text-xs text-slate-400 mb-1">
            <a href="{{ route('mahasiswa.kelas.index') }}" class="hover:text-ink-500">Mata Kuliah Saya</a>
            <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
            <span class="text-slate-600">{{ $kelas->mataKuliah->nama }}</span>
            <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
            <span class="text-ink-500 font-medium">Pengumuman</span>
        </nav>
        <h2 class="font-heading font-semibold text-slate-800 text-lg">
            {{ $kelas->mataKuliah->nama }}
            <span class="text-slate-400 font-normal text-sm">— Kelas {{ $kelas->nama_kelas }}</span>
        </h2>
        <p class="text-xs text-slate-400 mt-0.5">Semester {{ ucfirst($kelas->semester->tipe) }} — {{ $kelas->semester->tahunAkademik->nama }}</p>
    </div>

    @if($pengumuman->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 p-16 text-center">
        <div class="w-16 h-16 rounded-2xl bg-ember-50 flex items-center justify-center mx-auto mb-4">
            <i class="fa-solid fa-bullhorn text-ember-300 text-2xl"></i>
        </div>
        <p class="font-heading font-semibold text-slate-600 mb-1">Belum ada pengumuman</p>
        <p class="text-slate-400 text-sm">Belum ada pengumuman dari dosen untuk kelas ini.</p>
    </div>
    @else
    <div class="space-y-3">
        @foreach($pengumuman as $p)
        <div class="bg-white rounded-2xl border {{ $p->is_pinned ? 'border-ember-200 bg-ember-50/30' : 'border-slate-200' }} overflow-hidden">
            <div class="px-5 py-4">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-ember-500 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fa-solid fa-bullhorn text-white text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 flex-wrap mb-1">
                            @if($p->is_pinned)
                            <span class="text-xs bg-ember-100 text-ember-700 px-2 py-0.5 rounded-full font-medium">
                                <i class="fa-solid fa-thumbtack text-xs mr-0.5"></i>Disematkan
                            </span>
                            @endif
                            <h3 class="font-heading font-semibold text-slate-800">{{ $p->judul }}</h3>
                        </div>
                        <p class="text-xs text-slate-400 mb-3">
                            <i class="fa-regular fa-clock mr-1"></i>{{ $p->created_at->isoFormat('D MMM YYYY, HH:mm') }}
                            &middot; {{ $p->penulis->name }}
                        </p>
                        <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">{{ $p->isi }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</x-app-layout>
