<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use App\Models\TahunAkademik;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SemesterController extends Controller
{
    public function update(Request $request, Semester $semester): RedirectResponse
    {
        $request->validate([
            'tanggal_mulai'   => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
        ]);

        $semester->update($request->only('tanggal_mulai', 'tanggal_selesai'));

        return back()->with('success', 'Tanggal semester berhasil diperbarui.');
    }

    public function aktifkan(Semester $semester): RedirectResponse
    {
        $semester->aktifkan();

        $label = $semester->tipe_human . ' ' . $semester->tahunAkademik->nama;

        return back()->with('success', "Semester {$label} kini menjadi semester aktif.");
    }

    public function nonaktifkan(Semester $semester): RedirectResponse
    {
        $semester->update(['is_aktif' => false]);

        return back()->with('success', 'Semester berhasil dinonaktifkan.');
    }
}
