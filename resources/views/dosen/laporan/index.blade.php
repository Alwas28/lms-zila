<x-app-layout>
    <x-slot name="title">Laporan — {{ $kelas->mataKuliah->nama }}</x-slot>
    <x-slot name="pageTitle">Laporan</x-slot>

    @include('admin._flash')

    {{-- Breadcrumb + Header --}}
    <div class="mb-6">
        <nav class="flex items-center gap-1.5 text-xs text-slate-400 mb-1">
            <a href="{{ route('dosen.mata-kuliah.index') }}" class="hover:text-ink-500 transition-colors">Mata Kuliah Saya</a>
            <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
            <span class="text-slate-600">{{ $kelas->mataKuliah->nama }}</span>
            <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
            <span class="text-ink-500 font-medium">Laporan</span>
        </nav>
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3">
            <div>
                <h2 class="font-heading font-semibold text-slate-800 text-lg">
                    {{ $kelas->mataKuliah->nama }}
                    <span class="text-slate-400 font-normal text-sm">— Kelas {{ $kelas->nama_kelas }}</span>
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">
                    {{ $kelas->mataKuliah->kode }} &middot; {{ $kelas->mataKuliah->sks }} SKS &middot;
                    Semester {{ ucfirst($kelas->semester->tipe) }} {{ $kelas->semester->tahunAkademik->nama }}
                </p>
            </div>
        </div>
    </div>

    {{-- Ringkasan --}}
    <div class="grid grid-cols-3 gap-3 mb-6">
        <div class="bg-white rounded-2xl border border-slate-200 p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-ink-50 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-user-graduate text-ink-500"></i>
            </div>
            <div>
                <p class="text-xl font-heading font-bold text-slate-800">{{ $jumlahMahasiswa }}</p>
                <p class="text-xs text-slate-400">Mahasiswa</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-sage-50 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-calendar-days text-sage-500"></i>
            </div>
            <div>
                <p class="text-xl font-heading font-bold text-slate-800">{{ $jumlahPertemuan }}</p>
                <p class="text-xs text-slate-400">Pertemuan</p>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-ember-50 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-flag text-ember-500"></i>
            </div>
            <div>
                <p class="text-xl font-heading font-bold text-slate-800">{{ count($cpmkLines) }}</p>
                <p class="text-xs text-slate-400">Item CPMK</p>
            </div>
        </div>
    </div>

    {{-- Kartu Laporan --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

        {{-- Laporan Nilai --}}
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden flex flex-col">
            <div class="p-5 border-b border-slate-100">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-11 h-11 rounded-xl bg-ink-500 flex items-center justify-center shadow-sm flex-shrink-0">
                        <i class="fa-solid fa-star-half-stroke text-white"></i>
                    </div>
                    <div>
                        <h3 class="font-heading font-semibold text-slate-800">Laporan Nilai</h3>
                        <p class="text-xs text-slate-400">Rekap nilai akhir mahasiswa</p>
                    </div>
                </div>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Mencakup nilai kehadiran, tugas, quiz, UTS, UAS, nilai akhir, dan grade untuk
                    <strong>{{ $jumlahMahasiswa }}</strong> mahasiswa.
                </p>
            </div>
            <div class="p-4 mt-auto">
                <div class="flex gap-2">
                    <a href="{{ route('dosen.laporan.nilai-pdf', $kelas) }}" target="_blank"
                        class="flex-1 flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 text-xs font-medium transition-colors">
                        <i class="fa-solid fa-file-pdf"></i> PDF
                    </a>
                    <a href="{{ route('dosen.laporan.nilai-csv', $kelas) }}"
                        class="flex-1 flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl bg-sage-50 hover:bg-sage-100 text-sage-700 text-xs font-medium transition-colors">
                        <i class="fa-solid fa-file-csv"></i> Excel / CSV
                    </a>
                </div>
            </div>
        </div>

        {{-- Laporan Presensi --}}
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden flex flex-col">
            <div class="p-5 border-b border-slate-100">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-11 h-11 rounded-xl bg-sage-500 flex items-center justify-center shadow-sm flex-shrink-0">
                        <i class="fa-solid fa-user-check text-white"></i>
                    </div>
                    <div>
                        <h3 class="font-heading font-semibold text-slate-800">Laporan Presensi</h3>
                        <p class="text-xs text-slate-400">Rekap kehadiran per pertemuan</p>
                    </div>
                </div>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Matriks kehadiran per mahasiswa per pertemuan, lengkap dengan rekap
                    hadir, izin, sakit, dan alpha untuk <strong>{{ $jumlahPertemuan }}</strong> pertemuan.
                </p>
            </div>
            <div class="p-4 mt-auto">
                <div class="flex gap-2">
                    <a href="{{ route('dosen.laporan.presensi-pdf', $kelas) }}" target="_blank"
                        class="flex-1 flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 text-xs font-medium transition-colors">
                        <i class="fa-solid fa-file-pdf"></i> PDF
                    </a>
                    <a href="{{ route('dosen.laporan.presensi-csv', $kelas) }}"
                        class="flex-1 flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl bg-sage-50 hover:bg-sage-100 text-sage-700 text-xs font-medium transition-colors">
                        <i class="fa-solid fa-file-csv"></i> Excel / CSV
                    </a>
                </div>
            </div>
        </div>

        {{-- Laporan Capaian CPMK --}}
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden flex flex-col">
            <div class="p-5 border-b border-slate-100">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-11 h-11 rounded-xl bg-ember-500 flex items-center justify-center shadow-sm flex-shrink-0">
                        <i class="fa-solid fa-chart-line text-white"></i>
                    </div>
                    <div>
                        <h3 class="font-heading font-semibold text-slate-800">Laporan Capaian CPMK</h3>
                        <p class="text-xs text-slate-400">Ketercapaian per CPMK</p>
                    </div>
                </div>
                @if(empty($cpmkLines))
                <div class="text-xs text-amber-600 bg-amber-50 rounded-xl px-3 py-2">
                    <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                    CPMK belum diisi di RPS.
                    <a href="{{ route('dosen.rps.show', $kelas) }}" class="underline">Isi sekarang</a>
                </div>
                @else
                <p class="text-xs text-slate-500 leading-relaxed">
                    Ketercapaian setiap CPMK berdasarkan komponen penilaian yang telah di-mapping,
                    untuk <strong>{{ count($cpmkLines) }}</strong> CPMK.
                </p>
                @endif
            </div>
            <div class="p-4 mt-auto">
                <div class="flex gap-2">
                    <a href="{{ route('dosen.laporan.capaian-pdf', $kelas) }}" target="_blank"
                        class="flex-1 flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl text-xs font-medium transition-colors
                        {{ empty($cpmkLines) ? 'bg-slate-100 text-slate-400 pointer-events-none' : 'bg-red-50 hover:bg-red-100 text-red-600' }}">
                        <i class="fa-solid fa-file-pdf"></i> PDF
                    </a>
                    <a href="{{ route('dosen.laporan.capaian-csv', $kelas) }}"
                        class="flex-1 flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl text-xs font-medium transition-colors
                        {{ empty($cpmkLines) ? 'bg-slate-100 text-slate-400 pointer-events-none' : 'bg-sage-50 hover:bg-sage-100 text-sage-700' }}">
                        <i class="fa-solid fa-file-csv"></i> Excel / CSV
                    </a>
                </div>
            </div>
        </div>

    </div>

</x-app-layout>
