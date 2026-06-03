<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; color: #1e293b; background: #fff; }
    .header { border-bottom: 2px solid #f98c05; padding-bottom: 10px; margin-bottom: 14px; }
    .header h1 { font-size: 14px; font-weight: 700; color: #f98c05; margin-bottom: 2px; }
    .header .meta { font-size: 9px; color: #64748b; }
    .meta-grid { display: flex; gap: 24px; margin-bottom: 12px; }
    .meta-item label { font-size: 8px; color: #94a3b8; text-transform: uppercase; display: block; }
    .meta-item span { font-size: 10px; font-weight: 600; color: #334155; }
    .cpmk-list { margin-bottom: 12px; background: #fff7ed; border: 1px solid #fed7aa; border-radius: 6px; padding: 8px 10px; }
    .cpmk-list h4 { font-size: 8px; font-weight: 700; color: #92400e; text-transform: uppercase; margin-bottom: 6px; }
    .cpmk-item { font-size: 8px; color: #451a03; margin-bottom: 2px; }
    .cpmk-item strong { color: #f98c05; }
    table { width: 100%; border-collapse: collapse; }
    thead tr { background: #f98c05; color: white; }
    thead th { padding: 6px 6px; font-size: 8px; font-weight: 600; text-align: center; }
    thead th.left { text-align: left; }
    tbody tr:nth-child(even) { background: #f8fafc; }
    tbody tr.avg-row { background: #fff7ed; border-top: 1.5px solid #f98c05; }
    tbody td { padding: 5px 6px; font-size: 8px; border-bottom: 1px solid #f1f5f9; text-align: center; }
    tbody td.left { text-align: left; }
    .high { color: #059669; font-weight: 700; }
    .med  { color: #d97706; font-weight: 700; }
    .low  { color: #dc2626; font-weight: 700; }
    .footer { margin-top: 14px; font-size: 8px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 8px; display: flex; justify-content: space-between; }
    .komponen-tag { display: inline-block; background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; border-radius: 4px; padding: 1px 5px; font-size: 7px; margin: 1px; }
</style>
</head>
<body>
<div class="header">
    <h1>Laporan Capaian CPMK</h1>
    <div class="meta">Dicetak pada {{ now()->isoFormat('D MMMM YYYY, HH:mm') }} WIB</div>
</div>

<div class="meta-grid">
    <div class="meta-item"><label>Mata Kuliah</label><span>{{ $kelas->mataKuliah->nama }}</span></div>
    <div class="meta-item"><label>Kode MK</label><span>{{ $kelas->mataKuliah->kode }}</span></div>
    <div class="meta-item"><label>Kelas</label><span>{{ $kelas->nama_kelas }}</span></div>
    <div class="meta-item"><label>Semester</label><span>{{ ucfirst($kelas->semester->tipe) }} {{ $kelas->semester->tahunAkademik->nama }}</span></div>
</div>

@if(!empty($cpmkLines))
<div class="cpmk-list">
    <h4>Daftar CPMK &amp; Mapping Komponen</h4>
    @foreach($cpmkLines as $i => $line)
    @php $nomor = $i+1; $mapped = $mappings[$nomor] ?? []; @endphp
    <div class="cpmk-item">
        <strong>CPMK-{{ $nomor }}:</strong> {{ $line }}
        @if(count($mapped) > 0)
        &nbsp;
        @foreach($mapped as $k)
        <span class="komponen-tag">{{ strtoupper($k) }}</span>
        @endforeach
        @else
        <span style="color:#94a3b8;font-size:7px">(belum di-mapping)</span>
        @endif
    </div>
    @endforeach
</div>
@endif

<table>
    <thead>
        <tr>
            <th class="left" style="width:20px">No</th>
            <th class="left" style="width:120px">Nama</th>
            <th class="left" style="width:65px">NIM</th>
            @foreach($cpmkLines as $i => $line)
            <th style="min-width:50px">CPMK-{{ $i+1 }}</th>
            @endforeach
            <th style="width:50px">Rata-rata</th>
        </tr>
    </thead>
    <tbody>
        @foreach($mahasiswa as $idx => $mhs)
        @php
            $rowScores = array_filter(
                array_map(fn($n) => $capaian[$mhs->id][$n] ?? null, range(1, count($cpmkLines))),
                fn($v) => $v !== null
            );
            $rowAvg = count($rowScores) > 0 ? round(array_sum($rowScores)/count($rowScores),1) : null;
        @endphp
        <tr>
            <td>{{ $idx+1 }}</td>
            <td class="left">{{ $mhs->name }}</td>
            <td class="left">{{ $mhs->nip_nim ?? '-' }}</td>
            @foreach($cpmkLines as $i => $line)
            @php
                $val = $capaian[$mhs->id][$i+1] ?? null;
                $cls = $val === null ? '' : ($val >= 70 ? 'high' : ($val >= 60 ? 'med' : 'low'));
            @endphp
            <td class="{{ $cls }}">{{ $val !== null ? number_format($val,1) : '—' }}</td>
            @endforeach
            @php $avgCls = $rowAvg === null ? '' : ($rowAvg >= 70 ? 'high' : ($rowAvg >= 60 ? 'med' : 'low')); @endphp
            <td class="{{ $avgCls }}">{{ $rowAvg !== null ? number_format($rowAvg,1) : '—' }}</td>
        </tr>
        @endforeach

        {{-- Rata-rata kelas --}}
        <tr class="avg-row">
            <td></td>
            <td class="left" style="font-weight:700;color:#f98c05">Rata-rata Kelas</td>
            <td></td>
            @foreach($cpmkLines as $i => $line)
            @php
                $avg = $avgCapaian[$i+1] ?? null;
                $cls = $avg === null ? '' : ($avg >= 70 ? 'high' : ($avg >= 60 ? 'med' : 'low'));
            @endphp
            <td class="{{ $cls }}">{{ $avg !== null ? number_format($avg,1) : '—' }}</td>
            @endforeach
            @php
                $allAvgs = array_filter(array_values($avgCapaian), fn($v) => $v !== null);
                $grandAvg = count($allAvgs) > 0 ? round(array_sum($allAvgs)/count($allAvgs),1) : null;
                $grandCls = $grandAvg === null ? '' : ($grandAvg >= 70 ? 'high' : ($grandAvg >= 60 ? 'med' : 'low'));
            @endphp
            <td class="{{ $grandCls }}">{{ $grandAvg !== null ? number_format($grandAvg,1) : '—' }}</td>
        </tr>
    </tbody>
</table>

<div class="footer">
    <span>LMS Zila &mdash; {{ $kelas->mataKuliah->nama }} Kelas {{ $kelas->nama_kelas }}</span>
    <span>Target capaian CPMK &ge; 70</span>
</div>
</body>
</html>
