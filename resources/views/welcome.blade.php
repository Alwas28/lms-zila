<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LMS Zila — Platform Pembelajaran Digital UM Kendari</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: { extend: {
                fontFamily: { heading: ['Sora','sans-serif'], body: ['DM Sans','sans-serif'] },
                colors: {
                    ink:   { 50:'#f0f0ff',100:'#e3e1ff',200:'#cac7ff',300:'#a9a4ff',400:'#8078ff',500:'#5a4fff',600:'#4a3bef',700:'#3d2dd4',800:'#3327ab',900:'#1e1870' },
                    sage:  { 50:'#effdf4',100:'#d8fbdf',300:'#7de8a0',400:'#43d47a',500:'#1ab75d',600:'#0f964e' },
                    ember: { 50:'#fff8ec',100:'#ffeec8',400:'#ffaa1e',500:'#f98c05',600:'#dc6c02' },
                }
            }}
        }
    </script>
    <style>
        body { font-family: 'DM Sans', sans-serif; }
        .font-heading { font-family: 'Sora', sans-serif; }
        .hero-bg { background: linear-gradient(135deg, #1e1870 0%, #3d2dd4 45%, #5a4fff 75%, #7060ff 100%); }
        .blob { position:absolute; border-radius:50%; filter:blur(90px); opacity:.18; pointer-events:none; }
        @keyframes float  { 0%,100%{transform:translateY(0px)}  50%{transform:translateY(-14px)} }
        @keyframes float2 { 0%,100%{transform:translateY(0px)}  50%{transform:translateY(-9px)} }
        .float  { animation: float  4.5s ease-in-out infinite; }
        .float2 { animation: float2 5.5s ease-in-out infinite .8s; }
        @keyframes pulse-dot { 0%,100%{opacity:1} 50%{opacity:.4} }
        .pulse-dot { animation: pulse-dot 2s ease-in-out infinite; }
        .feature-card:hover { transform: translateY(-2px); }
        .feature-card { transition: transform .2s ease, box-shadow .2s ease; }
    </style>
</head>
<body class="bg-white antialiased text-slate-800">

{{-- ══════════════════════════ NAVBAR ══════════════════════════ --}}
<header class="fixed top-0 inset-x-0 z-50 bg-white/80 backdrop-blur-xl border-b border-slate-100">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-4">
        <a href="/" class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-xl bg-ink-500 flex items-center justify-center shadow">
                <i class="fa-solid fa-graduation-cap text-white text-sm"></i>
            </div>
            <span class="font-heading font-bold text-slate-800 text-lg">LMS <span class="text-ink-500">Zila</span></span>
        </a>

        <nav class="hidden md:flex items-center gap-7 text-sm font-medium text-slate-500">
            <a href="#fitur"    class="hover:text-ink-500 transition-colors">Fitur</a>
            <a href="#tentang"  class="hover:text-ink-500 transition-colors">Tentang</a>
        </nav>

        <div class="flex items-center gap-2">
            @auth
            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-ink-500 hover:bg-ink-600 text-white text-sm font-medium transition-colors shadow-sm">
                <i class="fa-solid fa-gauge text-xs"></i> Dashboard
            </a>
            @else
            <a href="{{ route('login') }}"
               class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-ink-500 transition-colors">
                Masuk
            </a>
            <a href="{{ route('register') }}"
               class="px-4 py-2 rounded-xl bg-ink-500 hover:bg-ink-600 text-white text-sm font-medium transition-colors shadow-sm">
                Daftar
            </a>
            @endauth
        </div>
    </div>
</header>

{{-- ══════════════════════════ HERO ════════════════════════════ --}}
<section class="hero-bg pt-28 pb-24 md:pt-36 md:pb-32 relative overflow-hidden">
    <div class="blob w-[500px] h-[500px] bg-purple-500  -top-20 -left-32"></div>
    <div class="blob w-96  h-96  bg-blue-500   bottom-0 right-0"></div>
    <div class="blob w-72  h-72  bg-teal-400   top-24  right-1/3"></div>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

            {{-- Left: copy --}}
            <div>
                <div class="inline-flex items-center gap-2 text-white/80 border border-white/20 bg-white/10 backdrop-blur-sm text-xs font-medium px-3 py-1.5 rounded-full mb-6">
                    <span class="pulse-dot w-1.5 h-1.5 rounded-full bg-sage-400 block"></span>
                    Platform Pembelajaran Digital UM Kendari
                </div>
                <h1 class="font-heading font-extrabold text-4xl md:text-5xl xl:text-6xl text-white leading-[1.1] mb-6">
                    Belajar Lebih Mudah,<br>
                    <span class="text-sage-300">Lebih Cerdas</span>
                </h1>
                <p class="text-white/70 text-lg leading-relaxed mb-9 max-w-lg">
                    Akses materi kuliah, kumpulkan tugas, ikuti ujian online, dan pantau presensi —
                    semuanya dari satu platform terpadu.
                </p>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('register') }}"
                       class="inline-flex items-center gap-2.5 px-6 py-3.5 rounded-2xl bg-white text-ink-600 font-heading font-semibold text-sm hover:bg-ink-50 transition-colors shadow-xl shadow-ink-900/25">
                        <i class="fa-solid fa-user-plus"></i> Daftar Sekarang
                    </a>
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center gap-2.5 px-6 py-3.5 rounded-2xl border border-white/25 text-white font-heading font-semibold text-sm hover:bg-white/10 transition-colors">
                        <i class="fa-solid fa-arrow-right-to-bracket"></i> Masuk
                    </a>
                </div>
            </div>

            {{-- Right: mock UI card --}}
            <div class="hidden lg:flex items-center justify-center">
                <div class="relative float">
                    {{-- Main card --}}
                    <div class="w-80 bg-white/10 backdrop-blur-lg border border-white/20 rounded-3xl p-6 shadow-2xl">
                        <div class="flex items-center justify-between mb-5">
                            <div>
                                <p class="font-heading font-semibold text-white text-sm">Materi Hari Ini</p>
                                <p class="text-white/50 text-xs mt-0.5">Basis Data — Pertemuan 8</p>
                            </div>
                            <div class="w-8 h-8 rounded-xl bg-sage-400/20 flex items-center justify-center">
                                <i class="fa-solid fa-book-open text-sage-300 text-sm"></i>
                            </div>
                        </div>
                        <div class="space-y-2.5">
                            @foreach([['fa-file-pdf','red','Normalisasi Database.pdf'],['fa-brands fa-youtube','red','Video Kuliah — ERD'],['fa-file-powerpoint','orange','Slide Presentasi.pptx']] as [$ic,$cl,$nm])
                            <div class="flex items-center gap-3 bg-white/10 rounded-xl px-3 py-2.5">
                                <div class="w-7 h-7 rounded-lg bg-white/10 flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid {{ $ic }} text-white/80 text-xs"></i>
                                </div>
                                <p class="text-white/80 text-xs truncate">{{ $nm }}</p>
                            </div>
                            @endforeach
                        </div>
                        <div class="mt-4 pt-4 border-t border-white/10 flex items-center justify-between">
                            <p class="text-white/40 text-xs">3 materi tersedia</p>
                            <button class="text-xs font-medium text-sage-300 hover:text-sage-200">Lihat Semua →</button>
                        </div>
                    </div>

                    {{-- Floating: presensi --}}
                    <div class="float2 absolute -bottom-10 -right-10 bg-white rounded-2xl shadow-xl px-4 py-3 flex items-center gap-3 border border-slate-100">
                        <div class="w-9 h-9 rounded-xl bg-sage-50 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-circle-check text-sage-500"></i>
                        </div>
                        <div>
                            <p class="font-heading font-semibold text-slate-800 text-xs">Hadir ✓</p>
                            <p class="text-slate-400 text-xs">85% kehadiran</p>
                        </div>
                    </div>

                    {{-- Floating: tugas --}}
                    <div class="float absolute -top-8 -left-8 bg-white rounded-2xl shadow-xl px-4 py-3 flex items-center gap-3 border border-slate-100">
                        <div class="w-9 h-9 rounded-xl bg-ember-50 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-list-check text-ember-500"></i>
                        </div>
                        <div>
                            <p class="font-heading font-semibold text-slate-800 text-xs">2 Tugas</p>
                            <p class="text-slate-400 text-xs">Deadline minggu ini</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════ STATS ═══════════════════════════ --}}
