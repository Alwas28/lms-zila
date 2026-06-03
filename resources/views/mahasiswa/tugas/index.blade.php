<x-app-layout>
    <x-slot name="title">Tugas — {{ $kelas->mataKuliah->nama }}</x-slot>
    <x-slot name="pageTitle">Tugas</x-slot>

    @include('admin._flash')

    <div class="mb-6">
        <nav class="flex items-center gap-1.5 text-xs text-slate-400 mb-1">
            <a href="{{ route('mahasiswa.kelas.index') }}" class="hover:text-ink-500">Mata Kuliah Saya</a>
            <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
            <span class="text-slate-600">{{ $kelas->mataKuliah->nama }}</span>
            <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
            <span class="text-ink-500 font-medium">Tugas</span>
        </nav>
        <h2 class="font-heading font-semibold text-slate-800 text-lg">
            {{ $kelas->mataKuliah->nama }}
            <span class="text-slate-400 font-normal text-sm">— Kelas {{ $kelas->nama_kelas }}</span>
        </h2>
    </div>

    @if($tugas->isEmpty())
    <div class="bg-white rounded-2xl border border-slate-200 p-16 text-center">
        <i class="fa-solid fa-list-check text-slate-200 text-4xl mb-3"></i>
        <p class="text-slate-400 text-sm">Belum ada tugas diberikan.</p>
    </div>
    @else
    <div class="space-y-3">
        @foreach($tugas as $t)
        @php
            $submission = $t->submissions->first();
            $sudah = $submission !== null;
            $dinilai = $sudah && $submission->nilai !== null;
            $expired = $t->is_expired;
        @endphp
        <a href="{{ route('mahasiswa.tugas.show', [$kelas, $t]) }}"
            class="block bg-white rounded-2xl border border-slate-200 hover:border-ink-200 hover:shadow-sm transition-all p-5">
            <div class="flex items-start justify-between gap-3">
                <div class="flex-1">
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium
                            @if($t->tipe === 'individu') bg-blue-50 text-blue-600 @else bg-purple-50 text-purple-600 @endif">
                            {{ ucfirst($t->tipe) }}
                        </span>
                        @if($dinilai)
                        <span class="text-xs bg-sage-50 text-sage-700 px-2 py-0.5 rounded-full font-medium">
                            <i class="fa-solid fa-circle-check text-xs mr-0.5"></i>Dinilai
                        </span>
                        @elseif($sudah)
                        <span class="text-xs bg-ink-50 text-ink-600 px-2 py-0.5 rounded-full font-medium">
                            <i class="fa-solid fa-paper-plane text-xs mr-0.5"></i>Dikumpulkan
                        </span>
                        @elseif($expired)
                        <span class="text-xs bg-red-50 text-red-600 px-2 py-0.5 rounded-full font-medium">
                            <i class="fa-solid fa-clock text-xs mr-0.5"></i>Terlambat
                        </span>
                        @else
                        <span class="text-xs bg-ember-50 text-ember-600 px-2 py-0.5 rounded-full font-medium">
                            Belum dikumpulkan
                        </span>
                        @endif
                        <h3 class="font-heading font-semibold text-slate-800">{{ $t->judul }}</h3>
                    </div>
                    <p class="text-xs text-slate-500 line-clamp-2 mb-2">{{ $t->deskripsi }}</p>
                    <div class="flex items-center gap-3 text-xs text-slate-400">
                        <span><i class="fa-regular fa-clock mr-1"></i>Deadline: {{ $t->deadline?->isoFormat('D MMM YYYY, HH:mm') ?? 'Tidak ada batas waktu' }}</span>
                        <span>·</span>
                        <span>Nilai maks: {{ $t->nilai_maks }}</span>
                        @if($dinilai)
                        <span>·</span>
                        <span class="font-semibold text-sage-600">Nilai: {{ $submission->nilai }}/{{ $t->nilai_maks }}</span>
                        @endif
                    </div>
                </div>
                <i class="fa-solid fa-chevron-right text-slate-300 flex-shrink-0 mt-1"></i>
            </div>
        </a>
        @endforeach
    </div>
    @endif
</x-app-layout>
