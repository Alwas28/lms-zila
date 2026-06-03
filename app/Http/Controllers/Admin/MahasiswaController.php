<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MahasiswaController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::where('role', 'mahasiswa');

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(fn($qb) =>
                $qb->where('name', 'like', "%{$q}%")
                   ->orWhere('email', 'like', "%{$q}%")
                   ->orWhere('nip_nim', 'like', "%{$q}%")
            );
        }

        $mahasiswa = $query->orderBy('name')->paginate(15)->withQueryString();
        $total     = User::where('role', 'mahasiswa')->count();
        $bulanIni  = User::where('role', 'mahasiswa')
                         ->whereMonth('created_at', now()->month)
                         ->whereYear('created_at', now()->year)
                         ->count();

        return view('admin.mahasiswa.index', compact('mahasiswa', 'total', 'bulanIni'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'    => ['required', 'string', 'max:100'],
            'email'   => ['required', 'email', 'unique:users,email'],
            'nip_nim' => ['required', 'string', 'max:20', 'unique:users,nip_nim'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'nip_nim'  => $data['nip_nim'],
            'password' => Hash::make($data['password']),
            'role'     => 'mahasiswa',
        ]);

        return back()->with('success', "Mahasiswa {$data['name']} berhasil ditambahkan.");
    }

    public function update(Request $request, User $mahasiswa): RedirectResponse
    {
        abort_if($mahasiswa->role !== 'mahasiswa', 403);

        $data = $request->validate([
            'name'    => ['required', 'string', 'max:100'],
            'email'   => ['required', 'email', Rule::unique('users', 'email')->ignore($mahasiswa->id)],
            'nip_nim' => ['required', 'string', 'max:20', Rule::unique('users', 'nip_nim')->ignore($mahasiswa->id)],
        ]);

        $mahasiswa->update($data);

        return back()->with('success', "Data mahasiswa {$mahasiswa->name} berhasil diperbarui.");
    }

    public function destroy(User $mahasiswa): RedirectResponse
    {
        abort_if($mahasiswa->role !== 'mahasiswa', 403);

        $nama = $mahasiswa->name;
        $mahasiswa->delete();

        return back()->with('success', "Mahasiswa {$nama} berhasil dihapus.");
    }

    public function resetPassword(User $mahasiswa): RedirectResponse
    {
        abort_if($mahasiswa->role !== 'mahasiswa', 403);

        $mahasiswa->update(['password' => Hash::make('password')]);

        return back()->with('success', "Password {$mahasiswa->name} berhasil direset ke 'password'.");
    }
}
