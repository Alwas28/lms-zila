<x-app-layout>
    <x-slot name="title">Materi &mdash; {{ $kelas->mataKuliah->nama }}</x-slot>
    <x-slot name="pageTitle">Materi Kuliah</x-slot>

    @include('admin._flash')

    {{-- Breadcrumb + Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
        <div>
            <nav class="flex items-center gap-1.5 text-xs text-slate-400 mb-1">
                <a href="{{ route('dosen.mata-kuliah.index') }}" class="hover:text-ink-500 transition-colors">Mata Kuliah Saya</a>
                <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
                <span class="text-slate-600">{{ $kelas->mataKuliah->nama }}</span>
                <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
                <span class="text-ink-500 font-medium">Materi</span>
            </nav>
            <h2 class="font-heading font-semibold text-slate-800 text-lg">
                {{ $kelas->mataKuliah->nama }}
                <span class="text-slate-400 font-normal text-sm">&mdash; Kelas {{ $kelas->nama_kelas }}</span>
            </h2>
        </div>
        <button onclick="openModal('modal-upload')"
            class="inline-flex items-center gap-2 bg-ink-500 hover:bg-ink-600 text-white text-sm font-medium px-4 py-2.5 rounded-xl transition-colors flex-shrink-0">
            <i class="fa-solid fa-upload text-xs"></i> Upload Materi
        </button>
    </div>

    {{-- Aksi Navigasi --}}
    <div class="flex flex-wrap gap-2 mb-5">
        <a href="{{ route('dosen.pertemuan.index', $kelas) }}" class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-600 hover:border-ink-300 hover:text-ink-600 transition-colors">
            <i class="fa-solid fa-calendar-days"></i> Pertemuan
        </a>
        <a href="{{ route('dosen.tugas.index', $kelas) }}" class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-600 hover:border-ink-300 hover:text-ink-600 transition-colors">
            <i class="fa-solid fa-list-check"></i> Tugas
        </a>
    </div>

    {{-- Pertemuan Sections --}}
    @forelse($pertemuan as $p)
    <div class="bg-white rounded-2xl border border-slate-200 mb-4 overflow-hidden">
        {{-- Section Header --}}
        <div class="flex items-center justify-between px-5 py-4 bg-slate-50 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-ink-500 flex items-center justify-center text-white font-bold text-sm font-heading">
                    {{ $p->nomor }}
                </div>
                <div>
                    <p class="font-medium text-slate-800 text-sm">{{ $p->topik ?: 'Belum ada topik' }}</p>
                    <p class="text-xs text-slate-400">{{ $p->materi->count() }} materi</p>
                </div>
            </div>
            <button onclick="setPertemuan({{ $p->id }}); openModal('modal-upload')"
                class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg bg-ink-50 hover:bg-ink-100 text-ink-600 transition-colors">
                <i class="fa-solid fa-plus text-xs"></i> Upload
            </button>
        </div>

        {{-- Materi List --}}
        @if($p->materi->isEmpty())
        <div class="px-5 py-8 text-center text-sm text-slate-400">
            <i class="fa-solid fa-folder-open text-slate-200 text-2xl mb-2 block"></i>
            Belum ada materi untuk pertemuan ini
        </div>
        @else
        <div class="divide-y divide-slate-50">
            @foreach($p->materi as $m)
            <div class="flex items-center gap-3 px-5 py-3.5 hover:bg-slate-50 transition-colors">
                {{-- Icon --}}
                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0
                    @if($m->tipe === 'pdf') bg-red-50 text-red-500
                    @elseif($m->tipe === 'ppt') bg-orange-50 text-orange-500
                    @elseif($m->tipe === 'video') bg-purple-50 text-purple-500
                    @elseif($m->tipe === 'audio') bg-blue-50 text-blue-500
                    @elseif($m->tipe === 'youtube') bg-red-50 text-red-600
                    @elseif($m->tipe === 'website') bg-slate-100 text-slate-500
                    @else bg-slate-100 text-slate-500 @endif">
                    <i class="fa-solid {{ $m->icon }} text-sm"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-800 truncate">{{ $m->judul }}</p>
                    <div class="flex items-center gap-2 mt-0.5">
                        <span class="text-xs text-slate-400 uppercase">{{ $m->tipe }}</span>
                        @if($m->is_wajib)
                        <span class="text-xs bg-ember-50 text-ember-600 px-1.5 py-0.5 rounded font-medium">Wajib</span>
                        @else
                        <span class="text-xs bg-slate-100 text-slate-400 px-1.5 py-0.5 rounded font-medium">Opsional</span>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-1.5 flex-shrink-0">
                    @if($m->url)
                    <a href="{{ $m->url }}" target="_blank" class="w-8 h-8 rounded-lg bg-ink-50 hover:bg-ink-100 text-ink-500 flex items-center justify-center transition-colors" title="Buka Link">
                        <i class="fa-solid fa-external-link text-xs"></i>
                    </a>
                    @elseif($m->file_path)
                    <a href="{{ asset('storage/' . $m->file_path) }}" target="_blank" class="w-8 h-8 rounded-lg bg-ink-50 hover:bg-ink-100 text-ink-500 flex items-center justify-center transition-colors" title="Unduh">
                        <i class="fa-solid fa-download text-xs"></i>
                    </a>
                    @endif
                    <form action="{{ route('dosen.materi.destroy', [$kelas, $m]) }}" method="POST"
                        onsubmit="return confirm('Hapus materi {{ addslashes($m->judul) }}?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 hover:bg-red-100 text-red-400 flex items-center justify-center transition-colors" title="Hapus">
                            <i class="fa-solid fa-trash-can text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
    @empty
    <div class="bg-white rounded-2xl border border-slate-200 p-16 text-center">
        <i class="fa-solid fa-folder-open text-slate-200 text-4xl mb-3 block"></i>
        <p class="text-slate-400 text-sm">Belum ada pertemuan. Buat pertemuan terlebih dahulu.</p>
        <a href="{{ route('dosen.pertemuan.index', $kelas) }}" class="inline-flex items-center gap-2 mt-4 bg-ink-500 hover:bg-ink-600 text-white text-sm font-medium px-4 py-2 rounded-xl transition-colors">
            <i class="fa-solid fa-calendar-days text-xs"></i> Kelola Pertemuan
        </a>
    </div>
    @endforelse

    {{-- Modal Upload Materi --}}
    <div id="modal-upload" class="hidden fixed inset-0 z-50 items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" onclick="closeModal('modal-upload')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-heading font-semibold text-slate-800">Upload Materi</h3>
                <button onclick="closeModal('modal-upload')" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <form action="{{ route('dosen.materi.store', $kelas) }}" method="POST" enctype="multipart/form-data" class="px-6 py-5 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Pertemuan <span class="text-red-400">*</span></label>
                    <select id="select-pertemuan" name="pertemuan_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200">
                        <option value="">&mdash; Pilih Pertemuan &mdash;</option>
                        @foreach($pertemuan as $p)
                        <option value="{{ $p->id }}">Pertemuan {{ $p->nomor }} &mdash; {{ $p->topik ?: 'Belum ada topik' }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Judul <span class="text-red-400">*</span></label>
                    <input type="text" name="judul" required placeholder="Judul materi..."
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Tipe <span class="text-red-400">*</span></label>
                    <select id="tipe-materi" name="tipe" required onchange="toggleInputMateri(this.value)"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200">
                        <option value="">&mdash; Pilih Tipe &mdash;</option>
                        <optgroup label="File Upload">
                            <option value="pdf">PDF</option>
                            <option value="ppt">PowerPoint (PPT)</option>
                            <option value="video">Video</option>
                            <option value="audio">Audio</option>
                            <option value="dokumen">Dokumen</option>
                            <option value="lainnya">Lainnya</option>
                        </optgroup>
                        <optgroup label="Link/URL">
                            <option value="youtube">YouTube</option>
                            <option value="website">Website/Link</option>
                        </optgroup>
                    </select>
                </div>
                <div id="input-file" class="hidden">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">File <span class="text-red-400">*</span></label>
                    <input type="file" name="file"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200 bg-slate-50">
                </div>
                <div id="input-url" class="hidden">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">URL <span class="text-red-400">*</span></label>
                    <input type="url" name="url" placeholder="https://..."
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200">
                </div>
                <div class="flex items-center gap-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_wajib" value="1" class="w-4 h-4 rounded border-slate-300 text-ink-500 focus:ring-ink-200">
                        <span class="text-sm font-medium text-slate-700">Materi Wajib</span>
                    </label>
                </div>
                <div class="flex gap-3 pt-1">
                    <button type="button" onclick="closeModal('modal-upload')" class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl bg-ink-500 hover:bg-ink-600 text-white text-sm font-medium transition-colors">
                        <i class="fa-solid fa-upload mr-1.5 text-xs"></i>Upload
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function openModal(id) { const el = document.getElementById(id); el.classList.remove('hidden'); el.classList.add('flex'); }
        function closeModal(id) { const el = document.getElementById(id); el.classList.add('hidden'); el.classList.remove('flex'); }
        function setPertemuan(id) {
            document.getElementById('select-pertemuan').value = id;
        }
        function toggleInputMateri(tipe) {
            const fileTypes = ['pdf', 'ppt', 'video', 'audio', 'dokumen', 'lainnya'];
            const urlTypes  = ['youtube', 'website'];
            document.getElementById('input-file').classList.toggle('hidden', !fileTypes.includes(tipe));
            document.getElementById('input-url').classList.toggle('hidden', !urlTypes.includes(tipe));
        }
        @if($errors->any()) openModal('modal-upload'); @endif
    </script>
    @endpush
</x-app-layout>
