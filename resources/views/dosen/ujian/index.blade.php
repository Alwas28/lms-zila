<x-app-layout>
    <x-slot name=”title”>Ujian &mdash; {{ $kelas->mataKuliah->nama }}</x-slot>
    <x-slot name="pageTitle">Manajemen Ujian</x-slot>

    @include('admin._flash')

    {{-- Breadcrumb + Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
        <div>
            <nav class="flex items-center gap-1.5 text-xs text-slate-400 mb-1">
                <a href="{{ route('dosen.mata-kuliah.index') }}" class="hover:text-ink-500 transition-colors">Mata Kuliah Saya</a>
                <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
                <span class="text-slate-600">{{ $kelas->mataKuliah->nama }}</span>
                <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
                <span class="text-ink-500 font-medium">Ujian</span>
            </nav>
            <h2 class="font-heading font-semibold text-slate-800 text-lg">
                {{ $kelas->mataKuliah->nama }}
                <span class=”text-slate-400 font-normal text-sm”>&mdash; Kelas {{ $kelas->nama_kelas }}</span>
            </h2>
        </div>
    </div>

    {{-- Quick links ke fitur lain --}}
    <div class="flex flex-wrap gap-2 mb-5">
        <a href="{{ route('dosen.pertemuan.index', $kelas) }}" class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-600 hover:border-ink-300 hover:text-ink-600 transition-colors"><i class="fa-solid fa-calendar-days text-xs"></i> Pertemuan</a>
        <a href="{{ route('dosen.materi.index',    $kelas) }}" class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-600 hover:border-ink-300 hover:text-ink-600 transition-colors"><i class="fa-solid fa-folder-open text-xs"></i> Materi</a>
        <a href="{{ route('dosen.rps.show',        $kelas) }}" class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-600 hover:border-ink-300 hover:text-ink-600 transition-colors"><i class="fa-solid fa-file-lines text-xs"></i> RPS</a>
        <a href="{{ route('dosen.tugas.index',     $kelas) }}" class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-600 hover:border-ink-300 hover:text-ink-600 transition-colors"><i class="fa-solid fa-list-check text-xs"></i> Tugas</a>
        <a href="{{ route('dosen.presensi.index',  $kelas) }}" class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-600 hover:border-ink-300 hover:text-ink-600 transition-colors"><i class="fa-solid fa-user-check text-xs"></i> Presensi</a>
        <a href="{{ route('dosen.penilaian.index', $kelas) }}" class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-600 hover:border-ink-300 hover:text-ink-600 transition-colors"><i class="fa-solid fa-star-half-stroke text-xs"></i> Penilaian</a>
    </div>

    {{-- Stats row --}}
    <div class="grid grid-cols-3 gap-3 mb-5">
        @foreach([['Quiz',$quiz->count(),'ink','circle-question'],['UTS',$uts->count(),'purple','pen-to-square'],['UAS',$uas->count(),'ember','file-pen']] as [$label,$count,$color,$icon])
        <div class="bg-white rounded-2xl p-4 border border-slate-200 flex items-center gap-3 card-hover">
            <div class="w-10 h-10 rounded-xl bg-{{ $color }}-50 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-{{ $icon }} text-{{ $color }}-500"></i>
            </div>
            <div>
                <p class="text-xl font-heading font-semibold text-slate-800">{{ $count }}</p>
                <p class="text-xs text-slate-400">{{ $label }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Tabs --}}
    @php $activeTab = session('active_tab', 'quiz'); @endphp
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">

        <div class="flex border-b border-slate-200 px-5">
            @foreach([['quiz','Quiz','circle-question','ink',$quiz],['uts','UTS','pen-to-square','purple',$uts],['uas','UAS','file-pen','ember',$uas]] as [$tipe,$label,$icon,$color,$list])
            <button onclick="switchTab('{{ $tipe }}')" id="tab-btn-{{ $tipe }}"
                class="tab-btn flex items-center gap-2 py-3.5 px-1 mr-5 text-sm font-medium border-b-2 transition-colors
                    {{ $activeTab === $tipe ? 'border-'.$color.'-500 text-'.$color.'-600' : 'border-transparent text-slate-400 hover:text-slate-600' }}">
                <i class="fa-solid fa-{{ $icon }} text-xs"></i>
                {{ $label }}
                <span class="text-xs px-1.5 py-0.5 rounded-full {{ $activeTab === $tipe ? 'bg-'.$color.'-50 text-'.$color.'-600' : 'bg-slate-100 text-slate-400' }}">
                    {{ $list->count() }}
                </span>
            </button>
            @endforeach
            <div class="ml-auto flex items-center py-2">
                <button onclick="openBuat('{{ $activeTab }}')"
                    class="inline-flex items-center gap-2 bg-ink-500 hover:bg-ink-600 text-white text-sm font-medium px-4 py-2 rounded-xl transition-colors" id="btn-buat">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span id="btn-buat-label">Buat Quiz</span>
                </button>
            </div>
        </div>

        {{-- Tab Panels --}}
        @foreach([['quiz',$quiz,'ink','circle-question'],['uts',$uts,'purple','pen-to-square'],['uas',$uas,'ember','file-pen']] as [$tipe,$list,$color,$icon])
        <div id="tab-{{ $tipe }}" class="{{ $activeTab !== $tipe ? 'hidden' : '' }}">
            @if($list->isEmpty())
            <div class="py-16 text-center">
                <i class="fa-solid fa-{{ $icon }} text-slate-200 text-5xl mb-4 block"></i>
                <p class="text-slate-500 font-medium mb-1">Belum ada {{ strtoupper($tipe) }}</p>
                <p class="text-slate-400 text-sm mb-4">Klik "Buat {{ strtoupper($tipe) }}" untuk membuat ujian baru.</p>
                <button onclick="openBuat('{{ $tipe }}')"
                    class="inline-flex items-center gap-2 bg-{{ $color }}-50 hover:bg-{{ $color }}-100 text-{{ $color }}-600 text-sm font-medium px-4 py-2 rounded-xl transition-colors border border-{{ $color }}-200">
                    <i class="fa-solid fa-plus text-xs"></i> Buat {{ strtoupper($tipe) }}
                </button>
            </div>
            @else
            <div class="divide-y divide-slate-100">
                @foreach($list as $u)
                <div class="p-5">
                    {{-- Header ujian --}}
                    <div class="flex flex-col sm:flex-row sm:items-start gap-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                {{-- Status badge --}}
                                @if($u->status === 'aktif')
                                    @if($u->is_sedang_berlangsung)
                                    <span class="inline-flex items-center gap-1 text-xs bg-sage-50 text-sage-600 px-2.5 py-1 rounded-full font-medium">
                                        <span class="w-1.5 h-1.5 bg-sage-500 rounded-full animate-pulse"></span>Berlangsung
                                    </span>
                                    @else
                                    <span class="text-xs bg-ink-50 text-ink-600 px-2.5 py-1 rounded-full font-medium">Aktif</span>
                                    @endif
                                @elseif($u->status === 'selesai')
                                <span class="text-xs bg-slate-100 text-slate-500 px-2.5 py-1 rounded-full font-medium">Selesai</span>
                                @else
                                <span class="text-xs bg-amber-50 text-amber-600 px-2.5 py-1 rounded-full font-medium">Draft</span>
                                @endif
                                {{-- Soal count --}}
                                <span class="text-xs text-slate-400">
                                    <i class="fa-solid fa-circle-question mr-1"></i>{{ $u->ujian_soal_count }} soal
                                    @if($u->is_acak_soal) · <i class="fa-solid fa-shuffle mr-1"></i>Acak @endif
                                </span>
                            </div>
                            <h3 class="font-heading font-semibold text-slate-800 text-base">{{ $u->judul }}</h3>
                            @if($u->deskripsi)
                            <p class="text-xs text-slate-400 mt-0.5">{{ \Str::limit($u->deskripsi, 100) }}</p>
                            @endif
                            {{-- Jadwal + durasi --}}
                            <div class="flex flex-wrap gap-3 mt-2 text-xs text-slate-500">
                                @if($u->mulai_at)
                                <span><i class="fa-solid fa-calendar-check mr-1 text-sage-500"></i>
                                    {{ $u->mulai_at->format('d M Y, H:i') }}
                                    @if($u->selesai_at) &mdash; {{ $u->selesai_at->format('d M Y, H:i') }} @endif
                                </span>
                                @else
                                <span class="text-amber-500"><i class="fa-solid fa-calendar-xmark mr-1"></i>Jadwal belum diatur</span>
                                @endif
                                @if($u->durasi)
                                <span><i class="fa-solid fa-clock mr-1 text-ink-400"></i>{{ $u->durasi }} menit</span>
                                @endif
                                <span><i class="fa-solid fa-rotate-right mr-1 text-purple-400"></i>{{ $u->batas_percobaan }}Ã— percobaan</span>
                                @if($u->jumlah_soal)
                                <span><i class="fa-solid fa-list-ol mr-1 text-slate-400"></i>Tampilkan {{ $u->jumlah_soal }} soal</span>
                                @endif
                            </div>
                        </div>

                        {{-- Aksi --}}
                        <div class="flex items-center gap-2 flex-shrink-0">
                            {{-- Toggle soal --}}
                            <button onclick="toggleSoalPanel({{ $u->id }})"
                                class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors">
                                <i class="fa-solid fa-list-ol text-xs"></i>
                                <span>Soal</span>
                                <span class="bg-ink-100 text-ink-600 text-xs px-1.5 py-0.5 rounded-full">{{ $u->ujian_soal_count }}</span>
                            </button>
                            {{-- Edit settings --}}
                            <button onclick="openEdit({{ $u->id }})"
                                class="w-8 h-8 rounded-xl bg-ink-50 hover:bg-ink-100 text-ink-500 flex items-center justify-center transition-colors" title="Edit">
                                <i class="fa-solid fa-gear text-xs"></i>
                            </button>
                            {{-- Status actions --}}
                            @if($u->status === 'draft')
                            <form action="{{ route('dosen.ujian.aktifkan', [$kelas, $u]) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" title="Aktifkan"
                                    class="w-8 h-8 rounded-xl bg-sage-50 hover:bg-sage-100 text-sage-600 flex items-center justify-center transition-colors">
                                    <i class="fa-solid fa-play text-xs"></i>
                                </button>
                            </form>
                            @elseif($u->status === 'aktif')
                            <form action="{{ route('dosen.ujian.tutup', [$kelas, $u]) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" title="Tutup Ujian"
                                    class="w-8 h-8 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-600 flex items-center justify-center transition-colors">
                                    <i class="fa-solid fa-stop text-xs"></i>
                                </button>
                            </form>
                            @else
                            <form action="{{ route('dosen.ujian.draftkan', [$kelas, $u]) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" title="Kembalikan ke Draft"
                                    class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition-colors">
                                    <i class="fa-solid fa-rotate-left text-xs"></i>
                                </button>
                            </form>
                            @endif
                            {{-- Hapus --}}
                            <form action="{{ route('dosen.ujian.destroy', [$kelas, $u]) }}" method="POST"
                                onsubmit="return confirm('Hapus ujian \'{{ addslashes($u->judul) }}\'?')">
                                @csrf @method('DELETE')
                                <button type="submit" title="Hapus"
                                    class="w-8 h-8 rounded-xl bg-red-50 hover:bg-red-100 text-red-400 flex items-center justify-center transition-colors">
                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Panel Soal (collapsed by default) --}}
                    <div id="soal-panel-{{ $u->id }}" class="hidden mt-4">
                        <div class="bg-slate-50 rounded-2xl border border-slate-200 overflow-hidden">
                            {{-- Toolbar panel soal --}}
                            <div class="flex flex-wrap items-center gap-2 px-4 py-3 border-b border-slate-200 bg-white">
                                <span class="text-xs font-medium text-slate-600">
                                    {{ $u->ujian_soal_count }} soal dipilih
                                    @if($u->jumlah_soal > 0)
                                    / akan ditampilkan {{ min($u->ujian_soal_count, $u->jumlah_soal) }}
                                    @endif
                                </span>
                                <div class="ml-auto flex items-center gap-2">
                                    {{-- Random soal --}}
                                    <button onclick="openRandom({{ $u->id }})"
                                        class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-600 border border-purple-200 transition-colors">
                                        <i class="fa-solid fa-shuffle text-xs"></i>Random
                                    </button>
                                    {{-- Tambah manual --}}
                                    <button onclick="openTambahSoal({{ $u->id }})"
                                        class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-xl bg-ink-500 hover:bg-ink-600 text-white transition-colors">
                                        <i class="fa-solid fa-plus text-xs"></i>Tambah Soal
                                    </button>
                                    @if($u->ujian_soal_count > 0)
                                    <form action="{{ route('dosen.ujian.hapus-semua-soal', [$kelas, $u]) }}" method="POST"
                                        onsubmit="return confirm('Hapus semua soal dari ujian ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-xl bg-red-50 hover:bg-red-100 text-red-500 border border-red-200 transition-colors">
                                            <i class="fa-solid fa-trash text-xs"></i>Hapus Semua
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>

                            {{-- Daftar soal --}}
                            @php $soalList = $u->ujianSoal()->with(['soal.pilihan'])->orderBy('nomor')->get(); @endphp
                            @if($soalList->isEmpty())
                            <div class="py-8 text-center">
                                <i class="fa-solid fa-inbox text-slate-300 text-3xl mb-2 block"></i>
                                <p class="text-slate-400 text-sm">Belum ada soal. Klik "Tambah Soal" atau "Random" untuk mulai.</p>
                            </div>
                            @else
                            <div class="divide-y divide-slate-200">
                                @foreach($soalList as $us)
                                <div class="flex items-start gap-3 px-4 py-3 hover:bg-white transition-colors">
                                    {{-- Nomor --}}
                                    <span class="flex-shrink-0 w-7 h-7 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-xs font-bold text-slate-500 mt-0.5">
                                        {{ $us->nomor }}
                                    </span>
                                    {{-- Soal info --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-0.5">
                                            @if($us->soal->tipe === 'pilihan_ganda')
                                            <span class="text-xs bg-sage-50 text-sage-600 px-2 py-0.5 rounded-full font-medium">PG</span>
                                            @else
                                            <span class="text-xs bg-purple-50 text-purple-600 px-2 py-0.5 rounded-full font-medium">Essay</span>
                                            @endif
                                            @if($us->soal->kategori)
                                            <span class="text-xs text-slate-400">{{ $us->soal->kategori }}</span>
                                            @endif
                                            <span class="text-xs text-slate-400 ml-auto">Bobot: {{ $us->soal->bobot }}</span>
                                        </div>
                                        <p class="text-sm text-slate-700 leading-snug">{{ $us->soal->pertanyaan_singkat }}</p>
                                        @if($us->soal->tipe === 'pilihan_ganda' && $us->soal->pilihan->count())
                                        <div class="flex gap-2 mt-1.5 flex-wrap">
                                            @foreach($us->soal->pilihan as $p)
                                            <span class="text-xs {{ $p->is_benar ? 'bg-sage-100 text-sage-700 font-medium' : 'bg-slate-100 text-slate-500' }} px-2 py-0.5 rounded-lg">
                                                {{ chr(65+$loop->index) }}. {{ \Str::limit($p->teks, 30) }}
                                            </span>
                                            @endforeach
                                        </div>
                                        @endif
                                    </div>
                                    {{-- Hapus dari ujian --}}
                                    <form action="{{ route('dosen.ujian.hapus-soal', [$kelas, $u, $us]) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="flex-shrink-0 w-7 h-7 rounded-lg bg-red-50 hover:bg-red-100 text-red-400 flex items-center justify-center transition-colors mt-0.5" title="Hapus">
                                            <i class="fa-solid fa-xmark text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
        @endforeach
    </div>

    {{-- ===== MODAL: Buat Ujian ===== --}}
    <div id="modal-buat" class="hidden fixed inset-0 z-50 items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" onclick="closeModal('modal-buat')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between sticky top-0 bg-white">
                <div>
                    <h3 class="font-heading font-semibold text-slate-800">Buat Ujian Baru</h3>
                    <p id="buat-tipe-label" class="text-xs text-slate-400 mt-0.5"></p>
                </div>
                <button onclick="closeModal('modal-buat')" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <form action="{{ route('dosen.ujian.store', $kelas) }}" method="POST" class="px-6 py-5 space-y-4">
                @csrf
                <input type="hidden" name="tipe" id="buat-tipe">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Judul <span class="text-red-400">*</span></label>
                    <input type="text" name="judul" required placeholder="contoh: Quiz Pertemuan 3 &mdash; Algoritma Sorting"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Deskripsi</label>
                    <textarea name="deskripsi" rows="2" placeholder="Instruksi atau keterangan tambahan (opsional)"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200 resize-none"></textarea>
                </div>
                {{-- Jadwal --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Mulai</label>
                        <input type="datetime-local" name="mulai_at"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Selesai</label>
                        <input type="datetime-local" name="selesai_at"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200">
                    </div>
                </div>
                {{-- Pengaturan --}}
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Durasi (menit)</label>
                        <input type="number" name="durasi" min="1" max="300" placeholder="90"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-ink-200">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Tampilkan Soal <span class="text-red-400">*</span></label>
                        <input type="number" name="jumlah_soal" value="10" min="1" max="200" required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-ink-200">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Maks. Percobaan <span class="text-red-400">*</span></label>
                        <input type="number" name="batas_percobaan" value="1" min="1" max="10" required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-ink-200">
                    </div>
                </div>
                {{-- Toggle options --}}
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer hover:bg-slate-100 transition-colors">
                        <input type="checkbox" name="is_acak_soal" value="1" class="w-4 h-4 rounded accent-ink-500">
                        <div>
                            <p class="text-sm font-medium text-slate-700">Acak Soal</p>
                            <p class="text-xs text-slate-400">Urutan soal berbeda tiap peserta</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer hover:bg-slate-100 transition-colors">
                        <input type="checkbox" name="is_acak_jawaban" value="1" class="w-4 h-4 rounded accent-ink-500">
                        <div>
                            <p class="text-sm font-medium text-slate-700">Acak Jawaban</p>
                            <p class="text-xs text-slate-400">Urutan opsi A"“D diacak</p>
                        </div>
                    </label>
                </div>
                <div class="flex gap-3 pt-2 border-t border-slate-100">
                    <button type="button" onclick="closeModal('modal-buat')" class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl bg-ink-500 hover:bg-ink-600 text-white text-sm font-medium transition-colors">
                        <i class="fa-solid fa-plus mr-1.5 text-xs"></i>Buat Ujian
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== MODAL: Edit Ujian ===== --}}
    <div id="modal-edit" class="hidden fixed inset-0 z-50 items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" onclick="closeModal('modal-edit')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between sticky top-0 bg-white">
                <h3 class="font-heading font-semibold text-slate-800">Edit Pengaturan Ujian</h3>
                <button onclick="closeModal('modal-edit')" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <form id="form-edit" method="POST" class="px-6 py-5 space-y-4">
                @csrf @method('PATCH')
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Judul <span class="text-red-400">*</span></label>
                    <input type="text" name="judul" id="edit-judul" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Deskripsi</label>
                    <textarea name="deskripsi" id="edit-deskripsi" rows="2"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200 resize-none"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Mulai</label>
                        <input type="datetime-local" name="mulai_at" id="edit-mulai"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Selesai</label>
                        <input type="datetime-local" name="selesai_at" id="edit-selesai"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200">
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Durasi (menit)</label>
                        <input type="number" name="durasi" id="edit-durasi" min="1" max="300"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-ink-200">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Tampilkan Soal</label>
                        <input type="number" name="jumlah_soal" id="edit-jumlah" min="1" max="200" required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-ink-200">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Maks. Percobaan</label>
                        <input type="number" name="batas_percobaan" id="edit-percobaan" min="1" max="10" required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-ink-200">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer hover:bg-slate-100 transition-colors">
                        <input type="checkbox" name="is_acak_soal" id="edit-acak-soal" value="1" class="w-4 h-4 rounded accent-ink-500">
                        <div>
                            <p class="text-sm font-medium text-slate-700">Acak Soal</p>
                            <p class="text-xs text-slate-400">Urutan soal diacak</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-200 cursor-pointer hover:bg-slate-100 transition-colors">
                        <input type="checkbox" name="is_acak_jawaban" id="edit-acak-jawaban" value="1" class="w-4 h-4 rounded accent-ink-500">
                        <div>
                            <p class="text-sm font-medium text-slate-700">Acak Jawaban</p>
                            <p class="text-xs text-slate-400">Urutan opsi diacak</p>
                        </div>
                    </label>
                </div>
                <div class="flex gap-3 pt-2 border-t border-slate-100">
                    <button type="button" onclick="closeModal('modal-edit')" class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl bg-ink-500 hover:bg-ink-600 text-white text-sm font-medium transition-colors">
                        <i class="fa-solid fa-floppy-disk mr-1.5 text-xs"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== MODAL: Tambah Soal dari Bank ===== --}}
    <div id="modal-tambah-soal" class="hidden fixed inset-0 z-50 items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" onclick="closeModal('modal-tambah-soal')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between sticky top-0 bg-white">
                <div>
                    <h3 class="font-heading font-semibold text-slate-800">Tambah Soal dari Bank</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Pilih soal yang akan dimasukkan ke ujian</p>
                </div>
                <button onclick="closeModal('modal-tambah-soal')" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            {{-- Filter dalam modal --}}
            <div class="px-6 py-3 border-b border-slate-100 flex gap-2">
                <input type="text" id="filter-soal" placeholder="Filter pertanyaan..."
                    oninput="filterSoalModal(this.value)"
                    class="flex-1 px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200">
                <select id="filter-tipe-soal" onchange="filterSoalModal(document.getElementById('filter-soal').value)"
                    class="px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200 text-slate-700">
                    <option value="">Semua Tipe</option>
                    <option value="pilihan_ganda">Pilihan Ganda</option>
                    <option value="essay">Essay</option>
                </select>
            </div>

            <form id="form-tambah-soal" method="POST" class="flex flex-col">
                @csrf
                <input type="hidden" name="_method" value="POST">

                <div id="bank-soal-list" class="px-6 py-4 space-y-2 overflow-y-auto" style="max-height:420px">
                    @forelse($bankSoal as $bs)
                    <label class="flex items-start gap-3 p-3 rounded-xl border border-slate-200 cursor-pointer hover:border-ink-300 hover:bg-ink-50/30 transition-colors has-[:checked]:border-ink-400 has-[:checked]:bg-ink-50"
                        data-teks="{{ strtolower($bs->pertanyaan) }}" data-tipe="{{ $bs->tipe }}">
                        <input type="checkbox" name="bank_soal_ids[]" value="{{ $bs->id }}" class="mt-0.5 w-4 h-4 rounded accent-ink-500 flex-shrink-0">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-0.5">
                                @if($bs->tipe === 'pilihan_ganda')
                                <span class="text-xs bg-sage-50 text-sage-600 px-1.5 py-0.5 rounded font-medium">PG</span>
                                @else
                                <span class="text-xs bg-purple-50 text-purple-600 px-1.5 py-0.5 rounded font-medium">Essay</span>
                                @endif
                                @if($bs->kategori)
                                <span class="text-xs text-slate-400">{{ $bs->kategori }}</span>
                                @endif
                                <span class="text-xs text-slate-400 ml-auto">Bobot: {{ $bs->bobot }}</span>
                            </div>
                            <p class="text-sm text-slate-700 leading-snug">{{ $bs->pertanyaan_singkat }}</p>
                        </div>
                    </label>
                    @empty
                    <div class="py-10 text-center">
                        <i class="fa-solid fa-inbox text-slate-200 text-3xl mb-2 block"></i>
                        <p class="text-slate-400 text-sm">Bank soal kosong. Buat soal di menu Bank Soal terlebih dahulu.</p>
                    </div>
                    @endforelse
                </div>

                <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between sticky bottom-0 bg-white">
                    <span id="selected-count" class="text-xs text-slate-500">0 soal dipilih</span>
                    <div class="flex gap-2">
                        <button type="button" onclick="closeModal('modal-tambah-soal')" class="px-4 py-2 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-ink-500 hover:bg-ink-600 text-white text-sm font-medium transition-colors">
                            <i class="fa-solid fa-plus mr-1.5 text-xs"></i>Tambahkan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== MODAL: Random Soal ===== --}}
    <div id="modal-random" class="hidden fixed inset-0 z-50 items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" onclick="closeModal('modal-random')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-heading font-semibold text-slate-800">Pilih Soal Acak</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Ambil soal secara otomatis dari bank soal</p>
                </div>
                <button onclick="closeModal('modal-random')" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <form id="form-random" method="POST" class="px-6 py-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Jumlah Soal yang Diambil <span class="text-red-400">*</span></label>
                    <input type="number" name="jumlah" value="10" min="1" max="100" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-ink-200">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Filter Tipe Soal</label>
                    <select name="tipe_soal" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200 bg-white">
                        <option value="">Semua Tipe</option>
                        <option value="pilihan_ganda">Pilihan Ganda saja</option>
                        <option value="essay">Essay saja</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Filter Kategori</label>
                    <input type="text" name="kategori" placeholder="Kosongkan untuk semua kategori"
                        list="kategori-random-list"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200">
                    <datalist id="kategori-random-list">
                        @foreach($bankSoal->whereNotNull('kategori')->pluck('kategori')->unique() as $kat)
                        <option value="{{ $kat }}">
                        @endforeach
                    </datalist>
                </div>
                <p class="text-xs text-slate-400 bg-slate-50 rounded-xl px-3 py-2.5">
                    <i class="fa-solid fa-circle-info text-slate-400 mr-1"></i>
                    Soal yang sudah ada dalam ujian ini tidak akan duplikat.
                </p>
                <div class="flex gap-3">
                    <button type="button" onclick="closeModal('modal-random')" class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl bg-purple-500 hover:bg-purple-600 text-white text-sm font-medium transition-colors">
                        <i class="fa-solid fa-shuffle mr-1.5 text-xs"></i>Pilih Acak
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Ujian data untuk JS --}}
    <script id="ujian-data" type="application/json">
        {!! json_encode(collect([$quiz,$uts,$uas])->flatten()->map(fn($u) => [
            'id'              => $u->id,
            'tipe'            => $u->tipe,
            'judul'           => $u->judul,
            'deskripsi'       => $u->deskripsi,
            'durasi'          => $u->durasi,
            'mulai_at'        => $u->mulai_at?->format('Y-m-d\TH:i'),
            'selesai_at'      => $u->selesai_at?->format('Y-m-d\TH:i'),
            'jumlah_soal'     => $u->jumlah_soal,
            'batas_percobaan' => $u->batas_percobaan,
            'is_acak_soal'    => $u->is_acak_soal,
            'is_acak_jawaban' => $u->is_acak_jawaban,
        ])) !!}
    </script>

    @push('scripts')
    <script>
    const ujianData = JSON.parse(document.getElementById('ujian-data').textContent);
    const tipeLabels = { quiz: 'Quiz', uts: 'UTS', uas: 'UAS' };

    function openModal(id) { const el = document.getElementById(id); el.classList.remove('hidden'); el.classList.add('flex'); }
    function closeModal(id) { const el = document.getElementById(id); el.classList.add('hidden'); el.classList.remove('flex'); }

    // Switch tab Quiz / UTS / UAS
    function switchTab(tipe) {
        ['quiz','uts','uas'].forEach(t => {
            document.getElementById('tab-'+t).classList.toggle('hidden', t !== tipe);
            const btn = document.getElementById('tab-btn-'+t);
            const colors = {quiz:'ink',uts:'purple',uas:'ember'};
            const c = colors[t];
            btn.classList.toggle('border-transparent', t !== tipe);
            btn.classList.toggle('text-slate-400', t !== tipe);
        });
        document.getElementById('btn-buat').onclick = () => openBuat(tipe);
        document.getElementById('btn-buat-label').textContent = 'Buat '+tipeLabels[tipe];
    }

    // Buka modal buat ujian dengan tipe tertentu
    function openBuat(tipe) {
        document.getElementById('buat-tipe').value = tipe;
        document.getElementById('buat-tipe-label').textContent = 'Tipe: '+tipeLabels[tipe];
        openModal('modal-buat');
    }

    // Toggle panel soal per ujian
    function toggleSoalPanel(ujianId) {
        const panel = document.getElementById('soal-panel-'+ujianId);
        panel.classList.toggle('hidden');
    }

    // Buka modal edit ujian
    function openEdit(ujianId) {
        const u = ujianData.find(x => x.id === ujianId);
        if (!u) return;
        document.getElementById('form-edit').action =
            '{{ route("dosen.ujian.update", [$kelas, "__ID__"]) }}'.replace('__ID__', ujianId);
        document.getElementById('edit-judul').value         = u.judul;
        document.getElementById('edit-deskripsi').value     = u.deskripsi ?? '';
        document.getElementById('edit-mulai').value         = u.mulai_at ?? '';
        document.getElementById('edit-selesai').value       = u.selesai_at ?? '';
        document.getElementById('edit-durasi').value        = u.durasi ?? '';
        document.getElementById('edit-jumlah').value        = u.jumlah_soal;
        document.getElementById('edit-percobaan').value     = u.batas_percobaan;
        document.getElementById('edit-acak-soal').checked   = u.is_acak_soal;
        document.getElementById('edit-acak-jawaban').checked = u.is_acak_jawaban;
        openModal('modal-edit');
    }

    // Buka modal tambah soal (pilih manual)
    function openTambahSoal(ujianId) {
        document.getElementById('form-tambah-soal').action =
            '{{ route("dosen.ujian.tambah-soal", [$kelas, "__ID__"]) }}'.replace('__ID__', ujianId);
        // Reset checkboxes
        document.querySelectorAll('#bank-soal-list input[type=checkbox]').forEach(c => c.checked = false);
        updateSelectedCount();
        document.getElementById('filter-soal').value = '';
        filterSoalModal('');
        openModal('modal-tambah-soal');
    }

    // Buka modal random soal
    function openRandom(ujianId) {
        document.getElementById('form-random').action =
            '{{ route("dosen.ujian.random-soal", [$kelas, "__ID__"]) }}'.replace('__ID__', ujianId);
        openModal('modal-random');
    }

    // Filter soal dalam modal tambah
    function filterSoalModal(search) {
        const tipe   = document.getElementById('filter-tipe-soal').value;
        const query  = search.toLowerCase();
        document.querySelectorAll('#bank-soal-list label[data-teks]').forEach(el => {
            const matchText = el.dataset.teks.includes(query);
            const matchTipe = !tipe || el.dataset.tipe === tipe;
            el.classList.toggle('hidden', !(matchText && matchTipe));
        });
    }

    // Count selected soal
    function updateSelectedCount() {
        const n = document.querySelectorAll('#bank-soal-list input[type=checkbox]:checked').length;
        document.getElementById('selected-count').textContent = n+' soal dipilih';
    }

    document.querySelectorAll('#bank-soal-list input[type=checkbox]').forEach(c => {
        c.addEventListener('change', updateSelectedCount);
    });

    // Auto-switch ke tab yang sesuai setelah redirect
    @if(session('active_tab'))
    switchTab('{{ session('active_tab') }}');
    @endif
    </script>
    @endpush
</x-app-layout>
