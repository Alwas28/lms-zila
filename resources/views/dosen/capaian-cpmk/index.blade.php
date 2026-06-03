<x-app-layout>
    <x-slot name="title">Capaian CPMK — {{ $kelas->mataKuliah->nama }}</x-slot>
    <x-slot name="pageTitle">Capaian CPMK</x-slot>

    @include('admin._flash')

    {{-- Breadcrumb + Header --}}
    <div class="mb-6">
        <nav class="flex items-center gap-1.5 text-xs text-slate-400 mb-1">
            <a href="{{ route('dosen.mata-kuliah.index') }}" class="hover:text-ink-500 transition-colors">Mata Kuliah Saya</a>
            <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
            <span class="text-slate-600">{{ $kelas->mataKuliah->nama }}</span>
            <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
            <span class="text-ink-500 font-medium">Capaian CPMK</span>
        </nav>
        <h2 class="font-heading font-semibold text-slate-800 text-lg">
            {{ $kelas->mataKuliah->nama }}
            <span class="text-slate-400 font-normal text-sm">— Kelas {{ $kelas->nama_kelas }}</span>
        </h2>
        <p class="text-xs text-slate-400 mt-0.5">
            Semester {{ ucfirst($kelas->semester->tipe) }} — {{ $kelas->semester->tahunAkademik->nama }}
        </p>
    </div>

    {{-- Peringatan jika RPS belum diisi --}}
    @if(empty($cpmkLines))
    <div class="bg-ember-50 border border-ember-200 rounded-2xl p-5 mb-6 flex items-start gap-3">
        <i class="fa-solid fa-triangle-exclamation text-ember-500 mt-0.5"></i>
        <div>
            <p class="text-sm font-medium text-ember-700">CPMK belum diisi di RPS</p>
            <p class="text-xs text-ember-600 mt-0.5">
                Silakan isi CPMK pada
                <a href="{{ route('dosen.rps.show', $kelas) }}" class="underline font-medium hover:text-ember-800">halaman RPS</a>
                terlebih dahulu. Setiap baris = satu item CPMK.
            </p>
        </div>
    </div>
    @endif

    @if(!empty($cpmkLines))
    {{-- Tab Nav --}}
    <div class="flex gap-1 mb-5 bg-slate-100 p-1 rounded-xl w-fit">
        <button onclick="switchTab('mapping')" id="tab-mapping"
            class="px-4 py-2 rounded-lg text-sm font-medium transition-all tab-btn tab-active">
            <i class="fa-solid fa-sliders mr-1.5 text-xs"></i>Mapping Komponen
        </button>
        <button onclick="switchTab('capaian')" id="tab-capaian"
            class="px-4 py-2 rounded-lg text-sm font-medium transition-all tab-btn">
            <i class="fa-solid fa-chart-bar mr-1.5 text-xs"></i>Capaian Mahasiswa
        </button>
    </div>

    {{-- TAB 1: Mapping Komponen --}}
    <div id="panel-mapping">
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-ink-50 flex items-center justify-center">
                    <i class="fa-solid fa-sliders text-ink-500 text-xs"></i>
                </div>
                <div>
                    <h3 class="font-heading font-semibold text-slate-800 text-sm">Mapping CPMK ke Komponen Penilaian</h3>
                    <p class="text-xs text-slate-400">Centang komponen yang digunakan untuk mengukur setiap CPMK</p>
                </div>
            </div>

            <form action="{{ route('dosen.capaian-cpmk.update', $kelas) }}" method="POST">
                @csrf @method('PATCH')

                <div class="divide-y divide-slate-50">
                    @foreach($cpmkLines as $i => $line)
                    @php $nomor = $i + 1; $mapped = $mappings[$nomor] ?? []; @endphp
                    <div class="px-5 py-4 flex flex-col sm:flex-row sm:items-center gap-3">
                        {{-- CPMK label --}}
                        <div class="flex-1 min-w-0">
                            <span class="inline-block text-xs font-semibold text-ink-600 bg-ink-50 px-2 py-0.5 rounded-full mb-1">CPMK-{{ $nomor }}</span>
                            <p class="text-sm text-slate-700 leading-snug">{{ $line }}</p>
                        </div>
                        {{-- Komponen checkboxes --}}
                        <div class="flex flex-wrap gap-2 sm:justify-end">
                            @foreach(['kehadiran' => 'Kehadiran', 'tugas' => 'Tugas', 'quiz' => 'Quiz', 'uts' => 'UTS', 'uas' => 'UAS'] as $k => $label)
                            @php $checked = in_array($k, $mapped); @endphp
                            <label class="cursor-pointer select-none">
                                <input type="checkbox"
                                    name="mapping[{{ $nomor }}][]"
                                    value="{{ $k }}"
                                    {{ $checked ? 'checked' : '' }}
                                    class="sr-only peer">
                                <span class="inline-flex items-center px-3 py-1.5 rounded-xl border text-xs font-medium transition-all
                                    bg-slate-50 text-slate-500 border-slate-200
                                    peer-checked:bg-ink-500 peer-checked:text-white peer-checked:border-transparent">
                                    {{ $label }}
                                </span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="px-5 py-4 border-t border-slate-100 flex justify-end">
                    <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-ink-500 hover:bg-ink-600 text-white text-sm font-medium transition-colors">
                        <i class="fa-solid fa-floppy-disk mr-1.5 text-xs"></i>Simpan Mapping
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- TAB 2: Capaian Mahasiswa --}}
    <div id="panel-capaian" class="hidden">

        @if($mahasiswa->isEmpty())
        <div class="bg-white rounded-2xl border border-slate-200 p-16 text-center">
            <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-user-graduate text-slate-300 text-2xl"></i>
            </div>
            <p class="text-slate-400 text-sm">Belum ada mahasiswa terdaftar di kelas ini.</p>
        </div>
        @else

        {{-- Chart --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5 mb-5">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-7 h-7 rounded-lg bg-sage-50 flex items-center justify-center">
                    <i class="fa-solid fa-chart-bar text-sage-500 text-xs"></i>
                </div>
                <h3 class="font-heading font-semibold text-slate-800 text-sm">Rata-rata Capaian CPMK Kelas</h3>
                <div class="ml-auto flex items-center gap-3 text-xs text-slate-400">
                    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-sm bg-ink-400 inline-block"></span>Rata-rata kelas</span>
                    <span class="flex items-center gap-1"><span class="w-8 border-t-2 border-dashed border-ember-400 inline-block"></span>Target 70</span>
                </div>
            </div>
            <div class="relative h-56">
                <canvas id="chartCapaian"></canvas>
            </div>
        </div>

        {{-- Legenda warna --}}
        <div class="flex flex-wrap gap-3 mb-4 text-xs">
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-sage-100 border border-sage-300 inline-block"></span><span class="text-slate-600">≥ 70 — Memuaskan</span></span>
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-ember-100 border border-ember-300 inline-block"></span><span class="text-slate-600">60–69 — Cukup</span></span>
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-red-100 border border-red-300 inline-block"></span><span class="text-slate-600">< 60 — Kurang</span></span>
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-slate-100 border border-slate-200 inline-block"></span><span class="text-slate-600">— Belum ada data</span></span>
        </div>

        {{-- Tabel Capaian --}}
        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 w-48 sticky left-0 bg-slate-50">Mahasiswa</th>
                            @foreach($cpmkLines as $i => $line)
                            <th class="px-3 py-3 text-center text-xs font-semibold text-slate-500 whitespace-nowrap min-w-[80px]">
                                <span class="block text-ink-600">CPMK-{{ $i+1 }}</span>
                                <span class="block font-normal text-slate-400 max-w-[120px] truncate" title="{{ $line }}">
                                    {{ Str::limit($line, 30) }}
                                </span>
                            </th>
                            @endforeach
                            <th class="px-3 py-3 text-center text-xs font-semibold text-slate-500 min-w-[80px]">Rata-rata</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($mahasiswa as $mhs)
                        @php
                            $rowScores = array_filter(
                                array_map(fn($n) => $capaian[$mhs->id][$n] ?? null, range(1, count($cpmkLines))),
                                fn($v) => $v !== null
                            );
                            $rowAvg = count($rowScores) > 0 ? round(array_sum($rowScores) / count($rowScores), 1) : null;
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-4 py-3 sticky left-0 bg-white">
                                <p class="font-medium text-slate-800 text-sm">{{ $mhs->name }}</p>
                                <p class="text-xs text-slate-400 font-mono">{{ $mhs->nip_nim ?? '—' }}</p>
                            </td>
                            @foreach($cpmkLines as $i => $line)
                            @php
                                $nomor = $i + 1;
                                $val   = $capaian[$mhs->id][$nomor] ?? null;
                                if ($val === null)       { $cls = 'bg-slate-50 text-slate-400'; $display = '—'; }
                                elseif ($val >= 70)      { $cls = 'bg-sage-50 text-sage-700 font-semibold'; $display = number_format($val, 1); }
                                elseif ($val >= 60)      { $cls = 'bg-ember-50 text-ember-700 font-semibold'; $display = number_format($val, 1); }
                                else                     { $cls = 'bg-red-50 text-red-700 font-semibold'; $display = number_format($val, 1); }
                            @endphp
                            <td class="px-3 py-3 text-center">
                                <span class="inline-block px-2 py-0.5 rounded-lg text-xs {{ $cls }}">{{ $display }}</span>
                            </td>
                            @endforeach
                            <td class="px-3 py-3 text-center">
                                @if($rowAvg !== null)
                                @php
                                    $avgCls = $rowAvg >= 70 ? 'bg-sage-100 text-sage-800' : ($rowAvg >= 60 ? 'bg-ember-100 text-ember-800' : 'bg-red-100 text-red-800');
                                @endphp
                                <span class="inline-block px-2.5 py-0.5 rounded-lg text-xs font-bold {{ $avgCls }}">{{ number_format($rowAvg, 1) }}</span>
                                @else
                                <span class="text-xs text-slate-400">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach

                        {{-- Rata-rata kelas --}}
                        <tr class="bg-slate-50 border-t-2 border-slate-200 font-medium">
                            <td class="px-4 py-3 sticky left-0 bg-slate-50">
                                <p class="text-xs font-semibold text-slate-600">Rata-rata Kelas</p>
                            </td>
                            @foreach($cpmkLines as $i => $line)
                            @php
                                $nomor = $i + 1;
                                $avg   = $avgCapaian[$nomor] ?? null;
                                if ($avg === null)       { $cls = 'text-slate-400'; $display = '—'; }
                                elseif ($avg >= 70)      { $cls = 'text-sage-700 font-bold'; $display = number_format($avg, 1); }
                                elseif ($avg >= 60)      { $cls = 'text-ember-700 font-bold'; $display = number_format($avg, 1); }
                                else                     { $cls = 'text-red-700 font-bold'; $display = number_format($avg, 1); }
                            @endphp
                            <td class="px-3 py-3 text-center text-xs {{ $cls }}">{{ $display }}</td>
                            @endforeach
                            <td class="px-3 py-3 text-center">
                                @php
                                    $allAvgs = array_filter(array_values($avgCapaian), fn($v) => $v !== null);
                                    $grandAvg = count($allAvgs) > 0 ? round(array_sum($allAvgs) / count($allAvgs), 1) : null;
                                @endphp
                                @if($grandAvg !== null)
                                <span class="text-xs font-bold {{ $grandAvg >= 70 ? 'text-sage-700' : ($grandAvg >= 60 ? 'text-ember-700' : 'text-red-700') }}">
                                    {{ number_format($grandAvg, 1) }}
                                </span>
                                @else
                                <span class="text-xs text-slate-400">—</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
    @endif

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // Tab switching
        function switchTab(tab) {
            ['mapping', 'capaian'].forEach(t => {
                document.getElementById('panel-' + t).classList.toggle('hidden', t !== tab);
                const btn = document.getElementById('tab-' + t);
                btn.classList.toggle('tab-active', t === tab);
            });
        }

        // Chart
        const chartEl = document.getElementById('chartCapaian');
        if (chartEl) {
            const labels  = @json($chartLabels);
            const data    = @json($chartData);

            new Chart(chartEl, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        label: 'Rata-rata Capaian',
                        data,
                        backgroundColor: data.map(v => v >= 70 ? 'rgba(26,183,93,0.25)' : v >= 60 ? 'rgba(249,140,5,0.25)' : 'rgba(239,68,68,0.25)'),
                        borderColor:     data.map(v => v >= 70 ? 'rgb(26,183,93)' : v >= 60 ? 'rgb(249,140,5)' : 'rgb(239,68,68)'),
                        borderWidth: 2,
                        borderRadius: 8,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: ctx => ' Rata-rata: ' + (ctx.raw || 0).toFixed(1)
                            }
                        },
                        annotation: undefined
                    },
                    scales: {
                        y: {
                            min: 0, max: 100,
                            ticks: { font: { size: 11 }, color: '#94a3b8' },
                            grid: { color: '#f1f5f9' }
                        },
                        x: {
                            ticks: { font: { size: 11, weight: '600' }, color: '#64748b' },
                            grid: { display: false }
                        }
                    }
                },
                plugins: [{
                    // target line at 70
                    id: 'targetLine',
                    afterDraw(chart) {
                        const { ctx, chartArea: { left, right }, scales: { y } } = chart;
                        const yPos = y.getPixelForValue(70);
                        ctx.save();
                        ctx.beginPath();
                        ctx.setLineDash([6, 4]);
                        ctx.strokeStyle = 'rgb(251,146,60)';
                        ctx.lineWidth = 1.5;
                        ctx.moveTo(left, yPos);
                        ctx.lineTo(right, yPos);
                        ctx.stroke();
                        ctx.setLineDash([]);
                        ctx.fillStyle = 'rgb(251,146,60)';
                        ctx.font = '10px sans-serif';
                        ctx.fillText('Target 70', right - 52, yPos - 4);
                        ctx.restore();
                    }
                }]
            });
        }
    </script>
    <style>
        .tab-active { background: white; color: #5a4fff; font-weight: 600; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
        .tab-btn:not(.tab-active) { color: #64748b; }
    </style>
    @endpush

</x-app-layout>
