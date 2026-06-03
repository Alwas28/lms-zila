<x-app-layout>
    <x-slot name="title">Penilaian — {{ $kelas->mataKuliah->nama }}</x-slot>
    <x-slot name="pageTitle">Penilaian</x-slot>

    @include('admin._flash')

    {{-- Breadcrumb + Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
        <div>
            <nav class="flex items-center gap-1.5 text-xs text-slate-400 mb-1">
                <a href="{{ route('dosen.mata-kuliah.index') }}" class="hover:text-ink-500 transition-colors">Mata Kuliah Saya</a>
                <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
                <span class="text-slate-600">{{ $kelas->mataKuliah->nama }}</span>
                <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
                <span class="text-ink-500 font-medium">Penilaian</span>
            </nav>
            <h2 class="font-heading font-semibold text-slate-800 text-lg">
                {{ $kelas->mataKuliah->nama }}
                <span class="text-slate-400 font-normal text-sm">— Kelas {{ $kelas->nama_kelas }}</span>
            </h2>
        </div>
    </div>

    {{-- Konfigurasi Bobot --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-5 mb-5">
        <div class="flex items-center gap-2 mb-4">
            <div class="w-7 h-7 rounded-lg bg-ink-50 flex items-center justify-center">
                <i class="fa-solid fa-sliders text-ink-500 text-xs"></i>
            </div>
            <h3 class="font-heading font-semibold text-slate-800 text-sm">Konfigurasi Bobot Penilaian</h3>
            <span class="ml-auto text-xs text-slate-400">Total harus 100%</span>
        </div>
        <form action="{{ route('dosen.penilaian.config', $kelas) }}" method="POST">
            @csrf @method('PATCH')
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-4">
                @foreach(['kehadiran' => 'Kehadiran', 'tugas' => 'Tugas', 'quiz' => 'Quiz', 'uts' => 'UTS', 'uas' => 'UAS'] as $key => $label)
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ $label }} (%)</label>
                    <div class="relative">
                        <input type="number" name="bobot_{{ $key }}" id="bobot-{{ $key }}"
                            value="{{ $config->{'bobot_'.$key} }}" min="0" max="100"
                            oninput="updateTotal()"
                            class="w-full px-3 py-2 pr-7 rounded-xl border border-slate-200 text-sm font-mono font-semibold text-center focus:outline-none focus:ring-2 focus:ring-ink-200">
                        <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs">%</span>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-sm text-slate-500">Total:</span>
                    <span id="total-bobot" class="text-sm font-heading font-bold text-slate-800">
                        {{ $config->bobot_kehadiran + $config->bobot_tugas + $config->bobot_quiz + $config->bobot_uts + $config->bobot_uas }}%
                    </span>
                    <span id="total-warning" class="text-xs text-red-500 hidden">— Total harus 100%</span>
                    <span id="total-ok" class="text-xs text-sage-500 {{ ($config->bobot_kehadiran + $config->bobot_tugas + $config->bobot_quiz + $config->bobot_uts + $config->bobot_uas) === 100 ? '' : 'hidden' }}">
                        <i class="fa-solid fa-circle-check"></i>
                    </span>
                </div>
                <button type="submit" class="px-4 py-2 rounded-xl bg-ink-500 hover:bg-ink-600 text-white text-sm font-medium transition-colors">
                    <i class="fa-solid fa-floppy-disk mr-1.5 text-xs"></i>Simpan Bobot
                </button>
            </div>
        </form>
    </div>

    {{-- Tabel Nilai --}}
    <form action="{{ route('dosen.penilaian.simpan', $kelas) }}" method="POST">
        @csrf
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden mb-4">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Mahasiswa</th>
                            <th class="text-center px-3 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide w-20">Kehadiran</th>
                            <th class="text-center px-3 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide w-20">Tugas</th>
                            <th class="text-center px-3 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide w-20">Quiz</th>
                            <th class="text-center px-3 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide w-20">UTS</th>
                            <th class="text-center px-3 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide w-20">UAS</th>
                            <th class="text-center px-3 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide w-24">Nilai Akhir</th>
                            <th class="text-center px-3 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide w-16">Grade</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($mahasiswa as $idx => $mhs)
                        @php
                            $pen = $penilaianMap->get($mhs->id);
                        @endphp
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3">
                                <input type="hidden" name="penilaian[{{ $idx }}][user_id]" value="{{ $mhs->id }}">
                                <p class="font-medium text-slate-800 text-sm">{{ $mhs->name }}</p>
                                <p class="text-xs text-slate-400 font-mono">{{ $mhs->nip_nim ?? '—' }}</p>
                            </td>
                            @foreach(['kehadiran','tugas','quiz','uts','uas'] as $k)
                            <td class="px-2 py-2.5 text-center">
                                <input type="number" name="penilaian[{{ $idx }}][{{ $k }}]"
                                    value="{{ $pen ? $pen->{'nilai_'.$k} : '' }}"
                                    min="0" max="100" step="0.5" placeholder="—"
                                    oninput="updateNilaiAkhir({{ $idx }})"
                                    data-idx="{{ $idx }}" data-komponen="{{ $k }}"
                                    class="w-16 px-2 py-1.5 rounded-lg border border-slate-200 text-xs font-mono text-center focus:outline-none focus:ring-2 focus:ring-ink-200">
                            </td>
                            @endforeach
                            <td class="px-3 py-2.5 text-center">
                                <span id="nilai-akhir-{{ $idx }}" class="text-sm font-heading font-bold text-slate-700">
                                    {{ $pen && $pen->nilai_akhir ? number_format($pen->nilai_akhir, 1) : '—' }}
                                </span>
                            </td>
                            <td class="px-3 py-2.5 text-center">
                                @php $grade = $pen ? $pen->grade : null; @endphp
                                <span id="grade-{{ $idx }}" class="text-xs font-heading font-bold px-2 py-1 rounded-lg
                                    @if($grade === 'A' || $grade === 'A-') bg-sage-50 text-sage-700
                                    @elseif(str_starts_with($grade ?? '', 'B')) bg-blue-50 text-blue-700
                                    @elseif(str_starts_with($grade ?? '', 'C')) bg-amber-50 text-amber-700
                                    @elseif($grade) bg-red-50 text-red-500
                                    @else bg-slate-100 text-slate-400 @endif">
                                    {{ $grade ?? '—' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-5 py-16 text-center">
                                <i class="fa-solid fa-user-graduate text-slate-200 text-4xl mb-3 block"></i>
                                <p class="text-slate-400 text-sm">Belum ada mahasiswa terdaftar di kelas ini.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($mahasiswa->isNotEmpty())
        <div class="flex justify-end">
            <button type="submit" class="inline-flex items-center gap-2 bg-ink-500 hover:bg-ink-600 text-white text-sm font-medium px-6 py-2.5 rounded-xl transition-colors">
                <i class="fa-solid fa-floppy-disk text-xs"></i> Simpan Semua Nilai
            </button>
        </div>
        @endif
    </form>

    @push('scripts')
    <script>
        const bobot = {
            kehadiran: {{ $config->bobot_kehadiran }},
            tugas:     {{ $config->bobot_tugas }},
            quiz:      {{ $config->bobot_quiz }},
            uts:       {{ $config->bobot_uts }},
            uas:       {{ $config->bobot_uas }},
        };

        function updateTotal() {
            let total = 0;
            ['kehadiran','tugas','quiz','uts','uas'].forEach(k => {
                const v = parseFloat(document.getElementById('bobot-' + k).value) || 0;
                total += v;
            });
            document.getElementById('total-bobot').textContent = total + '%';
            const isOk = Math.round(total) === 100;
            document.getElementById('total-warning').classList.toggle('hidden', isOk);
            document.getElementById('total-ok').classList.toggle('hidden', !isOk);
        }

        function updateNilaiAkhir(idx) {
            const komps = ['kehadiran','tugas','quiz','uts','uas'];
            let weightedSum = 0;
            let totalBobot = 0;
            komps.forEach(k => {
                const input = document.querySelector(`input[data-idx="${idx}"][data-komponen="${k}"]`);
                const val = parseFloat(input?.value) || 0;
                const b = bobot[k] || 0;
                weightedSum += val * b;
                totalBobot += b;
            });
            const nilaiAkhir = totalBobot > 0 ? weightedSum / totalBobot : 0;
            document.getElementById('nilai-akhir-' + idx).textContent = nilaiAkhir.toFixed(1);
            document.getElementById('grade-' + idx).textContent = hitungGrade(nilaiAkhir);
            updateGradeStyle(idx, nilaiAkhir);
        }

        function hitungGrade(n) {
            if (n >= 85) return 'A';
            if (n >= 80) return 'A-';
            if (n >= 75) return 'B+';
            if (n >= 70) return 'B';
            if (n >= 65) return 'B-';
            if (n >= 60) return 'C+';
            if (n >= 55) return 'C';
            if (n >= 40) return 'D';
            return 'E';
        }

        function updateGradeStyle(idx, n) {
            const el = document.getElementById('grade-' + idx);
            el.className = 'text-xs font-heading font-bold px-2 py-1 rounded-lg ';
            if (n >= 80) el.className += 'bg-sage-50 text-sage-700';
            else if (n >= 65) el.className += 'bg-blue-50 text-blue-700';
            else if (n >= 55) el.className += 'bg-amber-50 text-amber-700';
            else if (n >= 40) el.className += 'bg-red-50 text-red-500';
            else el.className += 'bg-red-100 text-red-600';
        }
    </script>
    @endpush
</x-app-layout>
