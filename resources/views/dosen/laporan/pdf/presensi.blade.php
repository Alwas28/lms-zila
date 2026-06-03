<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; color: #1e293b; background: #fff; }
    .header { border-bottom: 2px solid #1ab75d; padding-bottom: 10px; margin-bottom: 14px; }
    .header h1 { font-size: 14px; font-weight: 700; color: #1ab75d; margin-bottom: 2px; }
    .header .meta { font-size: 9px; color: #64748b; }
    .meta-grid { display: flex; gap: 24px; margin-bottom: 14px; }
    .meta-item label { font-size: 8px; color: #94a3b8; text-transform: uppercase; display: block; }
    .meta-item span { font-size: 10px; font-weight: 600; color: #334155; }
    table { width: 100%; border-collapse: collapse; }
    thead tr { background: #1ab75d; color: white; }
    thead th { padding: 6px 5px; font-size: 8px; font-weight: 600; text-align: center; }
    thead th.left { text-align: left; }
    tbody tr:nth-child(even) { background: #f8fafc; }
    tbody tr.avg-row { background: #ecfdf5; border-top: 1.5px solid #1ab75d; }
    tbody td { padding: 5px 5px; font-size: 8px; border-bottom: 1px solid #f1f5f9; text-align: center; }
    tbody td.left { text-align: left; }
    .h { color: #059669; font-weight: 700; }
    .i { color: #d97706; font-weight: 700; }
    .s { color: #2563eb; font-weight: 700; }
    .a { color: #dc2626; font-weight: 700; }
    .pct-high { color: #059669; font-weight: 700; }
    .pct-low  { color: #dc2626; font-weight: 700; }
    .legend { font-size: 8px; color: #64748b; margin-bottom: 10px; display: flex; gap: 16px; }
    .footer { margin-top: 14px; font-size: 8px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 8px; display: flex; justify-content: space-between; }
</style>
</head>
<body>
<div class="header">
    <h1>Laporan Presensi Mahasiswa</h1>
    <div class="meta">Dicetak pada {{ now()->isoFormat('D MMMM YYYY, HH:mm') }} WIB</div>
</div>

<div class="meta-grid">
    <div class="meta-item"><label>Mata Kuliah</label><span>{{ $kelas->mataKuliah->nama }}</span></div>
    <div class="meta-item"><label>Kode MK</label><span>{{ $kelas->mataKuliah->kode }}</span></div>
    <div class="meta-item"><label>Kelas</label><span>{{ $kelas->nama_kelas }}</span></div>
    <div class="meta-item"><label>Semester</label><span>{{ ucfirst($kelas->semester->tipe) }} {{ $kelas->semester->tahunAkademik->nama }}</span></div>
    <div class="meta-item"><label>Total Pertemuan</label><span>{{ $pertemuan->count() }}</span></div>
</div>

<div class="legend">
    <span class="h">H = Hadir</span>
    <span class="i">I = Izin</span>
    <span class="s">S = Sakit</span>
    <span class="a">A = Alpha</span>
    <span>— = Tidak ada sesi</span>
</div>

<table>
    <thead>
        <tr>
            <th class="left" style="width:20px">No</th>
            <th class="left" style="width:110px">Nama</th>
            <th class="left" style="width:62px">NIM</th>
            @foreach($pertemuan as $p)
            <th style="width:16px">{{ $p->nomor }}</th>
            @endforeach
            <th style="width:22px">H</th>
            <th style="width:22px">I</th>
            <th style="width:22px">S</th>
            <th style="width:22px">A</th>
            <th style="width:32px">%Hadir</th>
        </tr>
    </thead>
    <tbody>
        @foreach($mahasiswa as $idx => $mhs)
        @php
            $rekap = $rekapMhs[$mhs->id] ?? ['hadir'=>0,'izin'=>0,'sakit'=>0,'alpha'=>0];
            $total = array_sum($rekap);
            $pct   = $total > 0 ? round($rekap['hadir'] / $total * 100) : 0;
        @endphp
        <tr>
            <td>{{ $idx+1 }}</td>
            <td class="left">{{ $mhs->name }}</td>
            <td class="left">{{ $mhs->nip_nim ?? '-' }}</td>
            @foreach($pertemuan as $p)
            @php $st = $presensiMap[$mhs->id][$p->id] ?? null; @endphp
            <td class="{{ $st === 'hadir' ? 'h' : ($st === 'izin' ? 'i' : ($st === 'sakit' ? 's' : ($st === 'alpha' ? 'a' : ''))) }}">
                {{ $st ? strtoupper(substr($st,0,1)) : '—' }}
            </td>
            @endforeach
            <td class="h">{{ $rekap['hadir'] }}</td>
            <td class="i">{{ $rekap['izin'] }}</td>
            <td class="s">{{ $rekap['sakit'] }}</td>
            <td class="a">{{ $rekap['alpha'] }}</td>
            <td class="{{ $pct >= 75 ? 'pct-high' : 'pct-low' }}">{{ $pct }}%</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">
    <span>LMS Zila &mdash; {{ $kelas->mataKuliah->nama }} Kelas {{ $kelas->nama_kelas }}</span>
    <span>Total mahasiswa: {{ $mahasiswa->count() }}</span>
</div>
</body>
</html>
