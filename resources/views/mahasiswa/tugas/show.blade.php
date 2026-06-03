<x-app-layout>
    <x-slot name="title">{{ $tugas->judul }}</x-slot>
    <x-slot name="pageTitle">Tugas</x-slot>

    @include('admin._flash')

    <div class="mb-6">
        <nav class="flex items-center gap-1.5 text-xs text-slate-400 mb-1 flex-wrap">
            <a href="{{ route('mahasiswa.kelas.index') }}" class="hover:text-ink-500">Mata Kuliah Saya</a>
            <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
            <span class="text-slate-600">{{ $kelas->mataKuliah->nama }}</span>
            <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
            <a href="{{ route('mahasiswa.tugas.index', $kelas) }}" class="hover:text-ink-500">Tugas</a>
            <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
            <span class="text-ink-500 font-medium">{{ $tugas->judul }}</span>
        </nav>
    </div>

    <div class="max-w-2xl space-y-4">
        {{-- Detail Tugas --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <div class="flex items-center gap-2 mb-3">
                <span class="text-xs px-2 py-0.5 rounded-full font-medium
                    @if($tugas->tipe === 'individu') bg-blue-50 text-blue-600 @else bg-purple-50 text-purple-600 @endif">
                    {{ ucfirst($tugas->tipe) }}
                </span>
                <h2 class="font-heading font-semibold text-slate-800">{{ $tugas->judul }}</h2>
            </div>
            <p class="text-sm text-slate-600 leading-relaxed mb-4 whitespace-pre-line">{{ $tugas->deskripsi }}</p>
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-slate-50 rounded-xl p-3">
                    <p class="text-xs text-slate-400 mb-0.5">Deadline</p>
                    <p class="text-sm font-medium {{ $tugas->is_expired ? 'text-red-600' : 'text-slate-800' }}">
                        {{ $tugas->deadline?->isoFormat('D MMM YYYY, HH:mm') ?? '—' }}
                    </p>
                </div>
                <div class="bg-slate-50 rounded-xl p-3">
                    <p class="text-xs text-slate-400 mb-0.5">Nilai Maksimal</p>
                    <p class="text-sm font-medium text-slate-800">{{ $tugas->nilai_maks }}</p>
                </div>
            </div>
        </div>

        {{-- Status Pengumpulan --}}
        @if($submission)
        <div class="bg-white rounded-2xl border {{ $submission->nilai !== null ? 'border-sage-200 bg-sage-50/20' : 'border-ink-200 bg-ink-50/20' }} p-5">
            <div class="flex items-center gap-2 mb-3">
                <i class="fa-solid fa-circle-check {{ $submission->nilai !== null ? 'text-sage-500' : 'text-ink-500' }}"></i>
                <h3 class="font-heading font-semibold text-slate-800 text-sm">
                    {{ $submission->nilai !== null ? 'Tugas Dinilai' : 'Tugas Dikumpulkan' }}
                </h3>
            </div>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-slate-500">Waktu Pengumpulan</span>
                    <span class="text-slate-700 font-medium">{{ $submission->submitted_at?->isoFormat('D MMM YYYY, HH:mm') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">File</span>
                    <a href="{{ asset('storage/'.$submission->file_path) }}" target="_blank"
                        class="text-ink-500 hover:text-ink-700 font-medium flex items-center gap-1">
                        <i class="fa-solid fa-file text-xs"></i> Lihat file
                    </a>
                </div>
                @if($submission->catatan)
                <div>
                    <p class="text-slate-500 mb-1">Catatan:</p>
                    <p class="text-slate-700 bg-white rounded-xl px-3 py-2 border border-slate-100">{{ $submission->catatan }}</p>
                </div>
                @endif
                @if($submission->nilai !== null)
                <div class="flex justify-between items-center pt-2 border-t border-slate-100">
                    <span class="text-slate-500">Nilai</span>
                    <span class="text-xl font-heading font-bold text-sage-600">{{ $submission->nilai }} / {{ $tugas->nilai_maks }}</span>
                </div>
                @if($submission->feedback)
                <div>
                    <p class="text-slate-500 mb-1">Feedback Dosen:</p>
                    <p class="text-slate-700 bg-white rounded-xl px-3 py-2 border border-slate-100">{{ $submission->feedback }}</p>
                </div>
                @endif
                @endif
            </div>
            @if(!$tugas->is_expired && $submission->nilai === null)
            <form method="POST" action="{{ route('mahasiswa.tugas.cancel', [$kelas, $tugas]) }}"
                class="mt-4" onsubmit="return confirm('Batalkan pengumpulan ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium">
                    <i class="fa-solid fa-rotate-left mr-1"></i>Batalkan Pengumpulan
                </button>
            </form>
            @endif
        </div>
        @elseif(!$tugas->is_expired)
        {{-- Form Upload --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <h3 class="font-heading font-semibold text-slate-800 text-sm mb-4">
                <i class="fa-solid fa-upload mr-1.5 text-ink-500"></i>Kumpulkan Tugas
            </h3>
            <form action="{{ route('mahasiswa.tugas.submit', [$kelas, $tugas]) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">File Tugas</label>
                    <input type="file" name="file" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm bg-slate-50 focus:outline-none focus:ring-2 focus:ring-ink-200">
                    <p class="text-xs text-slate-400 mt-1">Maks 20MB. Format: PDF, DOC, ZIP, dll.</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Catatan (opsional)</label>
                    <textarea name="catatan" rows="3" placeholder="Tambahkan catatan untuk dosen..."
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200 resize-none"></textarea>
                </div>
                <button type="submit"
                    class="w-full py-2.5 rounded-xl bg-ink-500 hover:bg-ink-600 text-white text-sm font-medium transition-colors">
                    <i class="fa-solid fa-paper-plane mr-1.5 text-xs"></i>Kumpulkan Tugas
                </button>
            </form>
        </div>
        @else
        <div class="bg-red-50 border border-red-200 rounded-2xl p-5 flex items-center gap-3">
            <i class="fa-solid fa-clock text-red-400 text-xl"></i>
            <div>
                <p class="font-medium text-red-700 text-sm">Batas waktu sudah lewat</p>
                <p class="text-xs text-red-500 mt-0.5">Anda tidak dapat mengumpulkan tugas ini.</p>
            </div>
        </div>
        @endif
    </div>
</x-app-layout>
