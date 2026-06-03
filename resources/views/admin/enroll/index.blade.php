<x-app-layout>
    <x-slot name="title">Enroll Mahasiswa</x-slot>
    <x-slot name="pageTitle">Enroll Mahasiswa</x-slot>

    @include('admin._flash')

    {{-- Filter Semester & Kelas --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-5 mb-5">
        <p class="text-sm font-medium text-slate-700 mb-3">Pilih Kelas</p>
        <div class="flex flex-wrap gap-3">
            <form method="GET" action="{{ route('admin.enroll.index') }}" class="flex items-center gap-2">
                <label class="text-sm text-slate-500 flex-shrink-0">Semester:</label>
                <select name="semester_id" onchange="this.form.submit()"
                    class="px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200 bg-white text-slate-700">
                    <option value="">Semua</option>
                    @foreach($semua as $sm)
                    <option value="{{ $sm->id }}" {{ $semesterId == $sm->id ? 'selected' : '' }}>
                        Semester {{ ucfirst($sm->tipe) }} &mdash; {{ $sm->tahunAkademik->nama }}{{ $sm->is_aktif ? ' âœ“' : '' }}
                    </option>
                    @endforeach
                </select>
            </form>

            <form method="GET" action="{{ route('admin.enroll.index') }}" class="flex items-center gap-2 flex-1 min-w-[200px]">
                <input type="hidden" name="semester_id" value="{{ $semesterId }}">
                <label class="text-sm text-slate-500 flex-shrink-0">Kelas:</label>
                <select name="kelas_id" onchange="this.form.submit()"
                    class="flex-1 px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200 bg-white text-slate-700">
                    <option value="">&mdash; Pilih Kelas &mdash;</option>
                    @foreach($semuaKelas as $k)
                    <option value="{{ $k->id }}" {{ $kelasId == $k->id ? 'selected' : '' }}>
                        {{ $k->mataKuliah->kode }} &mdash; {{ $k->mataKuliah->nama }} ({{ $k->nama_kelas }})
                    </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    @if($kelasAktif)
    {{-- Info Kelas --}}
    <div class="bg-gradient-to-r from-ink-500 to-ink-700 rounded-2xl p-4 md:p-5 mb-5 text-white flex items-center justify-between flex-wrap gap-3">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center font-heading font-bold text-lg">
                {{ $kelasAktif->nama_kelas }}
            </div>
            <div>
                <p class="font-heading font-semibold">{{ $kelasAktif->mataKuliah->nama }}</p>
                <p class="text-ink-200 text-sm">{{ ucfirst($kelasAktif->semester->tipe) }} {{ $kelasAktif->semester->tahunAkademik->nama }}</p>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <div class="text-center">
                <p class="text-2xl font-heading font-bold">{{ $peserta->total() }}</p>
                <p class="text-ink-200 text-xs">Terdaftar</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-heading font-bold">{{ $kelasAktif->kapasitas }}</p>
                <p class="text-ink-200 text-xs">Kapasitas</p>
            </div>
            <div class="text-center">
                <p class="text-2xl font-heading font-bold {{ $kelasAktif->kapasitas - $peserta->total() <= 0 ? 'text-red-300' : '' }}">
                    {{ max(0, $kelasAktif->kapasitas - $peserta->total()) }}
                </p>
                <p class="text-ink-200 text-xs">Sisa Slot</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        {{-- Toolbar --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-5 py-4 border-b border-slate-100">
            <form method="GET" action="{{ route('admin.enroll.index') }}" class="flex-1 max-w-sm">
                <input type="hidden" name="semester_id" value="{{ $semesterId }}">
                <input type="hidden" name="kelas_id" value="{{ $kelasId }}">
                <div class="relative">
                    <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau NIM..."
                        class="w-full pl-9 pr-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200 text-slate-700 placeholder:text-slate-400">
                </div>
            </form>
            <div class="flex items-center gap-2 flex-shrink-0">
                <label for="importEnroll"
                    class="inline-flex items-center gap-2 bg-sage-50 hover:bg-sage-100 text-sage-700 text-sm font-medium px-4 py-2.5 rounded-xl transition-colors cursor-pointer border border-sage-200">
                    <i class="fa-solid fa-file-excel text-sage-600 text-xs"></i>Import Excel
                </label>
                <input type="file" id="importEnroll" accept=".xlsx,.xls,.csv" class="hidden"
                    onchange="if(this.files[0]) alert('Import dari \''+this.files[0].name+'\' belum dikonfigurasi.');">
                <button onclick="openModal('modal-tambah')"
                    class="inline-flex items-center gap-2 bg-ink-500 hover:bg-ink-600 text-white text-sm font-medium px-4 py-2.5 rounded-xl transition-colors">
                    <i class="fa-solid fa-user-plus text-xs"></i>Tambah Mahasiswa
                </button>
            </div>
        </div>

        {{-- Table peserta --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide w-8">#</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Nama Mahasiswa</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden md:table-cell">NIM</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden lg:table-cell">Email</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide hidden lg:table-cell">Tgl Enroll</th>
                        <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide text-center w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($peserta as $mhs)
                    @php $enroll = \App\Models\Enrollment::where(['kelas_id'=>$kelasAktif->id,'user_id'=>$mhs->id])->first(); @endphp
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-3.5 text-slate-400 text-xs">
                            {{ ($peserta->currentPage()-1)*$peserta->perPage()+$loop->iteration }}
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-sage-100 flex items-center justify-center text-sage-600 font-semibold text-xs flex-shrink-0">
                                    {{ strtoupper(substr($mhs->name,0,1)) }}
                                </div>
                                <p class="font-medium text-slate-800 truncate">{{ $mhs->name }}</p>
                            </div>
                        </td>
                        <td class="px-4 py-3.5 hidden md:table-cell">
                            <span class="font-mono text-xs bg-slate-100 px-2 py-1 rounded-lg">{{ $mhs->nip_nim ?? '&mdash;' }}</span>
                        </td>
                        <td class="px-4 py-3.5 text-slate-400 text-xs hidden lg:table-cell">{{ $mhs->email }}</td>
                        <td class="px-4 py-3.5 text-slate-400 text-xs hidden lg:table-cell">
                            {{ $mhs->pivot->created_at->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center justify-center gap-1.5">
                                {{-- Pindah Kelas --}}
                                @if($enroll)
                                <button onclick="openPindah({{ $enroll->id }}, '{{ addslashes($mhs->name) }}')"
                                    class="w-8 h-8 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-500 flex items-center justify-center transition-colors" title="Pindah Kelas">
                                    <i class="fa-solid fa-right-left text-xs"></i>
                                </button>
                                {{-- Hapus --}}
                                <form action="{{ route('admin.enroll.destroy', $enroll) }}" method="POST"
                                    onsubmit="return confirm('Hapus {{ addslashes($mhs->name) }} dari kelas ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 hover:bg-red-100 text-red-400 flex items-center justify-center transition-colors" title="Hapus">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-14 text-center">
                            <i class="fa-solid fa-user-graduate text-slate-200 text-4xl mb-3 block"></i>
                            <p class="text-slate-400 text-sm">
                                @if(request('search')) Tidak ada mahasiswa yang cocok.
                                @else Belum ada mahasiswa terdaftar di kelas ini.
                                @endif
                            </p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($peserta->hasPages())
        <div class="px-5 py-4 border-t border-slate-100 flex items-center justify-between text-sm text-slate-500">
            <span>{{ $peserta->firstItem() }}"“{{ $peserta->lastItem() }} dari {{ $peserta->total() }}</span>
            <div class="flex gap-1">
                @if(!$peserta->onFirstPage())<a href="{{ $peserta->previousPageUrl() }}" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-ink-50 hover:text-ink-500 flex items-center justify-center"><i class="fa-solid fa-chevron-left text-xs"></i></a>@endif
                @foreach($peserta->getUrlRange(max(1,$peserta->currentPage()-2),min($peserta->lastPage(),$peserta->currentPage()+2)) as $page=>$url)
                <a href="{{ $url }}" class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-medium {{ $page==$peserta->currentPage() ? 'bg-ink-500 text-white' : 'bg-slate-100 hover:bg-ink-50 hover:text-ink-500 text-slate-600' }}">{{ $page }}</a>
                @endforeach
                @if($peserta->hasMorePages())<a href="{{ $peserta->nextPageUrl() }}" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-ink-50 hover:text-ink-500 flex items-center justify-center"><i class="fa-solid fa-chevron-right text-xs"></i></a>@endif
            </div>
        </div>
        @endif
    </div>

    @else
    {{-- Empty state: belum pilih kelas --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-16 text-center">
        <div class="w-16 h-16 rounded-2xl bg-ink-50 flex items-center justify-center mx-auto mb-4">
            <i class="fa-solid fa-user-plus text-ink-400 text-2xl"></i>
        </div>
        <h3 class="font-heading font-semibold text-slate-700 mb-1">Pilih Kelas Terlebih Dahulu</h3>
        <p class="text-sm text-slate-400">Gunakan filter di atas untuk memilih semester dan kelas yang ingin dikelola.</p>
    </div>
    @endif

    {{-- Modal Tambah Mahasiswa ke Kelas --}}
    <div id="modal-tambah" class="hidden fixed inset-0 z-50 items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" onclick="closeModal('modal-tambah')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-heading font-semibold text-slate-800">Tambah Mahasiswa</h3>
                    @if($kelasAktif)
                    <p class="text-xs text-slate-400 mt-0.5">{{ $kelasAktif->nama_lengkap }}</p>
                    @endif
                </div>
                <button onclick="closeModal('modal-tambah')" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <form action="{{ route('admin.enroll.store') }}" method="POST" class="px-6 py-5 space-y-4">
                @csrf
                <input type="hidden" name="kelas_id" value="{{ $kelasId }}">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Pilih Mahasiswa <span class="text-red-400">*</span></label>
                    @if($kelasAktif && $mahasiswaBelumEnroll->count() > 0)
                    <select name="user_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200">
                        <option value="">&mdash; Pilih Mahasiswa &mdash;</option>
                        @foreach($mahasiswaBelumEnroll as $m)
                        <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->nip_nim }})</option>
                        @endforeach
                    </select>
                    @else
                    <p class="text-xs text-amber-600 bg-amber-50 rounded-xl px-3 py-2.5">
                        <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                        Semua mahasiswa sudah terdaftar atau belum ada mahasiswa.
                    </p>
                    @endif
                </div>
                <div class="flex gap-3 pt-1">
                    <button type="button" onclick="closeModal('modal-tambah')" class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl bg-ink-500 hover:bg-ink-600 text-white text-sm font-medium transition-colors">
                        <i class="fa-solid fa-user-plus mr-1.5 text-xs"></i>Daftarkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Pindah Kelas --}}
    <div id="modal-pindah" class="hidden fixed inset-0 z-50 items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" onclick="closeModal('modal-pindah')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-heading font-semibold text-slate-800">Pindah Kelas</h3>
                    <p id="pindah-nama" class="text-xs text-slate-400 mt-0.5"></p>
                </div>
                <button onclick="closeModal('modal-pindah')" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <form id="form-pindah" method="POST" class="px-6 py-5 space-y-4">
                @csrf @method('PATCH')
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Kelas Tujuan <span class="text-red-400">*</span></label>
                    <select name="kelas_tujuan_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-ink-200">
                        <option value="">&mdash; Pilih Kelas Tujuan &mdash;</option>
                        @foreach($semuaKelas as $k)
                        @if($k->id != $kelasId)
                        <option value="{{ $k->id }}">{{ $k->mataKuliah->kode }} &mdash; {{ $k->mataKuliah->nama }} ({{ $k->nama_kelas }})</option>
                        @endif
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-3 pt-1">
                    <button type="button" onclick="closeModal('modal-pindah')" class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium transition-colors">
                        <i class="fa-solid fa-right-left mr-1.5 text-xs"></i>Pindahkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function openModal(id) { const el = document.getElementById(id); el.classList.remove('hidden'); el.classList.add('flex'); }
        function closeModal(id) { const el = document.getElementById(id); el.classList.add('hidden'); el.classList.remove('flex'); }
        function openPindah(enrollId, nama) {
            document.getElementById('form-pindah').action = '/admin/enroll/' + enrollId + '/pindah';
            document.getElementById('pindah-nama').textContent = nama;
            openModal('modal-pindah');
        }
    </script>
    @endpush
</x-app-layout>
