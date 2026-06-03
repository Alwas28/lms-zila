<x-app-layout>
    <x-slot name="title">Capaian CPMK — {{ $kelas->mataKuliah->nama }}</x-slot>
    <x-slot name="pageTitle">Capaian CPMK</x-slot>

    <div class="mb-6">
        <nav class="flex items-center gap-1.5 text-xs text-slate-400 mb-1">
            <a href="{{ route('mahasiswa.kelas.index') }}" class="hover:text-ink-500">Mata Kuliah Saya</a>
            <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
            <span class="text-slate-600">{{ $kelas->mataKuliah->nama }}</span>
            <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
            <span class="text-ink-500 font-medium">Capaian CPMK</span>
        </nav>
        <h2 class="font-heading font-semibold text-slate-800 text-lg">
            {{ $kelas->mataKuliah->nama }}
            <span class="text-slate-400 font-normal text-sm">— Kelas {{ $kelas->nama_kelas }}</span>
        </h2>
    </div>

    @if(empty($cpmkLines))
    <div class="bg-ember-50 border border-ember-200 rounded-2xl p-5 flex items-start gap-3">
        <i class="fa-solid fa-triangle-exclamation text-ember-500 mt-0.5"></i>
        <p class="text-sm text-ember-700">CPMK belum diisi oleh dosen di RPS.</p>
    </div>
    @else
    {{-- Chart --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-5 mb-5">
        <h3 class="font-heading font-semibold text-slate-800 text-sm mb-4">
            <i class="fa-solid fa-chart-bar text-ink-500 mr-1.5"></i>Grafik Capaian CPMK Anda
        </h3>
        <div class="relative h-52">
            <canvas id="chartCapaian"></canvas>
        </div>
    </div>

    {{-- Detail per CPMK --}}
    <div class="space-y-3">
        @foreach($cpmkLines as $i => $line)
        @php
            $nomor = $i + 1;
            $val   = $capaian[$nomor] ?? null;
            $mapped = $mappings[$nomor] ?? [];
            $cls = $val === null ? 'border-slate-200' : ($val >= 70 ? 'border-sage-200' : ($val >= 60 ? 'border-ember-200' : 'border-red-200'));
        @endphp
        <div class="bg-white rounded-2xl border {{ $cls }} p-5">
            <div class="flex items-start justify-between gap-3 mb-2">
                <div class="flex-1">
                    <span class="text-xs font-semibold text-ink-600 bg-ink-50 px-2 py-0.5 rounded-full">CPMK-{{ $nomor }}</span>
                    <p class="text-sm text-slate-700 mt-1.5 leading-snug">{{ $line }}</p>
                </div>
                <div class="text-right flex-shrink-0">
                    @if($val !== null)
                    <p class="text-2xl font-heading font-black {{ $val >= 70 ? 'text-sage-600' : ($val >= 60 ? 'text-ember-600' : 'text-red-600') }}">
                        {{ number_format($val, 1) }}
                    </p>
                    <p class="text-xs {{ $val >= 70 ? 'text-sage-500' : ($val >= 60 ? 'text-ember-500' : 'text-red-500') }}">
                        {{ $val >= 70 ? 'Memuaskan' : ($val >= 60 ? 'Cukup' : 'Kurang') }}
                    </p>
                    @else
                    <p class="text-lg font-heading font-bold text-slate-300">—</p>
                    <p class="text-xs text-slate-400">Belum ada data</p>
                    @endif
                </div>
            </div>
            @if(count($mapped) > 0)
            <div class="flex items-center gap-1.5 flex-wrap mt-2">
                <span class="text-xs text-slate-400">Dari:</span>
                @foreach($mapped as $k)
                <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full">{{ strtoupper($k) }}</span>
                @endforeach
            </div>
            @endif
            @if($val !== null)
            <div class="mt-3 h-2 bg-slate-100 rounded-full overflow-hidden">
                <div class="h-2 rounded-full {{ $val >= 70 ? 'bg-sage-500' : ($val >= 60 ? 'bg-ember-500' : 'bg-red-500') }}"
                    style="width: {{ min(100, $val) }}%"></div>
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @endif

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        const chartEl = document.getElementById('chartCapaian');
        if (chartEl) {
            const labels = @json($chartLabels);
            const data   = @json($chartData);
            new Chart(chartEl, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        label: 'Capaian Anda',
                        data,
                        backgroundColor: data.map(v => v >= 70 ? 'rgba(26,183,93,.25)' : v >= 60 ? 'rgba(249,140,5,.25)' : 'rgba(239,68,68,.25)'),
                        borderColor:     data.map(v => v >= 70 ? 'rgb(26,183,93)' : v >= 60 ? 'rgb(249,140,5)' : 'rgb(239,68,68)'),
                        borderWidth: 2, borderRadius: 8,
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { min: 0, max: 100, ticks: { font: { size: 11 }, color: '#94a3b8' }, grid: { color: '#f1f5f9' } },
                        x: { ticks: { font: { size: 11, weight: '600' }, color: '#64748b' }, grid: { display: false } }
                    }
                },
                plugins: [{ id: 'target70', afterDraw(c) {
                    const { ctx, chartArea: { left, right }, scales: { y } } = c;
                    const yp = y.getPixelForValue(70);
                    ctx.save(); ctx.beginPath(); ctx.setLineDash([6,4]);
                    ctx.strokeStyle = 'rgb(251,146,60)'; ctx.lineWidth = 1.5;
                    ctx.moveTo(left, yp); ctx.lineTo(right, yp); ctx.stroke();
                    ctx.fillStyle = 'rgb(251,146,60)'; ctx.font = '10px sans-serif';
                    ctx.fillText('Target 70', right - 52, yp - 4); ctx.restore();
                }}]
            });
        }
    </script>
    @endpush
</x-app-layout>