<section class="py-14 bg-slate-50 border-y border-slate-100">
    <div class="max-w-5xl mx-auto px-4 sm:px-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            @foreach([
                ['fa-book',          'text-ink-500',    '20+',  'Mata Kuliah'],
                ['fa-chalkboard-user','text-sage-600',   '15+',  'Dosen'],
                ['fa-user-graduate', 'text-ember-500',  '500+', 'Mahasiswa'],
                ['fa-layer-group',   'text-purple-500', '20',   'Fitur Aktif'],
            ] as [$ic, $cl, $val, $lbl])
            <div class="group">
                <i class="fa-solid {{ $ic }} {{ $cl }} text-3xl mb-2 group-hover:scale-110 transition-transform inline-block"></i>
                <p class="font-heading font-extrabold text-3xl text-slate-800">{{ $val }}</p>
                <p class="text-sm text-slate-400 mt-0.5">{{ $lbl }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══════════════════════════ FITUR ═══════════════════════════ --}}
<section id="fitur" class="py-24 md:py-32">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-14">
            <p class="text-ink-500 text-sm font-semibold tracking-widest uppercase mb-3">Fitur Unggulan</p>
            <h2 class="font-heading font-bold text-3xl md:text-4xl text-slate-800 mb-4">Satu Platform, Semua Kebutuhan</h2>
            <p class="text-slate-400 max-w-xl mx-auto leading-relaxed">
                Dari materi kuliah hingga ujian online — semua dirancang untuk memudahkan proses belajar mengajar.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach([
                ['fa-folder-open',        'ink',    'Materi Digital',     'PDF, PPT, video, YouTube — semua format materi kuliah dalam satu tempat yang mudah diakses.'],
                ['fa-list-check',         'ember',  'Tugas & Penilaian',  'Kumpulkan tugas langsung dari platform. Dosen menilai dan memberikan feedback secara online.'],
                ['fa-clipboard-question', 'purple', 'Ujian Online',       'Kerjakan ujian dengan timer, soal acak, dan penilaian otomatis untuk pilihan ganda.'],
                ['fa-user-check',         'sage',   'Presensi Digital',   'Check-in kehadiran menggunakan kode unik yang dibuka dosen setiap sesi pertemuan.'],
                ['fa-chart-line',         'blue',   'Capaian CPMK',       'Lihat grafik ketercapaian Capaian Pembelajaran Mata Kuliah (CPMK) secara real-time.'],
                ['fa-file-export',        'red',    'Laporan & Ekspor',   'Download laporan nilai, presensi, dan capaian dalam format PDF maupun Excel.'],
            ] as [$ic, $cl, $title, $desc])
            <div class="feature-card bg-white rounded-2xl border border-slate-100 p-6 hover:border-ink-200 hover:shadow-md">
                <div class="w-12 h-12 rounded-2xl bg-{{ $cl }}-50 flex items-center justify-center mb-5">
                    <i class="fa-solid fa-{{ $ic }} text-{{ $cl }}-500 text-xl"></i>
                </div>
                <h3 class="font-heading font-semibold text-slate-800 text-base mb-2">{{ $title }}</h3>
                <p class="text-slate-500 text-sm leading-relaxed">{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══════════════════════════ ROLES ═══════════════════════════ --}}
