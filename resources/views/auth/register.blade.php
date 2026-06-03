<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Mahasiswa — LMS Zila</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: { extend: {
                fontFamily: { heading: ['Sora','sans-serif'] },
                colors: {
                    ink:  { 50:'#f0f0ff',100:'#e3e1ff',200:'#cac7ff',300:'#a9a4ff',500:'#5a4fff',600:'#4a3bef',700:'#3d2dd4',900:'#1e1870' },
                    sage: { 50:'#effdf4',400:'#43d47a',500:'#1ab75d',600:'#0f964e' },
                }
            }}
        }
    </script>
    <style>
        body { font-family: 'DM Sans', sans-serif; }
        .font-heading { font-family: 'Sora', sans-serif; }
        .side-bg { background: linear-gradient(160deg, #0d7441 0%, #1ab75d 55%, #43d47a 100%); }
        .blob { position:absolute; border-radius:50%; filter:blur(80px); opacity:.2; pointer-events:none; }
        @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-10px)} }
        .float { animation: float 5s ease-in-out infinite; }
    </style>
</head>
<body class="min-h-screen flex antialiased bg-slate-50">

{{-- Left decorative panel --}}
<div class="hidden lg:flex lg:w-5/12 side-bg relative overflow-hidden flex-col items-center justify-center p-12">
    <div class="blob w-80 h-80 bg-emerald-600 -top-10 -left-10"></div>
    <div class="blob w-64 h-64 bg-teal-300  bottom-10 right-10"></div>

    <div class="relative z-10 text-center max-w-sm">
        <div class="inline-flex items-center gap-3 mb-10">
            <div class="w-12 h-12 rounded-2xl bg-white/15 backdrop-blur-sm border border-white/20 flex items-center justify-center shadow-xl">
                <i class="fa-solid fa-graduation-cap text-white text-xl"></i>
            </div>
            <span class="font-heading font-bold text-white text-2xl">LMS Zila</span>
        </div>

        <div class="float bg-white/10 backdrop-blur-md border border-white/20 rounded-3xl p-6 mb-8 shadow-2xl text-left space-y-3.5">
            <p class="font-heading font-semibold text-white text-sm mb-1">3 Langkah Mudah</p>
            @foreach([
                ['1','Isi data diri','Nama, NIM, dan email'],
                ['2','Buat password','Minimal 8 karakter'],
                ['3','Mulai belajar','Akses semua fitur LMS'],
            ] as [$n,$t,$s])
            <div class="flex items-center gap-3">
                <div class="w-7 h-7 rounded-lg bg-white/20 flex items-center justify-center text-white font-heading font-bold text-xs flex-shrink-0">{{ $n }}</div>
                <div>
                    <p class="text-white font-medium text-xs">{{ $t }}</p>
                    <p class="text-white/50 text-xs">{{ $s }}</p>
                </div>
            </div>
            @endforeach
        </div>

        <p class="text-white/60 text-sm leading-relaxed">
            Daftar sebagai mahasiswa dan mulai perjalanan belajar digital bersama UM Kendari.
        </p>
    </div>
</div>

