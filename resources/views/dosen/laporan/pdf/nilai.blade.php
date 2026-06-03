<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #1e293b; background: #fff; }
    .header { border-bottom: 2px solid #5a4fff; padding-bottom: 10px; margin-bottom: 14px; }
    .header h1 { font-size: 14px; font-weight: 700; color: #5a4fff; margin-bottom: 2px; }
    .header .meta { font-size: 9px; color: #64748b; }
    .meta-grid { display: flex; gap: 24px; margin-bottom: 14px; }
    .meta-item label { font-size: 8px; color: #94a3b8; text-transform: uppercase; letter-spacing: .05em; display: block; }
    .meta-item span { font-size: 10px; font-weight: 600; color: #334155; }
    table { width: 100%; border-collapse: collapse; }
    thead tr { background: #5a4fff; color: white; }
    thead th { padding: 7px 8px; text-align: left; font-size: 9px; font-weight: 600; }
    thead th.center { text-align: center; }
    tbody tr:nth-child(even) { background: #f8fafc; }
    tbody tr:last-child.avg { background: #eef2ff; border-top: 1.5px solid #5a4fff; }
    tbody td { padding: 6px 8px; font-size: 9px; border-bottom: 1px solid #f1f5f9; }
    tbody td.center { text-align: center; }
    .badge { display: inline-block; padding: 2px 7px; border-radius: 10px; font-weight: 700; font-size: 9px; }
    .grade-a { background: #d1fae5; color: #065f46; }
    .grade-b { background: #dbeafe; color: #1e40af; }
    .grade-c { background: #fef9c3; color: #713f12; }
    .grade-d { background: #ffe4e6; color: #9f1239; }
    .grade-e { background: #f1f5f9; color: #475569; }
    .footer { margin-top: 16px; font-size: 8px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 8px; display: flex; justify-content: space-between; }
    .high { color: #059669; font-weight: 700; }
    .med  { color: #d97706; font-weight: 700; }
    .low  { color: #dc2626; font-weight: 700; }
</style>
</head>
<body>
<div class="header">
    <h1>Laporan Nilai Mahasiswa</h1>
    <div class="meta">Dicetak pada {{ now()->isoFormat('D MMMM YYYY, HH:mm') }} WIB</div>
</div>

<div class="meta-grid">
    <div class="meta-item"><label>Mata Kuliah</label><span>{{ $kelas->mataKuliah->nama }}</span></div>
    <div class="meta-item"><label>Kode MK</label><span>{{ $kelas->mataKuliah->kode }}</span></div>
    <div class="meta-item"><label>Kelas</label><span>{{ $kelas->nama_kelas }}</span></div>
    <div class="meta-item"><label>SKS</label><span>{{ $kelas->mataKuliah->sks }}</span></div>
    <div class="meta-item"><label>Semester</label><span>{{ ucfirst($kelas->semester->tipe) }} {{ $kelas->semester->tahunAkademik->nama }}</span></div>
</div>

@if($config)
<div style="font-size:8px;color:#64748b;margin-bottom:10px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;padding:6px 10px;">
    Bobot: Kehadiran {{ $config->bobot_kehadiran }}% &nbsp;|&nbsp;
    Tugas {{ $config->bobot_tugas }}% &nbsp;|&nbsp;
    Quiz {{ $config->bobot_quiz }}% &nbsp;|&nbsp;
    UTS {{ $config->bobot_uts }}% &nbsp;|&nbsp;
    UAS {{ $config->bobot_uas }}%
</div>
@endif

<table>
    <thead>
        <tr>
            <th style="width:22px">No</th>
            <th>Nama Mahasiswa</th>
            <th style="width:70px">NIM</th>
            <th class="center" style="width:48px">Kehadiran</th>
            <th class="center" style="width:40px">Tugas</th>
            <th class="center" style="width:40px">Quiz</th>
            <th class="center" style="width:40px">UTS</th>
            <th class="center" style="width:40px">UAS</th>
            <th class="center" style="width:52px">Nilai Akhir</th>
            <th class="center" style="width:36px">Grade</th>
        </tr>
    </thead>
    <tbody>
        @php $sumNilai = 0; $countNilai = 0; @endphp
        @foreach($mahasiswa as $i => $mhs)
        @php
            $p = $penilaian[$mhs->id] ?? null;
            if ($p && $p->nilai_akhir !== null) { $sumNilai += $p->nilai_akhir; $countNilai++; }
            $na = $p?->nilai_akhir;
            $naClass = $na === null ? '' : ($na >= 70 ? 'high' : ($na >= 60 ? 'med' : 'low'));
            $grade = $p?->grade ?? '-';
            $gradeClass = match(substr($grade,0,1)) {
                'A' => 'grade-a', 'B' => 'grade-b', 'C' => 'grade-c',
                'D' => 'grade-d', default => 'grade-e'
            };
        @endphp
        <tr>
            <td class="center">{{ $i+1 }}</td>
            <td>{{ $mhs->name }}</td>
            <td class="center">{{ $mhs->nip_nim ?? '-' }}</td>
            <td class="center">{{ $p?->nilai_kehadiran !== null ? number_format($p->nilai_kehadiran,1) : '-' }}</td>
            <td class="center">{{ $p?->nilai_tugas !== null ? number_format($p->nilai_tugas,1) : '-' }}</td>
            <td class="center">{{ $p?->nilai_quiz !== null ? number_format($p->nilai_quiz,1) : '-' }}</td>
            <td class="center">{{ $p?->nilai_uts !== null ? number_format($p->nilai_uts,1) : '-' }}</td>
            <td class="center">{{ $p?->nilai_uas !== null ? number_format($p->nilai_uas,1) : '-' }}</td>
            <td class="center {{ $naClass }}">{{ $na !== null ? number_format($na,1) : '-' }}</td>
            <td class="center"><span class="badge {{ $gradeClass }}">{{ $grade }}</span></td>
        </tr>
        @endforeach
        @if($countNilai > 0)
        <tr class="avg">
            <td></td>
            <td style="font-weight:700;font-size:9px;color:#5a4fff">Rata-rata Kelas</td>
            <td></td>
            <td></td><td></td><td></td><td></td><td></td>
            <td class="center" style="font-weight:700;color:#5a4fff">{{ number_format($sumNilai/$countNilai,1) }}</td>
            <td></td>
        </tr>
        @endif
    </tbody>
</table>

<div class="footer">
    <span>LMS Zila &mdash; {{ $kelas->mataKuliah->nama }} Kelas {{ $kelas->nama_kelas }}</span>
    <span>Total mahasiswa: {{ $mahasiswa->count() }}</span>
</div>
</body>
</html>
