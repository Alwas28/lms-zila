<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $ujian->judul }} — LMS Zila</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: { extend: { colors: {
                ink:  { 50:'#f0f0ff',500:'#5a4fff',600:'#4a3bef' },
                sage: { 500:'#1ab75d' },
            }}}
        }
    </script>
</head>
<body class="bg-slate-100 min-h-screen">

{{-- Header ujian --}}
<div class="bg-white border-b border-slate-200 sticky top-0 z-30">
    <div class="max-w-3xl mx-auto px-4 py-3 flex items-center justify-between gap-4">
        <div class="flex-1 min-w-0">
            <p class="font-semibold text-slate-800 text-sm truncate">{{ $ujian->judul }}</p>
            <p class="text-xs text-slate-400">{{ $kelas->mataKuliah->nama }} — Kelas {{ $kelas->nama_kelas }}</p>
        </div>
        <div class="flex items-center gap-3 flex-shrink-0">
            <div id="timer" class="flex items-center gap-2 bg-ink-50 text-ink-600 px-3 py-1.5 rounded-xl font-mono font-bold text-sm">
                <i class="fa-regular fa-clock text-xs"></i>
                <span id="timer-text">--:--</span>
            </div>
        </div>
    </div>
</div>

<div class="max-w-3xl mx-auto px-4 py-6">
    <form id="form-ujian" action="{{ route('mahasiswa.ujian.selesai', [$kelas, $ujian]) }}" method="POST">
        @csrf

        {{-- Soal --}}
        <div class="space-y-5">
            @foreach($soalList as $idx => $ujianSoal)
            @php $soal = $ujianSoal->bankSoal; @endphp
            <div class="bg-white rounded-2xl border border-slate-200 p-5" id="soal-{{ $idx+1 }}">
                <div class="flex items-start gap-3 mb-4">
                    <span class="w-7 h-7 rounded-lg bg-ink-500 text-white flex items-center justify-center text-xs font-bold flex-shrink-0">
                        {{ $idx+1 }}
                    </span>
                    <p class="text-sm text-slate-800 leading-relaxed flex-1">{!! nl2br(e($soal->pertanyaan)) !!}</p>
                </div>

                @if($soal->tipe === 'pilihan_ganda')
                <div class="space-y-2 ml-10">
                    @foreach($soal->pilihan as $pilihan)
                    <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 cursor-pointer hover:border-ink-300 hover:bg-ink-50/50 transition-all has-[:checked]:border-ink-500 has-[:checked]:bg-ink-50">
                        <input type="radio" name="jawaban[{{ $soal->id }}]" value="{{ $pilihan->id }}"
                            {{ ($jawabanSudah[$soal->id] ?? null) == $pilihan->id ? 'checked' : '' }}
                            class="w-4 h-4 accent-ink-500">
                        <span class="text-sm text-slate-700">{{ $pilihan->teks }}</span>
                    </label>
                    @endforeach
                </div>
                @else
                <div class="ml-10">
                    <textarea name="jawaban_essay[{{ $soal->id }}]" rows="4" placeholder="Tulis jawaban Anda di sini..."
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200 resize-none">{{ $jawabanSudah[$soal->id] ?? '' }}</textarea>
                </div>
                @endif
            </div>
            @endforeach
        </div>

        {{-- Navigasi soal --}}
        <div class="mt-6 bg-white rounded-2xl border border-slate-200 p-4">
            <p class="text-xs font-semibold text-slate-500 mb-3">Navigasi Soal</p>
            <div class="flex flex-wrap gap-2">
                @foreach($soalList as $idx => $ujianSoal)
                @php $soal = $ujianSoal->bankSoal; $sudahDijawab = isset($jawabanSudah[$soal->id]); @endphp
                <a href="#soal-{{ $idx+1 }}" class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-medium transition-colors
                    {{ $sudahDijawab ? 'bg-ink-500 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    {{ $idx+1 }}
                </a>
                @endforeach
            </div>
        </div>

        {{-- Submit --}}
        <div class="mt-4 flex justify-end">
            <button type="button" onclick="konfirmasiSelesai()"
                class="px-6 py-3 rounded-xl bg-sage-500 hover:bg-sage-600 text-white font-medium text-sm transition-colors shadow-sm">
                <i class="fa-solid fa-circle-check mr-1.5"></i>Selesai & Kumpulkan
            </button>
        </div>
    </form>
</div>

{{-- Konfirmasi modal --}}
<div id="modal-konfirmasi" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center">
        <div class="w-14 h-14 rounded-2xl bg-sage-50 flex items-center justify-center mx-auto mb-4">
            <i class="fa-solid fa-circle-check text-sage-500 text-2xl"></i>
        </div>
        <h3 class="font-semibold text-slate-800 mb-2">Kumpulkan Ujian?</h3>
        <p class="text-sm text-slate-500 mb-5">Pastikan semua soal sudah dijawab. Jawaban tidak bisa diubah setelah dikumpulkan.</p>
        <div class="flex gap-3">
            <button onclick="document.getElementById('modal-konfirmasi').classList.add('hidden')"
                class="flex-1 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50">Kembali</button>
            <button onclick="document.getElementById('form-ujian').submit()"
                class="flex-1 py-2.5 rounded-xl bg-sage-500 hover:bg-sage-600 text-white text-sm font-medium transition-colors">Kumpulkan</button>
        </div>
    </div>
</div>

<script>
    function konfirmasiSelesai() {
        document.getElementById('modal-konfirmasi').classList.remove('hidden');
    }

    // Timer
    let sisaDetik = {{ $sesi->sisa_waktu_detik }};
    const timerEl = document.getElementById('timer-text');
    const timerBox = document.getElementById('timer');

    function updateTimer() {
        if (sisaDetik <= 0) {
            timerEl.textContent = '00:00';
            document.getElementById('form-ujian').submit();
            return;
        }
        const m = Math.floor(sisaDetik / 60);
        const s = sisaDetik % 60;
        timerEl.textContent = String(m).padStart(2,'0') + ':' + String(s).padStart(2,'0');
        if (sisaDetik <= 300) {
            timerBox.classList.remove('bg-ink-50', 'text-ink-600');
            timerBox.classList.add('bg-red-50', 'text-red-600');
        }
        sisaDetik--;
    }
    updateTimer();
    setInterval(updateTimer, 1000);
</script>
</body>
</html>
