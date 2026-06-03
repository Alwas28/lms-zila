<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Pengampu;
use App\Models\Rps;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RpsController extends Controller
{
    private function authorizeKelas(Kelas $kelas): void
    {
        abort_if(
            !Pengampu::where('kelas_id', $kelas->id)->where('user_id', auth()->id())->exists(),
            403, 'Anda tidak memiliki akses ke kelas ini.'
        );
    }

    public function show(Kelas $kelas)
    {
        $this->authorizeKelas($kelas);

        $rps = Rps::firstOrNew(['kelas_id' => $kelas->id]);

        $kelas->load('mataKuliah', 'semester.tahunAkademik');

        return view('dosen.rps.show', compact('kelas', 'rps'));
    }

    public function update(Request $request, Kelas $kelas)
    {
        $this->authorizeKelas($kelas);

        $data = $request->validate([
            'deskripsi_mk'       => 'nullable|string',
            'cpl'                => 'nullable|string',
            'cpmk'               => 'nullable|string',
            'sub_cpmk'           => 'nullable|string',
            'metode_pembelajaran'=> 'nullable|string',
            'referensi'          => 'nullable|string',
            'pdf'                => 'nullable|file|mimes:pdf|max:10240',
        ]);

        $rps = Rps::firstOrNew(['kelas_id' => $kelas->id]);
        $rps->kelas_id = $kelas->id;

        if ($request->hasFile('pdf')) {
            if ($rps->pdf_path) {
                Storage::disk('public')->delete($rps->pdf_path);
            }
            $rps->pdf_path = $request->file('pdf')->store("rps/{$kelas->id}", 'public');
        }

        $rps->fill([
            'deskripsi_mk'        => $data['deskripsi_mk'] ?? null,
            'cpl'                 => $data['cpl'] ?? null,
            'cpmk'                => $data['cpmk'] ?? null,
            'sub_cpmk'            => $data['sub_cpmk'] ?? null,
            'metode_pembelajaran' => $data['metode_pembelajaran'] ?? null,
            'referensi'           => $data['referensi'] ?? null,
        ]);

        $rps->save();

        return back()->with('success', 'RPS berhasil disimpan.');
    }
}
