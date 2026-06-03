<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'LMS Zila') }} — {{ $title ?? 'Dashboard' }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,400&display=swap" rel="stylesheet" />
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sora: ['Sora', 'sans-serif'],
                        dm: ['DM Sans', 'sans-serif'],
                    },
                    colors: {
                        ink:   { 50:'#f0f0ff',100:'#e3e1ff',200:'#cac7ff',300:'#a9a4ff',400:'#8078ff',500:'#5a4fff',600:'#4a3bef',700:'#3d2dd4',800:'#3327ab',900:'#1e1870' },
                        sage:  { 50:'#effdf4',100:'#d8fbdf',200:'#b4f5c5',300:'#7de8a0',400:'#43d47a',500:'#1ab75d',600:'#0f964e',700:'#0d7441',800:'#0e5c38',900:'#0c4b2f' },
                        ember: { 50:'#fff8ec',100:'#ffeec8',300:'#ffc24a',400:'#ffaa1e',500:'#f98c05',600:'#dc6c02',700:'#b74f04',800:'#923b0b' },
                        slate: { 50:'#f8f9fc',100:'#f0f1f7',200:'#e2e4ef',300:'#c8cbde',400:'#9296b7',500:'#6b6f94',600:'#4e5278',700:'#3a3e62',800:'#252847',900:'#15173a' },
                    },
                }
            }
        }
    </script>
    <style>
        body { font-family: 'DM Sans', sans-serif; background: #f0f1f7; }
        h1,h2,h3,h4,h5,.font-heading { font-family: 'Sora', sans-serif; }
        .nav-item { transition: all 0.2s; }
        .nav-item:hover { background: rgba(90,79,255,0.08); }
        .nav-item.active { background: rgba(90,79,255,0.12); color: #5a4fff; }
        .nav-item.active i { color: #5a4fff; }
        .nav-group-header { transition: all 0.2s; }
        .nav-group-header:hover { background: rgba(255,255,255,0.05); }
        .card-hover { transition: transform 0.2s, box-shadow 0.2s; }
        .card-hover:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(90,79,255,0.15); }
        .progress-bar { transition: width 0.8s ease; }
        .badge { font-family: 'Sora', sans-serif; }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #c8cbde; border-radius: 99px; }
        .tab-active { border-bottom: 2px solid #5a4fff; color: #5a4fff; }

        /* Sidebar drawer */
        #sidebar { transition: transform 0.3s cubic-bezier(.4,0,.2,1); }
        #sidebar.open  { transform: translateX(0); }
        #sidebar.closed { transform: translateX(-100%); }
        #overlay { transition: opacity 0.3s; }
        #overlay.hidden  { opacity:0; pointer-events:none; }
        #overlay.visible { opacity:1; pointer-events:auto; }
        @media (min-width: 1024px) {
            #sidebar  { transform: translateX(0) !important; }
            #overlay  { display: none !important; }
        }

        /* Sub-menu collapse */
        .sub-menu { overflow: hidden; transition: max-height 0.3s ease; max-height: 0; }
        .sub-menu.open { max-height: 500px; }
        .chevron-rotate { transition: transform 0.3s; }
        .chevron-rotate.rotated { transform: rotate(180deg); }

        /* Avatar ring */
        .avatar-ring { box-shadow: 0 0 0 3px white, 0 0 0 5px #5a4fff; }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen">

<!-- Mobile Overlay -->
<div id="overlay" class="hidden fixed inset-0 bg-black/50 z-40 lg:hidden" onclick="closeSidebar()"></div>

<div class="flex min-h-screen">

    {{-- ===== SIDEBAR ===== --}}
    <aside id="sidebar" class="closed w-64 bg-slate-900 text-white flex flex-col fixed h-full z-50 shadow-xl lg:translate-x-0">

        {{-- Logo --}}
        <div class="px-6 py-5 flex items-center justify-between border-b border-slate-700/60">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-ink-500 flex items-center justify-center shadow-lg flex-shrink-0">
                    <i class="fa-solid fa-graduation-cap text-white text-sm"></i>
                </div>
                <div>
                    <span class="font-heading font-semibold text-base text-white tracking-tight leading-none block">LMS Zila</span>
                    <span class="text-slate-400 text-xs leading-none">
                        @auth
                            @if(auth()->user()->role === 'admin') Administrator
                            @elseif(auth()->user()->role === 'dosen') Dosen
                            @else Mahasiswa @endif
                        @endauth
                    </span>
                </div>
            </div>
            <button onclick="closeSidebar()" class="lg:hidden text-slate-400 hover:text-white transition-colors p-1">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">

            @auth

            {{-- ============ ADMIN ============ --}}
            @if(auth()->user()->role === 'admin')

            <p class="text-slate-500 text-xs font-medium px-3 py-2 uppercase tracking-wider">Menu Utama</p>

            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-300 cursor-pointer">
                <i class="fa-solid fa-house w-4 text-center text-slate-400"></i>
                <span>Dashboard</span>
            </a>

            <p class="text-slate-500 text-xs font-medium px-3 pt-4 pb-1.5 uppercase tracking-wider">Manajemen Akademik</p>

            <a href="{{ route('admin.tahun-akademik.index') }}" class="nav-item {{ request()->routeIs('admin.tahun-akademik.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-300 cursor-pointer">
                <i class="fa-solid fa-calendar-alt w-4 text-center text-slate-400"></i>
                <span>Tahun Akademik</span>
            </a>
            <a href="{{ route('admin.tahun-akademik.index') }}#semester" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-300 cursor-pointer">
                <i class="fa-solid fa-layer-group w-4 text-center text-slate-400"></i>
                <span>Semester</span>
            </a>

            <p class="text-slate-500 text-xs font-medium px-3 pt-4 pb-1.5 uppercase tracking-wider">Manajemen Pengguna</p>

            <a href="{{ route('admin.dosen.index') }}" class="nav-item {{ request()->routeIs('admin.dosen.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-300 cursor-pointer">
                <i class="fa-solid fa-chalkboard-user w-4 text-center text-slate-400"></i>
                <span>Dosen</span>
            </a>
            <a href="{{ route('admin.mahasiswa.index') }}" class="nav-item {{ request()->routeIs('admin.mahasiswa.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-300 cursor-pointer">
                <i class="fa-solid fa-user-graduate w-4 text-center text-slate-400"></i>
                <span>Mahasiswa</span>
            </a>

            <p class="text-slate-500 text-xs font-medium px-3 pt-4 pb-1.5 uppercase tracking-wider">Akademik</p>

            <a href="{{ route('admin.mata-kuliah.index') }}" class="nav-item {{ request()->routeIs('admin.mata-kuliah.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-300 cursor-pointer">
                <i class="fa-solid fa-book w-4 text-center text-slate-400"></i>
                <span>Mata Kuliah</span>
            </a>
            <a href="{{ route('admin.kelas.index') }}" class="nav-item {{ request()->routeIs('admin.kelas.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-300 cursor-pointer">
                <i class="fa-solid fa-door-open w-4 text-center text-slate-400"></i>
                <span>Kelas</span>
            </a>
            <a href="{{ route('admin.pengampu.index') }}" class="nav-item {{ request()->routeIs('admin.pengampu.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-300 cursor-pointer">
                <i class="fa-solid fa-user-tie w-4 text-center text-slate-400"></i>
                <span>Pengampu</span>
            </a>
            <a href="{{ route('admin.enroll.index') }}" class="nav-item {{ request()->routeIs('admin.enroll.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-300 cursor-pointer">
                <i class="fa-solid fa-user-plus w-4 text-center text-slate-400"></i>
                <span>Enroll Mahasiswa</span>
            </a>

            <p class="text-slate-500 text-xs font-medium px-3 pt-4 pb-1.5 uppercase tracking-wider">Sistem</p>

            <a href="#" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-300 cursor-pointer">
                <i class="fa-solid fa-chart-bar w-4 text-center text-slate-400"></i>
                <span>Laporan</span>
            </a>
            <a href="#" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-300 cursor-pointer">
                <i class="fa-solid fa-bullhorn w-4 text-center text-slate-400"></i>
                <span>Pengumuman</span>
            </a>

            {{-- ============ DOSEN ============ --}}
            @elseif(auth()->user()->role === 'dosen')

            <p class="text-slate-500 text-xs font-medium px-3 py-2 uppercase tracking-wider">Menu Utama</p>

            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-300 cursor-pointer">
                <i class="fa-solid fa-house w-4 text-center text-slate-400"></i>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('dosen.mata-kuliah.index') }}" class="nav-item {{ request()->routeIs('dosen.mata-kuliah.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-300 cursor-pointer">
                <i class="fa-solid fa-book-open w-4 text-center text-slate-400"></i>
                <span>Mata Kuliah Saya</span>
            </a>

            <p class="text-slate-500 text-xs font-medium px-3 pt-4 pb-1.5 uppercase tracking-wider">Pembelajaran</p>

            <a href="{{ route('dosen.mata-kuliah.index') }}" class="nav-item {{ request()->routeIs('dosen.pertemuan.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-300 cursor-pointer">
                <i class="fa-solid fa-calendar-days w-4 text-center text-slate-400"></i>
                <span>Pertemuan</span>
            </a>
            <a href="{{ route('dosen.mata-kuliah.index') }}" class="nav-item {{ request()->routeIs('dosen.rps.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-300 cursor-pointer">
                <i class="fa-solid fa-file-lines w-4 text-center text-slate-400"></i>
                <span>RPS</span>
            </a>
            <a href="{{ route('dosen.mata-kuliah.index') }}" class="nav-item {{ request()->routeIs('dosen.materi.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-300 cursor-pointer">
                <i class="fa-solid fa-folder-open w-4 text-center text-slate-400"></i>
                <span>Materi</span>
            </a>

            <p class="text-slate-500 text-xs font-medium px-3 pt-4 pb-1.5 uppercase tracking-wider">Evaluasi</p>

            <a href="{{ route('dosen.mata-kuliah.index') }}" class="nav-item {{ request()->routeIs('dosen.tugas.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-300 cursor-pointer">
                <i class="fa-solid fa-list-check w-4 text-center text-slate-400"></i>
                <span>Tugas</span>
            </a>
            <a href="{{ route('dosen.bank-soal.index') }}" class="nav-item {{ request()->routeIs('dosen.bank-soal.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-300 cursor-pointer">
                <i class="fa-solid fa-pen-to-square w-4 text-center text-slate-400"></i>
                <span>Bank Soal</span>
            </a>
            <a href="{{ route('dosen.mata-kuliah.index') }}" class="nav-item {{ request()->routeIs('dosen.ujian.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-300 cursor-pointer">
                <i class="fa-solid fa-clipboard-question w-4 text-center text-slate-400"></i>
                <span>Ujian</span>
            </a>
            <a href="{{ route('dosen.mata-kuliah.index') }}" class="nav-item {{ request()->routeIs('dosen.presensi.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-300 cursor-pointer">
                <i class="fa-solid fa-user-check w-4 text-center text-slate-400"></i>
                <span>Presensi</span>
            </a>

            <p class="text-slate-500 text-xs font-medium px-3 pt-4 pb-1.5 uppercase tracking-wider">Penilaian</p>

            <a href="{{ route('dosen.mata-kuliah.index') }}" class="nav-item {{ request()->routeIs('dosen.penilaian.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-300 cursor-pointer">
                <i class="fa-solid fa-star-half-stroke w-4 text-center text-slate-400"></i>
                <span>Penilaian</span>
            </a>
            <a href="{{ route('dosen.mata-kuliah.index') }}" class="nav-item {{ request()->routeIs('dosen.capaian-cpmk.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-300 cursor-pointer">
                <i class="fa-solid fa-chart-line w-4 text-center text-slate-400"></i>
                <span>Capaian CPMK</span>
            </a>
            <a href="{{ route('dosen.mata-kuliah.index') }}" class="nav-item {{ request()->routeIs('dosen.laporan.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-300 cursor-pointer">
                <i class="fa-solid fa-file-export w-4 text-center text-slate-400"></i>
                <span>Laporan</span>
            </a>

            <p class="text-slate-500 text-xs font-medium px-3 pt-4 pb-1.5 uppercase tracking-wider">Komunikasi</p>

            <a href="{{ route('dosen.mata-kuliah.index') }}" class="nav-item {{ request()->routeIs('dosen.forum.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-300 cursor-pointer">
                <i class="fa-solid fa-comments w-4 text-center text-slate-400"></i>
                <span>Forum Diskusi</span>
            </a>
            <a href="{{ route('dosen.mata-kuliah.index') }}" class="nav-item {{ request()->routeIs('dosen.pengumuman.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-300 cursor-pointer">
                <i class="fa-solid fa-bullhorn w-4 text-center text-slate-400"></i>
                <span>Pengumuman</span>
            </a>

            {{-- ============ MAHASISWA ============ --}}
            @else

            <p class="text-slate-500 text-xs font-medium px-3 py-2 uppercase tracking-wider">Menu Utama</p>

            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-300 cursor-pointer">
                <i class="fa-solid fa-house w-4 text-center text-slate-400"></i>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('mahasiswa.kelas.index') }}" class="nav-item {{ request()->routeIs('mahasiswa.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-300 cursor-pointer">
                <i class="fa-solid fa-book-open w-4 text-center text-slate-400"></i>
                <span>Mata Kuliah Saya</span>
            </a>

            <p class="text-slate-500 text-xs font-medium px-3 pt-4 pb-1.5 uppercase tracking-wider">Pembelajaran</p>

            <a href="{{ route('mahasiswa.kelas.index') }}" class="nav-item {{ request()->routeIs('mahasiswa.materi.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-300 cursor-pointer">
                <i class="fa-solid fa-folder-open w-4 text-center text-slate-400"></i>
                <span>Materi</span>
            </a>
            <a href="{{ route('mahasiswa.kelas.index') }}" class="nav-item {{ request()->routeIs('mahasiswa.presensi.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-300 cursor-pointer">
                <i class="fa-solid fa-user-check w-4 text-center text-slate-400"></i>
                <span>Presensi</span>
            </a>

            <p class="text-slate-500 text-xs font-medium px-3 pt-4 pb-1.5 uppercase tracking-wider">Evaluasi</p>

            <a href="{{ route('mahasiswa.kelas.index') }}" class="nav-item {{ request()->routeIs('mahasiswa.tugas.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-300 cursor-pointer">
                <i class="fa-solid fa-list-check w-4 text-center text-slate-400"></i>
                <span>Tugas</span>
            </a>
            <a href="{{ route('mahasiswa.kelas.index') }}" class="nav-item {{ request()->routeIs('mahasiswa.ujian.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-300 cursor-pointer">
                <i class="fa-solid fa-clipboard-question w-4 text-center text-slate-400"></i>
                <span>Ujian</span>
            </a>

            <p class="text-slate-500 text-xs font-medium px-3 pt-4 pb-1.5 uppercase tracking-wider">Akademik</p>

            <a href="{{ route('mahasiswa.kelas.index') }}" class="nav-item {{ request()->routeIs('mahasiswa.nilai.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-300 cursor-pointer">
                <i class="fa-solid fa-chart-bar w-4 text-center text-slate-400"></i>
                <span>Nilai</span>
            </a>
            <a href="{{ route('mahasiswa.kelas.index') }}" class="nav-item {{ request()->routeIs('mahasiswa.capaian-cpmk.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-300 cursor-pointer">
                <i class="fa-solid fa-chart-line w-4 text-center text-slate-400"></i>
                <span>Capaian CPMK</span>
            </a>
            <a href="{{ route('mahasiswa.kelas.index') }}" class="nav-item {{ request()->routeIs('mahasiswa.rps.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-300 cursor-pointer">
                <i class="fa-solid fa-file-lines w-4 text-center text-slate-400"></i>
                <span>RPS</span>
            </a>

            <p class="text-slate-500 text-xs font-medium px-3 pt-4 pb-1.5 uppercase tracking-wider">Komunikasi</p>

            <a href="{{ route('mahasiswa.kelas.index') }}" class="nav-item {{ request()->routeIs('mahasiswa.forum.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-300 cursor-pointer">
                <i class="fa-solid fa-comments w-4 text-center text-slate-400"></i>
                <span>Forum Diskusi</span>
            </a>
            <a href="{{ route('mahasiswa.kelas.index') }}" class="nav-item {{ request()->routeIs('mahasiswa.pengumuman.*') ? 'active' : '' }} flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-300 cursor-pointer">
                <i class="fa-solid fa-bullhorn w-4 text-center text-slate-400"></i>
                <span>Pengumuman</span>
            </a>

            @endif
            @endauth

        </nav>

        {{-- User Profile Footer --}}
        @auth
        <div class="px-4 py-4 border-t border-slate-700/60">
            <div class="flex items-center gap-3">
                <div class="relative flex-shrink-0">
                    <div class="w-9 h-9 rounded-full bg-ink-600 flex items-center justify-center text-white font-semibold text-sm avatar-ring">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-sage-400 border-2 border-slate-900 rounded-full"></span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate font-heading">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-slate-400 truncate">
                        @if(auth()->user()->role === 'admin') Administrator
                        @elseif(auth()->user()->role === 'dosen') Dosen
                        @else Mahasiswa @endif
                    </p>
                </div>
                <a href="{{ route('profile.edit') }}" class="text-slate-400 hover:text-white transition-colors" title="Pengaturan Profil">
                    <i class="fa-solid fa-gear text-sm"></i>
                </a>
            </div>
        </div>
        @endauth

    </aside>
    {{-- ===== END SIDEBAR ===== --}}

    {{-- ===== MAIN CONTENT ===== --}}
    <main class="w-full lg:ml-64 flex-1 flex flex-col min-h-screen">

        {{-- Topbar --}}
        <header class="sticky top-0 z-30 bg-white/90 backdrop-blur border-b border-slate-200 px-4 md:px-8 py-3.5 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                {{-- Hamburger (mobile) --}}
                <button onclick="openSidebar()" class="lg:hidden w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-ink-50 hover:text-ink-500 transition-colors flex-shrink-0">
                    <i class="fa-solid fa-bars text-sm"></i>
                </button>
                <div>
                    <h1 class="font-heading font-semibold text-slate-800 text-base md:text-lg leading-tight">
                        {{ $pageTitle ?? 'Dashboard' }}
                    </h1>
                    <p class="text-xs text-slate-400 mt-0.5 hidden sm:block">
                        {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 md:gap-3">
                {{-- Search (desktop) --}}
                <div class="relative hidden md:block">
                    <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" placeholder="Cari..." class="pl-9 pr-4 py-2 rounded-xl bg-slate-100 text-sm border border-transparent focus:outline-none focus:border-ink-300 w-44 lg:w-56 text-slate-700 placeholder:text-slate-400" />
                </div>
                {{-- Search icon (mobile) --}}
                <button class="md:hidden w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500">
                    <i class="fa-solid fa-search text-sm"></i>
                </button>
                {{-- Notifications --}}
                <button class="relative w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-ink-50 hover:text-ink-500 transition-colors">
                    <i class="fa-solid fa-bell text-sm"></i>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-ember-500 rounded-full"></span>
                </button>
                {{-- Profile Dropdown --}}
                @auth
                <div class="relative" id="profileMenu">
                    <button onclick="toggleProfileMenu()" class="flex items-center gap-2 pl-1 pr-2 py-1 rounded-xl hover:bg-slate-100 transition-colors cursor-pointer">
                        <div class="w-8 h-8 rounded-xl bg-ink-600 flex items-center justify-center text-white font-semibold text-sm ring-2 ring-ink-200">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <i class="fa-solid fa-chevron-down text-slate-400 text-xs hidden sm:block transition-transform duration-200" id="profileChevron"></i>
                    </button>
                    {{-- Dropdown --}}
                    <div id="profileDropdown" class="hidden absolute right-0 top-full mt-2 w-52 bg-white rounded-2xl border border-slate-200 shadow-xl shadow-slate-200/60 overflow-hidden z-50">
                        <div class="px-4 py-3 border-b border-slate-100">
                            <p class="text-sm font-heading font-semibold text-slate-800 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-slate-400 truncate">{{ auth()->user()->email }}</p>
                        </div>
                        <div class="py-1.5">
                            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-50 hover:text-ink-600 transition-colors group">
                                <span class="w-7 h-7 rounded-lg bg-ink-50 group-hover:bg-ink-100 flex items-center justify-center transition-colors">
                                    <i class="fa-solid fa-user text-ink-400 text-xs"></i>
                                </span>
                                <span class="font-medium">Profil Saya</span>
                            </a>
                            <a href="{{ route('profile.edit') }}#password" class="flex items-center gap-3 px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-50 hover:text-ink-600 transition-colors group">
                                <span class="w-7 h-7 rounded-lg bg-ink-50 group-hover:bg-ink-100 flex items-center justify-center transition-colors">
                                    <i class="fa-solid fa-lock text-ink-400 text-xs"></i>
                                </span>
                                <span class="font-medium">Ubah Password</span>
                            </a>
                        </div>
                        <div class="border-t border-slate-100 py-1.5">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 transition-colors group">
                                    <span class="w-7 h-7 rounded-lg bg-red-50 group-hover:bg-red-100 flex items-center justify-center transition-colors">
                                        <i class="fa-solid fa-right-from-bracket text-red-400 text-xs"></i>
                                    </span>
                                    <span class="font-medium">Keluar</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endauth
            </div>
        </header>

        {{-- Page Content --}}
        <div class="flex-1 px-4 md:px-8 py-4 md:py-6">
            {{ $slot }}
        </div>

        {{-- Footer --}}
        <footer class="px-4 md:px-8 py-4 border-t border-slate-200 bg-white flex flex-col sm:flex-row items-center justify-between gap-2">
            <p class="text-xs text-slate-400">&copy; {{ date('Y') }} LMS Zila. Hak cipta dilindungi.</p>
            <div class="flex gap-4 text-xs text-slate-400">
                <a href="#" class="hover:text-ink-500 transition-colors">Bantuan</a>
                <a href="#" class="hover:text-ink-500 transition-colors">Privasi</a>
                <a href="#" class="hover:text-ink-500 transition-colors">Hubungi Kami</a>
            </div>
        </footer>

        {{-- Mobile Bottom Nav --}}
        @auth
        <nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-slate-200 z-30 flex items-center justify-around px-2 py-2">
            <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl {{ request()->routeIs('dashboard') ? 'text-ink-500' : 'text-slate-400' }}">
                <i class="fa-solid fa-house text-base"></i>
                <span class="text-xs {{ request()->routeIs('dashboard') ? 'font-medium' : '' }}">Home</span>
            </a>
            <a href="#" class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl text-slate-400">
                <i class="fa-solid fa-book-open text-base"></i>
                <span class="text-xs">Mata Kuliah</span>
            </a>
            <a href="#" class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl text-slate-400 relative">
                <i class="fa-solid fa-list-check text-base"></i>
                <span class="absolute top-1 right-1.5 w-2 h-2 bg-ember-500 rounded-full"></span>
                <span class="text-xs">Tugas</span>
            </a>
            <a href="#" class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl text-slate-400">
                <i class="fa-solid fa-comments text-base"></i>
                <span class="text-xs">Forum</span>
            </a>
            <a href="{{ route('profile.edit') }}" class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl text-slate-400">
                <div class="w-6 h-6 rounded-full bg-ink-600 flex items-center justify-center text-white text-xs ring-1 ring-slate-200">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <span class="text-xs">Profil</span>
            </a>
        </nav>
        <div class="h-16 lg:hidden"></div>
        @endauth

    </main>
    {{-- ===== END MAIN CONTENT ===== --}}

</div>

@stack('scripts')
<script>
    function toggleProfileMenu() {
        const dd = document.getElementById('profileDropdown');
        const chevron = document.getElementById('profileChevron');
        const isHidden = dd.classList.contains('hidden');
        dd.classList.toggle('hidden', !isHidden);
        if (chevron) chevron.style.transform = isHidden ? 'rotate(180deg)' : '';
    }
    document.addEventListener('click', function(e) {
        const menu = document.getElementById('profileMenu');
        const dd = document.getElementById('profileDropdown');
        const chevron = document.getElementById('profileChevron');
        if (menu && !menu.contains(e.target)) {
            dd.classList.add('hidden');
            if (chevron) chevron.style.transform = '';
        }
    });
    function openSidebar() {
        document.getElementById('sidebar').classList.remove('closed');
        document.getElementById('sidebar').classList.add('open');
        document.getElementById('overlay').classList.remove('hidden');
        document.getElementById('overlay').classList.add('visible');
        document.body.style.overflow = 'hidden';
    }
    function closeSidebar() {
        document.getElementById('sidebar').classList.remove('open');
        document.getElementById('sidebar').classList.add('closed');
        document.getElementById('overlay').classList.remove('visible');
        document.getElementById('overlay').classList.add('hidden');
        document.body.style.overflow = '';
    }
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) {
            document.body.style.overflow = '';
            const ov = document.getElementById('overlay');
            if (ov) { ov.classList.add('hidden'); ov.classList.remove('visible'); }
        }
    });
</script>
</body>
</html>
