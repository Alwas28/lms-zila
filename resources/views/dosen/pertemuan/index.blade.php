<x-app-layout>
    <x-slot name="title">Pertemuan — {{ $kelas->mataKuliah->nama }}</x-slot>
    <x-slot name="pageTitle">Pertemuan</x-slot>

    @include('admin._flash')

    {{-- Breadcrumb + Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
        <div>
            <nav class="flex items-center gap-1.5 text-xs text-slate-400 mb-1">
                <a href="{{ route('dosen.mata-kuliah.index') }}" class="hover:text-ink-500 transition-colors">Mata Kuliah Saya</a>
                <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
                <span class="text-slate-600">{{ $kelas->mataKuliah->nama }}</span>
                <i class="fa-solid fa-chevron-right text-slate-300 text-xs"></i>
                <span class="text-ink-500 font-medium">Pertemuan</span>
            </nav>
            <h2 class="font-heading font-semibold text-slate-800 text-lg">
                {{ $kelas->mataKuliah->nama }}
                <span class="text-slate-400 font-normal text-sm">— Kelas {{ $kelas->nama_kelas }}</span>
            </h2>
        </div>
        <button onclick="openModal('modal-tambah')"
            class="inline-flex items-center gap-2 bg-ink-500 hover:bg-ink-600 text-white text-sm font-medium px-4 py-2.5 rounded-xl transition-colors flex-shrink-0">
            <i class="fa-solid fa-plus text-xs"></i> Tambah Pertemuan
        </button>
    </div>

    {{-- Aksi Navigasi Kelas --}}
    @php
    $navLinks = [
        ['route' => route('dosen.materi.index',    $kelas), 'icon' => 'folder-open',       'label' => 'Materi'],
        ['route' => route('dosen.rps.show',        $kelas), 'icon' => 'file-lines',         'label' => 'RPS'],
        ['route' => route('dosen.tugas.index',     $kelas), 'icon' => 'list-check',         'label' => 'Tugas'],
        ['route' => route('dosen.ujian.index',     $kelas), 'icon' => 'clipboard-question', 'label' => 'Ujian'],
        ['route' => route('dosen.presensi.index',  $kelas), 'icon' => 'user-check',         'label' => 'Presensi'],
        ['route' => route('dosen.penilaian.index', $kelas), 'icon' => 'star-half-stroke',   'label' => 'Penilaian'],
    ];
    @endphp
    <div class="flex flex-wrap gap-2 mb-5">
        @foreach($navLinks as $nav)
        <a href="{{ $nav['route'] }}"
            class="inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-600 hover:border-ink-300 hover:text-ink-600 transition-colors">
            <i class="fa-solid fa-{{ $nav['icon'] }}"></i> {{ $nav['label'] }}
        </a>
        @endforeach
    </div>

    {{-- Timeline Pertemuan --}}
    @php
        $pertemuanMap = $pertemuan->keyBy('nomor');
    @endphp

    <div class="space-y-3">
        @for($n = 1; $n <= 16; $n++)
        @php $p = $pertemuanMap->get($n); @endphp
        <div class="bg-white rounded-2xl border {{ $p && $p->status === 'selesai' ? 'border-sage-200' : 'border-slate-200' }} p-4 flex items-start gap-4 hover:shadow-sm transition-shadow">
            {{-- Nomor --}}
            <div class="flex-shrink-0 w-10 h-10 rounded-full {{ $p ? ($p->status === 'selesai' ? 'bg-sage-500' : 'bg-ink-500') : 'bg-slate-200' }} flex items-center justify-center text-white font-heading font-bold text-sm">
                {{ $p && $p->status === 'selesai' ? '✓' : $n }}
            </div>

            {{-- Content --}}
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2 mb-0.5">
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Pertemuan {{ $n }}</span>
                    @if($p)
                        @if($p->status === 'selesai')
                        <span class="text-xs bg-sage-50 text-sage-600 px-2 py-0.5 rounded-full font-medium">Selesai</span>
                        @else
                        <span class="text-xs bg-slate-100 text-slate-400 px-2 py-0.5 rounded-full font-medium">Draft</span>
                        @endif
                        @if($p->presensiSesi && $p->presensiSesi->is_aktif)
                        <span class="text-xs bg-ember-50 text-ember-600 px-2 py-0.5 rounded-full font-medium">
                            <i class="fa-solid fa-signal text-xs mr-0.5"></i> Presensi Aktif
                        </span>
                        @endif
                    @endif
                </div>
                <p class="font-medium text-slate-800 text-sm">{{ $p ? $p->topik : 'Belum diisi' }}</p>
                @if($p)
                <div class="flex flex-wrap gap-3 mt-1 text-xs text-slate-400">
                    @if($p->metode)
                    <span><i class="fa-solid fa-chalkboard mr-1"></i>{{ ucfirst($p->metode) }}</span>
                    @endif
                    @if($p->tanggal)
                    <span><i class="fa-solid fa-calendar mr-1"></i>{{ $p->tanggal->format('d M Y') }}</span>
                    @endif
                    <span><i class="fa-solid fa-file mr-1"></i>{{ $p->materi_count }} Materi</span>
                </div>
                @endif
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-1.5 flex-shrink-0">
                @if($p)
                <button onclick="openEditPertemuan({{ $p->id }}, {{ $p->nomor }}, '{{ addslashes($p->topik) }}', '{{ addslashes($p->deskripsi ?? '') }}', '{{ $p->metode }}', '{{ $p->tanggal ? $p->tanggal->format('Y-m-d') : '' }}')"
                    class="w-8 h-8 rounded-lg bg-ink-50 hover:bg-ink-100 text-ink-500 flex items-center justify-center transition-colors" title="Edit">
                    <i class="fa-solid fa-pen text-xs"></i>
                </button>
                @if($p->status === 'draft')
                <form action="{{ route('dosen.pertemuan.selesai', [$kelas, $p]) }}" method="POST"
                    onsubmit="return confirm('Tandai pertemuan {{ $n }} sebagai selesai?')">
                    @csrf @method('PATCH')
                    <button type="submit" class="w-8 h-8 rounded-lg bg-sage-50 hover:bg-sage-100 text-sage-500 flex items-center justify-center transition-colors" title="Tandai Selesai">
                        <i class="fa-solid fa-check text-xs"></i>
                    </button>
                </form>
                @endif
                <form action="{{ route('dosen.pertemuan.destroy', [$kelas, $p]) }}" method="POST"
                    onsubmit="return confirm('Hapus pertemuan {{ $n }}? Semua materi ikut terhapus.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 hover:bg-red-100 text-red-400 flex items-center justify-center transition-colors" title="Hapus">
                        <i class="fa-solid fa-trash-can text-xs"></i>
                    </button>
                </form>
                @else
                <button onclick="document.getElementById('input-nomor').value={{ $n }}; openModal('modal-tambah')"
                    class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-ink-50 hover:text-ink-500 text-slate-400 flex items-center justify-center transition-colors" title="Isi Pertemuan {{ $n }}">
                    <i class="fa-solid fa-plus text-xs"></i>
                </button>
                @endif
            </div>
        </div>
        @endfor
    </div>

    {{-- Modal Tambah Pertemuan --}}
    <div id="modal-tambah" class="hidden fixed inset-0 z-50 items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" onclick="closeModal('modal-tambah')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-heading font-semibold text-slate-800">Tambah Pertemuan</h3>
                <button onclick="closeModal('modal-tambah')" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <form action="{{ route('dosen.pertemuan.store', $kelas) }}" method="POST" class="px-6 py-5 space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">No. Pertemuan <span class="text-red-400">*</span></label>
                        <input type="number" id="input-nomor" name="nomor" min="1" max="16" required
                            value="{{ old('nomor') }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Tanggal <span class="text-red-400">*</span></label>
                        <input type="date" name="tanggal" required value="{{ old('tanggal') }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Topik <span class="text-red-400">*</span></label>
                    <input type="text" name="topik" required value="{{ old('topik') }}" placeholder="Topik pertemuan..."
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Metode <span class="text-red-400">*</span></label>
                    <select name="metode" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200">
                        <option value="">— Pilih Metode —</option>
                        <option value="ceramah" {{ old('metode')=='ceramah' ? 'selected' : '' }}>Ceramah</option>
                        <option value="diskusi" {{ old('metode')=='diskusi' ? 'selected' : '' }}>Diskusi</option>
                        <option value="praktikum" {{ old('metode')=='praktikum' ? 'selected' : '' }}>Praktikum</option>
                        <option value="presentasi" {{ old('metode')=='presentasi' ? 'selected' : '' }}>Presentasi</option>
                        <option value="studi kasus" {{ old('metode')=='studi kasus' ? 'selected' : '' }}>Studi Kasus</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Deskripsi</label>
                    <textarea name="deskripsi" rows="3" placeholder="Deskripsi pertemuan (opsional)..."
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200 resize-none">{{ old('deskripsi') }}</textarea>
                </div>
                <div class="flex gap-3 pt-1">
                    <button type="button" onclick="closeModal('modal-tambah')" class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl bg-ink-500 hover:bg-ink-600 text-white text-sm font-medium transition-colors">
                        <i class="fa-solid fa-plus mr-1.5 text-xs"></i>Tambah
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit Pertemuan --}}
    <div id="modal-edit" class="hidden fixed inset-0 z-50 items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" onclick="closeModal('modal-edit')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-heading font-semibold text-slate-800">Edit Pertemuan</h3>
                <button onclick="closeModal('modal-edit')" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <form id="form-edit-pertemuan" method="POST" class="px-6 py-5 space-y-4">
                @csrf @method('PATCH')
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">No. Pertemuan <span class="text-red-400">*</span></label>
                        <input type="number" id="edit-nomor" name="nomor" min="1" max="16" required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Tanggal <span class="text-red-400">*</span></label>
                        <input type="date" id="edit-tanggal" name="tanggal" required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Topik <span class="text-red-400">*</span></label>
                    <input type="text" id="edit-topik" name="topik" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Metode <span class="text-red-400">*</span></label>
                    <select id="edit-metode" name="metode" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200">
                        <option value="ceramah">Ceramah</option>
                        <option value="diskusi">Diskusi</option>
                        <option value="praktikum">Praktikum</option>
                        <option value="presentasi">Presentasi</option>
                        <option value="studi kasus">Studi Kasus</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Deskripsi</label>
                    <textarea id="edit-deskripsi" name="deskripsi" rows="3"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200 resize-none"></textarea>
                </div>
                <div class="flex gap-3 pt-1">
                    <button type="button" onclick="closeModal('modal-edit')" class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl bg-ink-500 hover:bg-ink-600 text-white text-sm font-medium transition-colors">
                        <i class="fa-solid fa-floppy-disk mr-1.5 text-xs"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function openModal(id) {
            const el = document.getElementById(id);
            el.classList.remove('hidden');
            el.classList.add('flex');
        }
        function closeModal(id) {
            const el = document.getElementById(id);
            el.classList.add('hidden');
            el.classList.remove('flex');
        }
        function openEditPertemuan(id, nomor, topik, deskripsi, metode, tanggal) {
            document.getElementById('form-edit-pertemuan').action = '/dosen/kelas/{{ $kelas->id }}/pertemuan/' + id;
            document.getElementById('edit-nomor').value    = nomor;
            document.getElementById('edit-topik').value    = topik;
            document.getElementById('edit-deskripsi').value = deskripsi;
            document.getElementById('edit-metode').value   = metode;
            document.getElementById('edit-tanggal').value  = tanggal;
            openModal('modal-edit');
        }
        @if($errors->any()) openModal('modal-tambah'); @endif
    </script>
    @endpush
</x-app-layout>
