{{-- ===== STATS ROW ===== --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-6">

    {{-- Mata Kuliah Diikuti --}}
    <div class="bg-white rounded-2xl p-4 md:p-5 border border-slate-200 card-hover">
        <div class="flex items-start justify-between mb-3">
            <div class="w-9 h-9 md:w-10 md:h-10 rounded-xl bg-ink-50 flex items-center justify-center">
                <i class="fa-solid fa-book-open text-ink-500 text-sm md:text-base"></i>
            </div>
            <span class="text-xs text-ink-600 bg-ink-50 px-2 py-0.5 rounded-full font-medium badge hidden sm:block">Aktif</span>
        </div>
        <p class="text-xl md:text-2xl font-heading font-semibold text-slate-800">{{ $jumlahMK ?? 0 }}</p>
        <p class="text-xs md:text-sm text-slate-400 mt-0.5">Mata Kuliah</p>
    </div>

    {{-- Tugas Belum Dikerjakan --}}
    <div class="bg-white rounded-2xl p-4 md:p-5 border border-slate-200 card-hover">
        <div class="flex items-start justify-between mb-3">
            <div class="w-9 h-9 md:w-10 md:h-10 rounded-xl bg-ember-50 flex items-center justify-center">
                <i class="fa-solid fa-list-check text-ember-500 text-sm md:text-base"></i>
            </div>
            <span class="text-xs text-ember-700 bg-ember-50 px-2 py-0.5 rounded-full font-medium badge hidden sm:block">Pending</span>
        </div>
        <p class="text-xl md:text-2xl font-heading font-semibold text-slate-800">{{ $tugasBelumDikumpulkan ?? 0 }}</p>
        <p class="text-xs md:text-sm text-slate-400 mt-0.5">Tugas Belum Dikerjakan</p>
    </div>

    {{-- Jadwal Ujian --}}
    <div class="bg-white rounded-2xl p-4 md:p-5 border border-slate-200 card-hover">
        <div class="flex items-start justify-between mb-3">
            <div class="w-9 h-9 md:w-10 md:h-10 rounded-xl bg-purple-50 flex items-center justify-center">
                <i class="fa-solid fa-clipboard-question text-purple-500 text-sm md:text-base"></i>
            </div>
            <span class="text-xs text-purple-600 bg-purple-50 px-2 py-0.5 rounded-full font-medium badge hidden sm:block">Mendatang</span>
        </div>
        <p class="text-xl md:text-2xl font-heading font-semibold text-slate-800">{{ $ujianMendatang ?? 0 }}</p>
        <p class="text-xs md:text-sm text-slate-400 mt-0.5">Ujian Mendatang</p>
    </div>

    {{-- Nilai Rata-rata --}}
    <div class="bg-white rounded-2xl p-4 md:p-5 border border-slate-200 card-hover">
        <div class="flex items-start justify-between mb-3">
            <div class="w-9 h-9 md:w-10 md:h-10 rounded-xl bg-sage-50 flex items-center justify-center">
                <i class="fa-solid fa-chart-bar text-sage-500 text-sm md:text-base"></i>
            </div>
            <span class="text-xs text-sage-600 bg-sage-50 px-2 py-0.5 rounded-full font-medium badge hidden sm:block">IPK</span>
        </div>
        <p class="text-xl md:text-2xl font-heading font-semibold text-slate-800">{{ $rataRataNilai ? number_format($rataRataNilai, 1) : '—' }}</p>
        <p class="text-xs md:text-sm text-slate-400 mt-0.5">Rata-rata Nilai</p>
    </div>

</div>

{{-- ===== TWO COLUMN ===== --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-4 md:gap-6">

    {{-- LEFT: Mata Kuliah + Nilai --}}
    <div class="xl:col-span-2 space-y-4">

        {{-- Mata Kuliah Semester Ini --}}
        <div>
            <div class="flex items-center justify-between mb-3">
                <h2 class="font-heading font-semibold text-slate-800 text-sm md:text-base">Mata Kuliah Semester Ini</h2>
                <a href="{{ route('mahasiswa.kelas.index') }}" class="text-xs md:text-sm text-ink-500 hover:text-ink-700 font-medium transition-colors">Lihat Semua →</a>
            </div>

            @if(($kelasEnrolled ?? collect())->isEmpty())
            <div class="bg-white rounded-2xl border border-slate-200 p-10 text-center">
                <i class="fa-solid fa-book-open text-slate-200 text-3xl mb-3"></i>
                <p class="text-slate-400 text-sm">Belum terdaftar di kelas manapun.</p>
            </div>
            @else
            <div class="space-y-3">
                @php $colors = ['ink','sage','ember','purple','blue']; @endphp
                @foreach($kelasEnrolled as $idx => $k)
                @php
                    $color = $colors[$idx % count($colors)];
                    $pct   = $k->pertemuan_count > 0 ? min(100, round($k->pertemuan_count / 16 * 100)) : 0;
                    $dosen = $k->koordinator();
                @endphp
                <div class="bg-white rounded-2xl p-4 border border-slate-200 card-hover flex gap-3">
                    <div class="w-12 h-12 rounded-xl bg-{{ $color }}-500 flex items-center justify-center flex-shrink-0 shadow-sm">
                        <i class="fa-solid fa-book text-white"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start gap-2 mb-1">
                            <div class="flex-1 min-w-0">
                                <span class="text-xs font-mono text-slate-400">{{ $k->mataKuliah->kode }} · {{ $k->mataKuliah->sks }} SKS · Kelas {{ $k->nama_kelas }}</span>
                                <h3 class="font-heading font-semibold text-slate-800 text-sm leading-snug truncate">{{ $k->mataKuliah->nama }}</h3>
                            </div>
                        </div>
                        @if($dosen)
                        <p class="text-xs text-slate-400 mb-2"><i class="fa-solid fa-user mr-1"></i>{{ $dosen->name }}</p>
                        @endif
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-slate-400">{{ $k->pertemuan_count }}/16 Pertemuan</span>
                            <span class="font-medium text-{{ $color }}-600">{{ $pct }}%</span>
                        </div>
                        <div class="h-1.5 bg-slate-100 rounded-full">
                            <div class="h-1.5 bg-{{ $color }}-500 rounded-full" style="width:{{ $pct }}%"></div>
                        </div>
                    </div>
                    <a href="{{ route('mahasiswa.materi.index', $k) }}"
                        class="self-center w-8 h-8 rounded-xl bg-{{ $color }}-50 hover:bg-{{ $color }}-100 flex items-center justify-center text-{{ $color }}-500 transition-colors flex-shrink-0">
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Nilai Per Mata Kuliah --}}
        @if(($penilaianList ?? collect())->isNotEmpty())
        <div class="bg-white rounded-2xl p-5 border border-slate-200">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-heading font-semibold text-slate-800 text-sm md:text-base">Rekap Nilai</h2>
                <a href="{{ route('mahasiswa.kelas.index') }}" class="text-xs text-ink-500 hover:text-ink-700 font-medium">Lihat Detail →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-xs text-slate-400 border-b border-slate-100">
                            <th class="text-left pb-2 font-medium">Mata Kuliah</th>
                            <th class="text-center pb-2 font-medium">Tugas</th>
                            <th class="text-center pb-2 font-medium">UTS</th>
                            <th class="text-center pb-2 font-medium">UAS</th>
                            <th class="text-center pb-2 font-medium">Akhir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($penilaianList as $p)
                        @php
                            $grade = $p->grade ?? '—';
                            $gradeCls = match(substr($grade,0,1)) { 'A'=>'text-sage-600 bg-sage-50', 'B'=>'text-blue-600 bg-blue-50', 'C'=>'text-amber-600 bg-amber-50', default=>'text-slate-400 bg-slate-50' };
                        @endphp
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-2.5 pr-3 text-xs font-medium text-slate-700 truncate max-w-[160px]">{{ $p->kelas->mataKuliah->nama }}</td>
                            <td class="py-2.5 text-center text-xs text-slate-600">{{ $p->nilai_tugas !== null ? number_format($p->nilai_tugas,1) : '—' }}</td>
                            <td class="py-2.5 text-center text-xs text-slate-600">{{ $p->nilai_uts !== null ? number_format($p->nilai_uts,1) : '—' }}</td>
                            <td class="py-2.5 text-center text-xs text-slate-600">{{ $p->nilai_uas !== null ? number_format($p->nilai_uas,1) : '—' }}</td>
                            <td class="py-2.5 text-center">
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-lg {{ $gradeCls }}">{{ $grade }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

    </div>

    {{-- RIGHT: Widgets --}}
    <div class="space-y-4">

        {{-- Tenggat Tugas --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-heading font-semibold text-slate-800 text-sm">Tugas Mendatang</h3>
                <a href="{{ route('mahasiswa.kelas.index') }}" class="text-xs text-ink-500 hover:text-ink-700 font-medium">Semua →</a>
            </div>
            @if(($tugasMendatang ?? collect())->isEmpty())
            <div class="text-center py-6">
                <i class="fa-solid fa-circle-check text-sage-300 text-2xl mb-2"></i>
                <p class="text-xs text-slate-400">Tidak ada tugas pending</p>
            </div>
            @else
            <div class="space-y-3">
                @foreach($tugasMendatang as $t)
                @php
                    $isNear  = $t->deadline && $t->deadline->diffInHours(now()) <= 24;
                    $expired = $t->is_expired;
                @endphp
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl {{ $isNear ? 'bg-ember-50' : 'bg-ink-50' }} flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-file-pen {{ $isNear ? 'text-ember-500' : 'text-ink-500' }} text-xs"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-slate-700 truncate">{{ $t->judul }}</p>
                        <p class="text-xs text-slate-400 truncate">{{ $t->kelas->mataKuliah->nama }}</p>
                        @if($t->deadline)
                        <p class="text-xs {{ $isNear ? 'text-ember-500 font-medium' : 'text-slate-400' }} mt-0.5">
                            {{ $t->deadline->isoFormat('D MMM, HH:mm') }}
                        </p>
                        @endif
                    </div>
                    <a href="{{ route('mahasiswa.tugas.show', [$t->kelas, $t]) }}"
                        class="text-xs {{ $isNear ? 'bg-ember-50 text-ember-600 hover:bg-ember-100' : 'bg-ink-50 text-ink-600 hover:bg-ink-100' }} px-2.5 py-1 rounded-lg transition-colors flex-shrink-0 font-medium">
                        Kumpul
                    </a>
                </div>
                @if(!$loop->last)<div class="h-px bg-slate-50"></div>@endif
                @endforeach
            </div>
            @endif
        </div>

        {{-- Ujian Mendatang --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-200">
            <h3 class="font-heading font-semibold text-slate-800 text-sm mb-4">Ujian Mendatang</h3>
            @if(($ujianMendatangList ?? collect())->isEmpty())
            <div class="text-center py-6">
                <i class="fa-solid fa-clipboard-question text-slate-200 text-2xl mb-2"></i>
                <p class="text-xs text-slate-400">Tidak ada ujian aktif saat ini</p>
            </div>
            @else
            <div class="space-y-3">
                @foreach($ujianMendatangList as $u)
                @php $typeColor = match($u->tipe) { 'uts'=>'ember','uas'=>'red', default=>'purple' }; @endphp
                <div class="flex gap-3 items-start">
                    <div class="w-9 h-9 rounded-xl bg-{{ $typeColor }}-100 flex items-center justify-center flex-shrink-0">
                        <span class="text-xs font-heading font-bold text-{{ $typeColor }}-600">{{ strtoupper($u->tipe) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-slate-700 truncate">{{ $u->judul }}</p>
                        <p class="text-xs text-slate-400 truncate">{{ $u->kelas->mataKuliah->nama }}</p>
                        @if($u->mulai_at)
                        <p class="text-xs text-ink-500 mt-0.5">{{ $u->mulai_at->isoFormat('D MMM, HH:mm') }}</p>
                        @endif
                    </div>
                    <a href="{{ route('mahasiswa.ujian.show', [$u->kelas, $u]) }}"
                        class="text-xs bg-ink-50 text-ink-600 hover:bg-ink-100 px-2 py-1 rounded-lg font-medium flex-shrink-0">
                        Mulai
                    </a>
                </div>
                @if(!$loop->last)<div class="h-px bg-slate-50"></div>@endif
                @endforeach
            </div>
            @endif
        </div>

        {{-- Akses Cepat --}}
        <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl p-5 border border-slate-700">
            <div class="flex items-center gap-2 mb-4">
                <i class="fa-solid fa-bolt text-amber-400 text-sm"></i>
                <h3 class="font-heading font-semibold text-white text-sm">Akses Cepat</h3>
            </div>
            <div class="grid grid-cols-2 gap-2">
                @foreach([
                    [route('mahasiswa.kelas.index'),'fa-book-open','Mata Kuliah','ink'],
                    [route('mahasiswa.kelas.index'),'fa-list-check','Tugas','ember'],
                    [route('mahasiswa.kelas.index'),'fa-user-check','Presensi','sage'],
                    [route('mahasiswa.kelas.index'),'fa-chart-bar','Nilai','purple'],
                ] as [$url,$ic,$lbl,$cl])
                <a href="{{ $url }}"
                    class="flex items-center gap-2 bg-white/10 hover:bg-white/20 rounded-xl px-3 py-2.5 transition-colors">
                    <i class="fa-solid {{ $ic }} text-{{ $cl }}-400 text-sm w-4 text-center"></i>
                    <span class="text-white/80 text-xs font-medium">{{ $lbl }}</span>
                </a>
                @endforeach
            </div>
        </div>

    </div>

</div>
