<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — LMS Zila</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: { extend: {
                fontFamily: { heading: ['Sora','sans-serif'], body: ['DM Sans','sans-serif'] },
                colors: {
                    ink:  { 50:'#f0f0ff',100:'#e3e1ff',200:'#cac7ff',300:'#a9a4ff',400:'#8078ff',500:'#5a4fff',600:'#4a3bef',700:'#3d2dd4',800:'#3327ab',900:'#1e1870' },
                    sage: { 50:'#effdf4',400:'#43d47a',500:'#1ab75d',600:'#0f964e' },
                    ember:{ 50:'#fff8ec',500:'#f98c05',600:'#dc6c02' },
                }
            }}
        }
    </script>
    <style>
        body { font-family: 'DM Sans', sans-serif; }
        .font-heading { font-family: 'Sora', sans-serif; }
        .side-bg { background: linear-gradient(160deg, #1e1870 0%, #3d2dd4 50%, #5a4fff 100%); }
        .blob { position:absolute; border-radius:50%; filter:blur(80px); opacity:.2; }
        @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-10px)} }
        .float { animation: float 5s ease-in-out infinite; }
    </style>
</head>
<body class="min-h-screen flex antialiased bg-slate-50">

{{-- ── Left Panel (decorative) ──────────────────────────────── --}}
<div class="hidden lg:flex lg:w-1/2 xl:w-[55%] side-bg relative overflow-hidden flex-col items-center justify-center p-12">
    <div class="blob w-80 h-80 bg-purple-400 -top-10 -left-10"></div>
    <div class="blob w-64 h-64 bg-blue-400   bottom-10 right-10"></div>
    <div class="blob w-48 h-48 bg-teal-400   top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2"></div>

    <div class="relative z-10 text-center max-w-md">
        {{-- Logo --}}
        <div class="inline-flex items-center gap-3 mb-10">
            <div class="w-12 h-12 rounded-2xl bg-white/15 backdrop-blur-sm border border-white/20 flex items-center justify-center shadow-xl">
                <i class="fa-solid fa-graduation-cap text-white text-xl"></i>
            </div>
            <span class="font-heading font-bold text-white text-2xl">LMS Zila</span>
        </div>

        {{-- Mock dashboard preview --}}
        <div class="float bg-white/10 backdrop-blur-md border border-white/20 rounded-3xl p-6 mb-8 shadow-2xl text-left">
            <p class="font-heading font-semibold text-white text-sm mb-4">Selamat Datang Kembali 👋</p>
            <div class="grid grid-cols-2 gap-2.5 mb-4">
                @foreach([['fa-book-open','Materi','12 tersedia'],['fa-list-check','Tugas','2 pending'],['fa-user-check','Presensi','85%'],['fa-chart-bar','Nilai','A']] as [$ic,$lbl,$val])
                <div class="bg-white/10 rounded-xl px-3 py-2.5 flex items-center gap-2">
                    <i class="fa-solid {{ $ic }} text-white/70 text-xs w-4 text-center"></i>
                    <div>
                        <p class="text-white/50 text-xs leading-none">{{ $lbl }}</p>
                        <p class="text-white font-medium text-xs mt-0.5">{{ $val }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="h-1.5 bg-white/10 rounded-full overflow-hidden">
                <div class="h-1.5 bg-sage-400 rounded-full w-[72%]"></div>
            </div>
            <p class="text-white/40 text-xs mt-1.5">72% semester selesai</p>
        </div>

        <p class="text-white/60 text-sm leading-relaxed">
            Platform pembelajaran digital Universitas Muhammadiyah Kendari. Akses materi, tugas, ujian, dan presensi dalam satu tempat.
        </p>
    </div>
</div>

{{-- ── Right Panel (form) ───────────────────────────────────── --}}
<div class="flex-1 flex flex-col items-center justify-center p-6 sm:p-10">
    {{-- Mobile logo --}}
    <div class="lg:hidden flex items-center gap-2.5 mb-8">
        <div class="w-9 h-9 rounded-xl bg-ink-500 flex items-center justify-center shadow">
            <i class="fa-solid fa-graduation-cap text-white"></i>
        </div>
        <span class="font-heading font-bold text-slate-800 text-xl">LMS <span class="text-ink-500">Zila</span></span>
    </div>

    <div class="w-full max-w-md">
        <div class="mb-8">
            <h1 class="font-heading font-bold text-2xl md:text-3xl text-slate-800 mb-1.5">Selamat Datang Kembali</h1>
            <p class="text-slate-400 text-sm">Masuk ke akun LMS Zila Anda</p>
        </div>

        {{-- Session status --}}
        @if (session('status'))
        <div class="mb-4 bg-sage-50 border border-sage-200 text-sage-700 text-sm rounded-xl px-4 py-3">
            {{ session('status') }}
        </div>
        @endif

        {{-- Error global --}}
        @if ($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-600 text-sm rounded-xl px-4 py-3">
            <i class="fa-solid fa-circle-exclamation mr-1.5"></i>
            {{ $errors->first() }}
        </div>
        @endif

        @php
            $inputBase = 'w-full py-3 rounded-xl border text-sm focus:outline-none focus:ring-2 focus:ring-ink-200 focus:border-ink-300 transition-colors';
            $inputOk   = 'border-slate-200 bg-white';
            $inputErr  = 'border-red-300 bg-red-50';
        @endphp

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            {{-- Email --}}
            <div>
                <label for="email" class="block text-xs font-semibold text-slate-600 mb-1.5">Email</label>
                <div class="relative">
                    <i class="fa-solid fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                        placeholder="nama@email.com"
                        class="{{ $inputBase }} pl-10 pr-4 {{ $errors->has('email') ? $inputErr : $inputOk }}">
                </div>
                @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="text-xs font-semibold text-slate-600">Password</label>
                    @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-xs text-ink-500 hover:text-ink-700 font-medium">Lupa password?</a>
                    @endif
                </div>
                <div class="relative">
                    <i class="fa-solid fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                        placeholder="Masukkan password"
                        class="{{ $inputBase }} pl-10 pr-11 {{ $errors->has('password') ? $inputErr : $inputOk }}">
                    <button type="button" onclick="togglePassword()" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                        <i id="eye-icon" class="fa-solid fa-eye text-sm"></i>
                    </button>
                </div>
                @error('password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Remember me --}}
            <label class="flex items-center gap-2.5 cursor-pointer">
                <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 accent-ink-500">
                <span class="text-sm text-slate-500">Ingat saya di perangkat ini</span>
            </label>

            {{-- Submit --}}
            <button type="submit"
                class="w-full py-3.5 rounded-xl bg-ink-500 hover:bg-ink-600 active:bg-ink-700 text-white font-heading font-semibold text-sm transition-colors shadow-lg shadow-ink-500/25">
                <i class="fa-solid fa-arrow-right-to-bracket mr-2 text-xs"></i>Masuk
            </button>
        </form>

        <p class="text-center text-sm text-slate-500 mt-6">
            Belum punya akun?
            <a href="{{ route('register') }}" class="text-ink-500 hover:text-ink-700 font-semibold">Daftar sekarang</a>
        </p>

        <div class="mt-6 pt-6 border-t border-slate-100 text-center">
            <a href="/" class="text-xs text-slate-400 hover:text-slate-600 transition-colors">
                <i class="fa-solid fa-arrow-left text-xs mr-1"></i> Kembali ke Beranda
            </a>
        </div>
    </div>
</div>

<script>
    function togglePassword() {
        const input = document.getElementById('password');
        const icon  = document.getElementById('eye-icon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'fa-solid fa-eye-slash text-sm';
        } else {
            input.type = 'password';
            icon.className = 'fa-solid fa-eye text-sm';
        }
    }
</script>
</body>
</html>