<section class="py-16 bg-slate-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-10">
            <h2 class="font-heading font-bold text-2xl md:text-3xl text-slate-800">Untuk Semua Peran</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            @foreach([
                ['fa-user-tie',      'ink',   'Administrator', 'Kelola mata kuliah, kelas, dosen, dan mahasiswa. Pantau seluruh aktivitas akademik.'],
                ['fa-chalkboard-user','sage', 'Dosen',         'Buat materi, tugas, ujian, kelola presensi, dan nilai mahasiswa dengan mudah.'],
                ['fa-user-graduate', 'ember', 'Mahasiswa',     'Akses materi, kumpulkan tugas, ikuti ujian, dan pantau perkembangan belajarmu.'],
            ] as [$ic, $cl, $role, $desc])
            <div class="bg-white rounded-2xl border border-slate-100 p-6 text-center hover:border-{{ $cl }}-200 hover:shadow-sm transition-all">
                <div class="w-14 h-14 rounded-2xl bg-{{ $cl }}-50 flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-{{ $ic }} text-{{ $cl }}-500 text-2xl"></i>
                </div>
                <h3 class="font-heading font-semibold text-slate-800 text-base mb-2">{{ $role }}</h3>
                <p class="text-slate-400 text-sm leading-relaxed">{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ══════════════════════════ CTA ═════════════════════════════ --}}
<section id="tentang" class="py-20 md:py-24">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 text-center">
        <div class="hero-bg rounded-3xl p-10 md:p-14 relative overflow-hidden shadow-2xl shadow-ink-900/30">
            <div class="blob w-64 h-64 bg-sage-400  top-0  right-0"></div>
            <div class="blob w-48 h-48 bg-ember-400 bottom-0 left-0"></div>
            <div class="relative z-10">
                <div class="w-16 h-16 rounded-2xl bg-white/10 border border-white/20 flex items-center justify-center mx-auto mb-5 backdrop-blur-sm">
                    <i class="fa-solid fa-graduation-cap text-white text-2xl"></i>
                </div>
                <h2 class="font-heading font-bold text-2xl md:text-3xl text-white mb-3">Siap Mulai Belajar?</h2>
                <p class="text-white/65 mb-8 max-w-md mx-auto leading-relaxed">
                    Daftar sekarang dan bergabung bersama civitas akademika Universitas Muhammadiyah Kendari.
                </p>
                <a href="{{ route('register') }}"
                   class="inline-flex items-center gap-2.5 px-7 py-3.5 rounded-2xl bg-white text-ink-600 font-heading font-semibold hover:bg-ink-50 transition-colors shadow-xl">
                    <i class="fa-solid fa-user-plus"></i> Daftar Sebagai Mahasiswa
                </a>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════════ FOOTER ══════════════════════════ --}}
<footer class="bg-slate-900 py-10">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 flex flex-col md:flex-row items-center justify-between gap-5 text-sm">
        <div class="flex items-center gap-2.5">
            <div class="w-7 h-7 rounded-lg bg-ink-500 flex items-center justify-center">
                <i class="fa-solid fa-graduation-cap text-white text-xs"></i>
            </div>
            <span class="font-heading font-semibold text-white">LMS Zila</span>
        </div>
        <p class="text-slate-500 text-center">
            © {{ date('Y') }} Universitas Muhammadiyah Kendari — Platform Pembelajaran Digital
        </p>
        <div class="flex items-center gap-5 text-slate-400">
            <a href="{{ route('login') }}"    class="hover:text-white transition-colors">Masuk</a>
            <a href="{{ route('register') }}" class="hover:text-white transition-colors">Daftar</a>
        </div>
    </div>
</footer>

</body>
</html>
