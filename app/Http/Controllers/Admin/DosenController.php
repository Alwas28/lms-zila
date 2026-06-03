<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DosenController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::where('role', 'dosen');

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(fn($qb) =>
                $qb->where('name', 'like', "%{$q}%")
                   ->orWhere('email', 'like', "%{$q}%")
                   ->orWhere('nip_nim', 'like', "%{$q}%")
            );
        }

        $dosen  = $query->orderBy('name')->paginate(15)->withQueryString();
        $total  = User::where('role', 'dosen')->count();
        $bulanIni = User::where('role', 'dosen')
                        ->whereMonth('created_at', now()->month)
                        ->whereYear('created_at', now()->year)
                        ->count();

        return view('admin.dosen.index', compact('dosen', 'total', 'bulanIni'));
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
            'role'     => 'dosen',
        ]);

        return back()->with('success', "Dosen {$data['name']} berhasil ditambahkan.");
    }

    public function update(Request $request, User $dosen): RedirectResponse
    {
        abort_if($dosen->role !== 'dosen', 403);

        $data = $request->validate([
            'name'    => ['required', 'string', 'max:100'],
            'email'   => ['required', 'email', Rule::unique('users', 'email')->ignore($dosen->id)],
            'nip_nim' => ['required', 'string', 'max:20', Rule::unique('users', 'nip_nim')->ignore($dosen->id)],
        ]);

        $dosen->update($data);

        return back()->with('success', "Data dosen {$dosen->name} berhasil diperbarui.");
    }

    public function destroy(User $dosen): RedirectResponse
    {
        abort_if($dosen->role !== 'dosen', 403);

        $nama = $dosen->name;
        $dosen->delete();

        return back()->with('success', "Dosen {$nama} berhasil dihapus.");
    }

    public function resetPassword(User $dosen): RedirectResponse
    {
        abort_if($dosen->role !== 'dosen', 403);

        $dosen->update(['password' => Hash::make('password')]);

        return back()->with('success', "Password {$dosen->name} berhasil direset ke 'password'.");
    }
}
