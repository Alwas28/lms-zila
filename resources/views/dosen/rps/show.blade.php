<x-app-layout>
    <x-slot name="title">RPS — {{ $kelas->mataKuliah->nama }}</x-slot>
    <x-slot name="pageTitle">Rencana Pembelajaran Semester</x-slot>

    @include('admin._flash')

    {{-- Breadcrumb + Header --}}
    <div class="mb-6">
        <nav class="flex items-center gap-1.5 text-xs text-slate-400 mb-1">
            <a href="{{ route('dosen.mata-kuliah.index') }}" class="hover:text-ink-500 transition-colors">Mata Kuliah Saya</a>
            <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
            <span class="text-slate-600">{{ $kelas->mataKuliah->nama }}</span>
            <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
            <span class="text-ink-500 font-medium">RPS</span>
        </nav>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="font-heading font-semibold text-slate-800 text-lg">
                    {{ $kelas->mataKuliah->nama }}
                    <span class="text-slate-400 font-normal text-sm">— Kelas {{ $kelas->nama_kelas }}</span>
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">
                    Semester {{ ucfirst($kelas->semester->tipe) }} — {{ $kelas->semester->tahunAkademik->nama }}
                </p>
            </div>
            <div class="flex gap-2">
                @if($rps->pdf_path)
                <a href="{{ asset('storage/' . $rps->pdf_path) }}" target="_blank"
                    class="inline-flex items-center gap-2 bg-red-50 hover:bg-red-100 text-red-600 text-sm font-medium px-4 py-2.5 rounded-xl transition-colors">
                    <i class="fa-solid fa-file-pdf text-xs"></i> Unduh PDF
                </a>
                @endif
            </div>
        </div>
    </div>

    {{-- RPS Form --}}
    <form action="{{ route('dosen.rps.update', $kelas) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PATCH')

        <div class="grid grid-cols-1 gap-5">

            {{-- Deskripsi MK --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-5">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-7 h-7 rounded-lg bg-ink-50 flex items-center justify-center">
                        <i class="fa-solid fa-book text-ink-500 text-xs"></i>
                    </div>
                    <h3 class="font-heading font-semibold text-slate-800 text-sm">Deskripsi Mata Kuliah</h3>
                </div>
                <textarea name="deskripsi_mk" rows="4" placeholder="Masukkan deskripsi mata kuliah..."
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200 resize-none">{{ old('deskripsi_mk', $rps->deskripsi_mk) }}</textarea>
            </div>

            {{-- CPL & CPMK --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="bg-white rounded-2xl border border-slate-200 p-5">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-7 h-7 rounded-lg bg-sage-50 flex items-center justify-center">
                            <i class="fa-solid fa-bullseye text-sage-500 text-xs"></i>
                        </div>
                        <h3 class="font-heading font-semibold text-slate-800 text-sm">CPL (Capaian Pembelajaran Lulusan)</h3>
                    </div>
                    <textarea name="cpl" rows="5" placeholder="Masukkan CPL..."
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200 resize-none">{{ old('cpl', $rps->cpl) }}</textarea>
                </div>
                <div class="bg-white rounded-2xl border border-slate-200 p-5">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-7 h-7 rounded-lg bg-ink-50 flex items-center justify-center">
                            <i class="fa-solid fa-flag text-ink-500 text-xs"></i>
                        </div>
                        <h3 class="font-heading font-semibold text-slate-800 text-sm">CPMK (Capaian Pembelajaran Mata Kuliah)</h3>
                    </div>
                    <textarea name="cpmk" rows="5" placeholder="Masukkan CPMK..."
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200 resize-none">{{ old('cpmk', $rps->cpmk) }}</textarea>
                </div>
            </div>

            {{-- Sub-CPMK --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-5">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-7 h-7 rounded-lg bg-ember-50 flex items-center justify-center">
                        <i class="fa-solid fa-list-check text-ember-500 text-xs"></i>
                    </div>
                    <h3 class="font-heading font-semibold text-slate-800 text-sm">Sub-CPMK</h3>
                </div>
                <textarea name="sub_cpmk" rows="5" placeholder="Masukkan Sub-CPMK..."
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200 resize-none">{{ old('sub_cpmk', $rps->sub_cpmk) }}</textarea>
            </div>

            {{-- Metode Pembelajaran --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-5">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center">
                        <i class="fa-solid fa-chalkboard-user text-blue-500 text-xs"></i>
                    </div>
                    <h3 class="font-heading font-semibold text-slate-800 text-sm">Metode Pembelajaran</h3>
                </div>
                <textarea name="metode_pembelajaran" rows="4" placeholder="Ceramah, Diskusi, Praktikum, dll..."
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200 resize-none">{{ old('metode_pembelajaran', $rps->metode_pembelajaran) }}</textarea>
            </div>

            {{-- Referensi --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-5">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-7 h-7 rounded-lg bg-purple-50 flex items-center justify-center">
                        <i class="fa-solid fa-book-bookmark text-purple-500 text-xs"></i>
                    </div>
                    <h3 class="font-heading font-semibold text-slate-800 text-sm">Referensi / Daftar Pustaka</h3>
                </div>
                <textarea name="referensi" rows="5" placeholder="Daftar referensi / buku teks..."
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200 resize-none">{{ old('referensi', $rps->referensi) }}</textarea>
            </div>

            {{-- Upload PDF --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-5">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-7 h-7 rounded-lg bg-red-50 flex items-center justify-center">
                        <i class="fa-solid fa-file-pdf text-red-500 text-xs"></i>
                    </div>
                    <h3 class="font-heading font-semibold text-slate-800 text-sm">Upload RPS (PDF)</h3>
                </div>
                @if($rps->pdf_path)
                <div class="flex items-center gap-3 mb-3 p-3 bg-slate-50 rounded-xl border border-slate-200">
                    <i class="fa-solid fa-file-pdf text-red-500"></i>
                    <span class="text-sm text-slate-600 flex-1">File PDF tersedia</span>
                    <a href="{{ asset('storage/' . $rps->pdf_path) }}" target="_blank"
                        class="text-xs text-ink-500 hover:text-ink-700 font-medium">Lihat File</a>
                </div>
                @endif
                <input type="file" name="pdf" accept=".pdf"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200 bg-slate-50">
                <p class="text-xs text-slate-400 mt-1.5">Format: PDF. Ukuran maks: 10MB. Kosongkan jika tidak ingin mengubah.</p>
            </div>

            {{-- Submit --}}
            <div class="flex justify-end gap-3">
                <a href="{{ route('dosen.mata-kuliah.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-ink-500 hover:bg-ink-600 text-white text-sm font-medium transition-colors">
                    <i class="fa-solid fa-floppy-disk mr-1.5 text-xs"></i> Simpan RPS
                </button>
            </div>
        </div>
    </form>

</x-app-layout>