{{-- Right form panel --}}
<div class="flex-1 flex flex-col items-center justify-center p-6 sm:p-10 overflow-y-auto">
    <div class="lg:hidden flex items-center gap-2.5 mb-8">
        <div class="w-9 h-9 rounded-xl bg-ink-500 flex items-center justify-center shadow">
            <i class="fa-solid fa-graduation-cap text-white"></i>
        </div>
        <span class="font-heading font-bold text-slate-800 text-xl">LMS <span class="text-ink-500">Zila</span></span>
    </div>

    <div class="w-full max-w-md">
        <div class="mb-7">
            <span class="inline-block text-xs font-semibold text-sage-600 bg-sage-50 px-2.5 py-1 rounded-full mb-3">Pendaftaran Mahasiswa</span>
            <h1 class="font-heading font-bold text-2xl md:text-3xl text-slate-800 mb-1.5">Buat Akun Baru</h1>
            <p class="text-slate-400 text-sm">Lengkapi data diri untuk mendaftar sebagai mahasiswa</p>
        </div>

        @if ($errors->any())
        <div class="mb-5 bg-red-50 border border-red-200 text-red-600 text-sm rounded-xl px-4 py-3">
            <i class="fa-solid fa-circle-exclamation mr-1.5"></i>{{ $errors->first() }}
        </div>
        @endif

        @php
            $base = 'w-full py-3 rounded-xl border text-sm focus:outline-none focus:ring-2 focus:ring-ink-200 focus:border-ink-300 transition-colors';
            $ok   = 'border-slate-200 bg-white';
            $err  = 'border-red-300 bg-red-50';
        @endphp

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            {{-- Nama --}}
            <div>
                <label for="name" class="block text-xs font-semibold text-slate-600 mb-1.5">Nama Lengkap</label>
                <div class="relative">
                    <i class="fa-solid fa-user absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                        placeholder="Nama sesuai KTP"
                        class="{{ $base }} pl-10 pr-4 {{ $errors->has('name') ? $err : $ok }}">
                </div>
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- NIM --}}
            <div>
                <label for="nip_nim" class="block text-xs font-semibold text-slate-600 mb-1.5">NIM</label>
                <div class="relative">
                    <i class="fa-solid fa-id-card absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input id="nip_nim" type="text" name="nip_nim" value="{{ old('nip_nim') }}" required
                        placeholder="Contoh: 2023110001"
                        class="{{ $base }} pl-10 pr-4 font-mono tracking-wider {{ $errors->has('nip_nim') ? $err : $ok }}">
                </div>
                @error('nip_nim')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-xs font-semibold text-slate-600 mb-1.5">Email</label>
                <div class="relative">
                    <i class="fa-solid fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                        placeholder="email@mahasiswa.ac.id"
                        class="{{ $base }} pl-10 pr-4 {{ $errors->has('email') ? $err : $ok }}">
                </div>
                @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="block text-xs font-semibold text-slate-600 mb-1.5">Password</label>
                <div class="relative">
                    <i class="fa-solid fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input id="password" type="password" name="password" required autocomplete="new-password"
                        placeholder="Minimal 8 karakter"
                        class="{{ $base }} pl-10 pr-11 {{ $errors->has('password') ? $err : $ok }}">
                    <button type="button" onclick="togglePwd('password','eye1')"
                        class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                        <i id="eye1" class="fa-solid fa-eye text-sm"></i>
                    </button>
                </div>
                @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Konfirmasi Password --}}
            <div>
                <label for="password_confirmation" class="block text-xs font-semibold text-slate-600 mb-1.5">Konfirmasi Password</label>
                <div class="relative">
                    <i class="fa-solid fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                        placeholder="Ulangi password"
                        class="{{ $base }} pl-10 pr-11 {{ $errors->has('password_confirmation') ? $err : $ok }}">
                    <button type="button" onclick="togglePwd('password_confirmation','eye2')"
                        class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                        <i id="eye2" class="fa-solid fa-eye text-sm"></i>
                    </button>
                </div>
            </div>

            {{-- Info role --}}
            <div class="flex items-start gap-2.5 bg-ink-50 border border-ink-100 rounded-xl px-3.5 py-3">
                <i class="fa-solid fa-circle-info text-ink-400 text-sm mt-0.5 flex-shrink-0"></i>
                <p class="text-ink-700 text-xs leading-relaxed">
                    Akun ini terdaftar sebagai <strong>Mahasiswa</strong>.
                    Akun Dosen dibuat oleh Administrator.
                </p>
            </div>

            <button type="submit"
                class="w-full py-3.5 rounded-xl bg-ink-500 hover:bg-ink-600 text-white font-heading font-semibold text-sm transition-colors shadow-lg shadow-ink-500/25">
                <i class="fa-solid fa-user-plus mr-2 text-xs"></i>Daftar Sekarang
            </button>
        </form>

        <p class="text-center text-sm text-slate-500 mt-5">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="text-ink-500 hover:text-ink-700 font-semibold">Masuk di sini</a>
        </p>
        <div class="mt-5 pt-5 border-t border-slate-100 text-center">
            <a href="/" class="text-xs text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-arrow-left text-xs mr-1"></i> Kembali ke Beranda
            </a>
        </div>
    </div>
</div>

<script>
    function togglePwd(id, iconId) {
        const el = document.getElementById(id);
        const ic = document.getElementById(iconId);
        el.type = el.type === 'password' ? 'text' : 'password';
        ic.className = el.type === 'text' ? 'fa-solid fa-eye-slash text-sm' : 'fa-solid fa-eye text-sm';
    }
</script>
</body>
</html>
