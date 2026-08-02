{{-- resources/views/workboard/index.blade.php --}}
<x-app-layout>
    <div class="min-h-screen bg-gray-50 -m-6 p-6" x-data="workboardOffcanvas()">
        <div class="max-w-[1200px] mx-auto pb-8">

            {{-- Header --}}
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                <div>
                    <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight flex items-center gap-2">
                        <i data-lucide="monitor-play" class="w-6 h-6 text-blue-600"></i> Workboard
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">Pantau progres pengerjaan sesi foto klien.</p>
                </div>

                @if(session('success'))
                    <div class="bg-emerald-50 text-emerald-600 px-4 py-2 rounded-xl text-sm font-semibold border border-emerald-200 flex items-center gap-2 shadow-sm" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
                        <i data-lucide="check-circle-2" class="w-4 h-4"></i> {{ session('success') }}
                    </div>
                @endif
            </div>

            {{-- Stat Cards --}}
            @php
                $stats = [
                    'semua'            => ['label' => 'Semua',            'desc' => 'Semua stage aktif',        'color' => 'blue'],
                    'belum_upload'     => ['label' => 'Belum Upload',     'desc' => 'Menunggu file original',   'color' => 'gray'],
                    'belum_pilih_foto' => ['label' => 'Belum Pilih Foto', 'desc' => 'File ada, klien belum pilih','color' => 'orange'],
                    'seleksi_masuk'    => ['label' => 'Seleksi Masuk',    'desc' => 'Klien sudah submit pilihan','color' => 'amber'],
                    'sedang_diedit'    => ['label' => 'Sedang Diedit',    'desc' => 'Status booking: editing',   'color' => 'purple'],
                    'terkirim'         => ['label' => 'Terkirim',         'desc' => 'Selesai dikerjakan',        'color' => 'emerald'],
                ];
                $colorText = [
                    'blue' => 'text-blue-600', 'gray' => 'text-gray-700', 'orange' => 'text-orange-500',
                    'amber' => 'text-amber-500', 'purple' => 'text-purple-600', 'emerald' => 'text-emerald-600',
                ];
            @endphp
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-5">
                @foreach($stats as $key => $s)
                    <a href="{{ route('workboard.index', ['tab' => $key]) }}"
                       class="rounded-xl border p-4 transition shadow-sm {{ $tab === $key ? 'border-blue-400 bg-blue-50' : 'border-gray-200 bg-white hover:border-gray-300 hover:shadow-md' }}">
                        <p class="text-2xl font-extrabold {{ $colorText[$s['color']] }}">{{ $counts[$key] ?? 0 }}</p>
                        <p class="text-sm font-bold text-gray-900 mt-1">{{ $s['label'] }}</p>
                        <p class="text-[11px] text-gray-500 mt-0.5">{{ $s['desc'] }}</p>
                    </a>
                @endforeach
            </div>

            {{-- Search Bar --}}
            <div class="flex items-center gap-3 mb-3">
                <div class="flex-1 flex items-center gap-2 bg-white border border-gray-200 rounded-xl px-4 h-11 shadow-sm focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500 transition">
                    <i data-lucide="search" class="w-4 h-4 text-gray-400"></i>
                    <input type="text" placeholder="Cari nama klien atau kode booking..." class="flex-1 bg-transparent text-sm text-gray-900 placeholder-gray-400 focus:outline-none w-full border-none focus:ring-0 px-0">
                </div>
            </div>

            <div class="flex items-center justify-between mb-4 text-[12px] text-gray-500">
                <span>{{ $bookings->count() ? $bookings->firstItem() . '-' . $bookings->lastItem() . ' dari ' . $bookings->total() : '0' }} booking</span>
            </div>

            {{-- Card Grid --}}
            @php
                $statusUi = [
                    'Dijadwalkan'             => ['label' => 'Belum Upload',     'badge' => 'bg-gray-100 text-gray-600',    'desc' => 'Menunggu upload file original'],
                    'File Original Disiapkan'  => ['label' => 'Belum Pilih Foto', 'badge' => 'bg-orange-50 text-orange-600', 'desc' => 'Menunggu klien memilih foto'],
                    'Pilih Foto'               => ['label' => 'Seleksi Masuk',    'badge' => 'bg-amber-50 text-amber-600',    'desc' => 'Klien sudah submit pilihan'],
                    'Pilihan Diterima'         => ['label' => 'Seleksi Masuk',    'badge' => 'bg-amber-50 text-amber-600',    'desc' => 'Klien sudah submit pilihan'],
                    'Proses Edit'              => ['label' => 'Sedang Diedit',    'badge' => 'bg-purple-50 text-purple-600', 'desc' => 'Sedang dalam proses editing'],
                    'Selesai'                  => ['label' => 'Terkirim',         'badge' => 'bg-emerald-50 text-emerald-600','desc' => 'Hasil akhir sudah dikirim'],
                ];
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @forelse($bookings as $b)
                    @php 
                        $ui = $statusUi[$b->status] ?? $statusUi['Dijadwalkan']; 
                        $bookingCode = 'BKG-' . \Carbon\Carbon::parse($b->created_at)->format('Ymd') . '-' . strtoupper(substr(md5($b->id), 0, 4));
                        $onlyDate = \Carbon\Carbon::parse($b->booking_date ?? $b->start_date)->format('Y-m-d');
                        $dateStr = \Carbon\Carbon::parse($onlyDate)->format('d M Y');
                        
                        $limitFoto = $b->serviceType->photo_limit ? $b->serviceType->photo_limit : '30';
                        $deadlineDb = $b->deadline_pilih ? \Carbon\Carbon::parse($b->deadline_pilih) : \Carbon\Carbon::parse($onlyDate)->addDays(7);
                        $deadlineStr = $deadlineDb->format('d M Y');
                        $sisaHari = max(0, \Carbon\Carbon::now()->startOfDay()->diffInDays($deadlineDb->startOfDay(), false));

                        $companySetting = DB::table('company_settings')->where('user_id', $b->user_id)->first();
                        $companyName = $companySetting->company_name ?? $b->user->name ?? 'Studio Foto';

                        $selectedPhotosArr = json_decode($b->selected_photos ?? '[]');
                        $totalSelected = count($selectedPhotosArr);
                        $selectedPhotosJson = json_encode($selectedPhotosArr);
                    @endphp
                    
                    <div @click="openSidebar({
                            id: {{ $b->id }},
                            status: '{{ $b->status }}',
                            statusLabel: '{{ $ui['label'] }}',
                            linkFolder: '{{ $b->link_folder_kerja ?? '' }}',
                            linkOriginal: '{{ $b->link_original ?? '' }}',
                            clientName: '{{ addslashes($b->client_name) }}',
                            bookingCode: '{{ $bookingCode }}',
                            packageName: '{{ addslashes($b->serviceType->name ?? 'Layanan') }}',
                            photoLimit: '{{ $limitFoto }}',
                            date: '{{ $dateStr }}',
                            deadline: '{{ $deadlineStr }}',
                            deadlineRaw: '{{ $deadlineDb->format('Y-m-d') }}',
                            sisaHari: {{ $sisaHari }},
                            companyName: '{{ addslashes($companyName) }}',
                            wa: '{{ $b->client_contact }}',
                            ig: '{{ $b->client_instagram }}',
                            totalSelected: {{ $totalSelected }},
                            selectedPhotos: {{ $selectedPhotosJson }},
                            clientNotes: '{{ addslashes($b->client_notes ?? '') }}'
                        })" 
                         class="bg-white border border-gray-200 rounded-xl p-5 flex flex-col h-full shadow-sm hover:shadow-md hover:border-blue-300 cursor-pointer transition">
                        
                        <div class="mb-4 flex justify-between items-center">
                            <span class="inline-block {{ $ui['badge'] }} text-[11px] font-bold px-2.5 py-1 rounded-md">
                                {{ $ui['label'] }}
                            </span>
                            @if($b->status === 'Pilihan Diterima' && $totalSelected > 0)
                                <span class="bg-emerald-100 text-emerald-800 text-[10px] font-extrabold px-2 py-0.5 rounded-full">
                                    {{ $totalSelected }} Foto Dipilih
                                </span>
                            @endif
                        </div>

                        <h3 class="font-extrabold text-gray-900 text-[16px] leading-tight mb-0.5">{{ $b->client_name }}</h3>
                        <p class="text-[12px] font-mono text-gray-400 font-semibold mb-3">{{ $bookingCode }}</p>

                        <div>
                            <span class="inline-block bg-gray-50 border border-gray-200 text-gray-600 text-[11px] font-bold px-2.5 py-1 rounded-lg">
                                {{ $b->serviceType->name ?? 'Paket Layanan' }}
                            </span>
                        </div>

                        <p class="text-[12px] text-gray-500 font-medium mt-3 flex items-center gap-1.5">
                            <i data-lucide="calendar" class="w-3.5 h-3.5 text-gray-400"></i>
                            {{ $dateStr }}
                        </p>
                    </div>
                @empty
                    <div class="col-span-full py-16 text-center text-gray-400">
                        <p class="font-medium text-sm text-gray-500">Belum ada data pengerjaan di tahap ini.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $bookings->links() }}
            </div>

        </div>

        {{-- OFFCANVAS SIDEBAR KANAN --}}
        <div class="fixed inset-0 z-[100] overflow-hidden" x-show="isOpen" x-cloak>
            <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="isOpen = false"></div>

            <div class="absolute inset-y-0 right-0 max-w-md w-full flex">
                <div class="w-full h-full bg-white shadow-2xl flex flex-col transform transition-transform duration-300 ease-in-out"
                     x-show="isOpen">
                    
                    <form :action="`/workboard/${activeData.id}/update`" method="POST" class="flex flex-col h-full relative">
                        @csrf

                        {{-- Header Sidebar --}}
                        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-start shrink-0">
                            <div>
                                <span class="inline-block text-[10px] font-bold px-2 py-0.5 rounded-md mb-2 bg-amber-50 text-amber-600" x-text="activeData.statusLabel"></span>
                                <h2 class="text-xl font-extrabold text-gray-900 leading-tight" x-text="activeData.clientName"></h2>
                                <p class="text-[13px] font-mono text-gray-500 mt-1" x-text="activeData.bookingCode"></p>
                            </div>
                            <button type="button" @click="isOpen = false" class="text-gray-400 hover:text-gray-700 bg-gray-50 p-2 rounded-full transition">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </div>

                        {{-- Body Sidebar --}}
                        <div class="p-6 overflow-y-auto flex-1 space-y-6">
                            
                            {{-- Info Klien Grid --}}
                            <div class="grid grid-cols-2 gap-y-4 gap-x-4">
                                <div>
                                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Paket</p>
                                    <p class="text-[13px] font-semibold text-gray-900" x-text="activeData.packageName"></p>
                                </div>
                                <div>
                                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Tanggal Sesi</p>
                                    <p class="text-[13px] font-semibold text-gray-900" x-text="activeData.date"></p>
                                </div>
                                <div>
                                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">WhatsApp</p>
                                    <a :href="'https://wa.me/' + activeData.wa.replace(/[^0-9]/g, '')" target="_blank" class="text-[13px] font-semibold text-blue-600 hover:underline" x-text="activeData.wa"></a>
                                </div>
                                <div>
                                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Instagram</p>
                                    <span class="text-[13px] font-semibold text-gray-900" x-text="activeData.ig || '-'"></span>
                                </div>
                            </div>

                            {{-- LINK FOLDER KERJA (DI ATAS SEBELUM FOTO) --}}
                            <div>
                                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Folder Kerja / Master Folder</p>
                                <div x-show="activeData.linkFolder !== ''">
                                    <a :href="activeData.linkFolder" target="_blank" class="text-[13px] text-blue-600 hover:underline break-all block" x-text="activeData.linkFolder"></a>
                                </div>
                                <div x-show="activeData.linkFolder === ''" class="text-[13px] text-gray-400 italic">Belum diset</div>
                            </div>

                            <hr class="border-gray-100">

                            {{-- SECTION FOTO DIPILIH KLIEN --}}
                            <div x-show="activeData.status === 'Pilihan Diterima'" class="space-y-4">
                                <div class="flex justify-between items-center bg-amber-50/70 border border-amber-200 rounded-2xl p-4">
                                    <div>
                                        <p class="text-[11px] font-bold text-amber-800 uppercase tracking-wider mb-0.5">Foto Dipilih Klien</p>
                                        <p class="text-2xl font-extrabold text-amber-900" x-text="activeData.totalSelected"></p>
                                    </div>
                                    <button type="button" @click="copyAllPhotos()" class="bg-white border border-amber-300 hover:bg-amber-100 text-amber-900 font-bold text-[12px] px-3.5 py-2 rounded-xl transition shadow-xs flex items-center gap-1.5">
                                        <span x-show="!copiedAll">📋 Salin semua</span>
                                        <span x-show="copiedAll" x-cloak class="text-emerald-600">Tersalin!</span>
                                    </button>
                                </div>

                                <div>
                                    <div class="flex justify-between items-center mb-2">
                                        <p class="text-[12px] font-bold text-gray-900">Semua Foto</p>
                                        <span class="text-[11px] font-bold text-gray-500 bg-gray-100 px-2.5 py-0.5 rounded-md" x-text="activeData.totalSelected + ' file'"></span>
                                    </div>

                                    <div class="border border-gray-200 rounded-2xl bg-gray-50 p-3 space-y-2">
                                        <div class="flex justify-between items-center mb-1">
                                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider"># &nbsp; Nama File</span>
                                        </div>
                                        <template x-for="(photo, idx) in activeData.selectedPhotos" :key="idx">
                                            <div class="bg-white border border-gray-200 rounded-xl px-3 py-2.5 text-[13px] font-mono font-medium text-gray-800 flex items-center shadow-2xs">
                                                <span class="w-full truncate" x-text="(idx + 1) + '.  ' + photo"></span>
                                            </div>
                                        </template>
                                        <div x-show="activeData.selectedPhotos.length === 0" class="text-center py-4 text-xs text-gray-400">
                                            Belum ada foto yang dipilih.
                                        </div>
                                    </div>
                                </div>

                                <div x-show="activeData.clientNotes" class="bg-white border border-gray-200 rounded-2xl p-4">
                                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Catatan Klien</p>
                                    <p class="text-[13px] text-gray-800 italic" x-text="activeData.clientNotes"></p>
                                </div>
                            </div>

                            <hr class="border-gray-100">

                            {{-- TOMBOL UBAH FOLDER KERJA (DI BAWAH SETELAH FOTO) --}}
                            <div>
                                <button type="button" @click="isFolderOpen = true; tempLink = activeData.linkFolder || '';" class="w-full flex items-center justify-center gap-2 border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 text-[13px] font-bold px-4 py-3 rounded-2xl transition shadow-sm focus:outline-none">
                                    <i data-lucide="folder-edit" class="w-4 h-4"></i> Ubah Folder Kerja / Master Folder
                                </button>
                                <p class="text-[11px] text-gray-500 leading-relaxed mt-2">
                                    Untuk folder internal tim. Tidak mengubah stage, tidak mengirim email ke klien, dan tidak dianggap sebagai hasil foto terkirim.
                                </p>
                            </div>

                        </div>

                        {{-- Footer Aksi Cepat --}}
                        <div class="p-6 border-t border-gray-100 bg-gray-50 shrink-0">
                            <p class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-3">Aksi Cepat</p>
                            
                            <div x-show="activeData.status === 'Pilih Foto' || activeData.status === 'Pilihan Diterima'">
                                <button type="submit" name="status" value="Proses Edit" class="w-full bg-amber-500 hover:bg-amber-600 text-white font-bold text-[13px] py-3 rounded-xl transition shadow-sm">
                                    Tandai Mulai Diedit
                                </button>
                            </div>
                            <div x-show="activeData.status === 'Proses Edit'">
                                <button type="submit" name="status" value="Selesai" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold text-[13px] py-3 rounded-xl transition shadow-sm">
                                    Kirim Hasil Final ke Klien
                                </button>
                            </div>
                        </div>

                        {{-- OFFCANVAS KEDUA: SET FOLDER KERJA --}}
                        <div class="absolute inset-0 bg-white z-20 flex flex-col transform transition-transform duration-300 ease-in-out shadow-[-10px_0_15px_-3px_rgba(0,0,0,0.1)]"
                             x-show="isFolderOpen"
                             x-transition:enter="translate-x-full"
                             x-transition:enter-end="translate-x-0"
                             x-transition:leave="translate-x-0"
                             x-transition:leave-end="translate-x-full"
                             x-cloak>
                            
                            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center shrink-0">
                                <h2 class="text-[16px] font-extrabold text-gray-900 leading-tight">Folder Kerja / Master Folder</h2>
                                <button type="button" @click="isFolderOpen = false" class="text-gray-400 hover:text-gray-700 bg-gray-50 p-2 rounded-full transition">
                                    <i data-lucide="x" class="w-4 h-4"></i>
                                </button>
                            </div>

                            <div class="p-6 overflow-y-auto flex-1 no-scrollbar">
                                <div class="mb-6">
                                    <label class="block text-[13px] font-bold text-gray-900 mb-2">Link Folder Kerja / Master Folder</label>
                                    <input type="url" x-model="tempLink" @keydown.enter.prevent="saveFolder()" placeholder="https://drive.google.com/drive/folders/..." class="w-full h-11 rounded-xl border border-gray-300 px-4 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm text-blue-600">
                                </div>
                            </div>

                            <div class="p-6 border-t border-gray-100 bg-gray-50 shrink-0 flex gap-3">
                                <button type="button" @click="isFolderOpen = false" class="w-1/3 bg-white border border-gray-300 text-gray-700 font-bold text-[13px] py-3 rounded-xl transition shadow-sm">
                                    Batal
                                </button>
                                <button type="button" @click="saveFolder()" :disabled="isLoading" class="w-2/3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-[13px] py-3 rounded-xl transition shadow-sm flex justify-center items-center gap-2">
                                    <span x-show="!isLoading">Simpan Folder Kerja</span>
                                    <span x-show="isLoading" class="animate-pulse">Menyimpan...</span>
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function workboardOffcanvas() {
            return {
                isOpen: false,
                isFolderOpen: false,
                isLoading: false,
                tempLink: '',
                copiedAll: false,
                activeData: {
                    id: null, status: '', statusLabel: '', linkFolder: '', clientName: '', bookingCode: '', packageName: '', date: '', wa: '', ig: '', totalSelected: 0, selectedPhotos: [], clientNotes: ''
                },

                openSidebar(data) {
                    this.activeData = { ...data };
                    this.tempLink = data.linkFolder || '';
                    this.isOpen = true;
                    this.isFolderOpen = false;
                    this.copiedAll = false;
                },

                copyAllPhotos() {
                    let textToCopy = this.activeData.selectedPhotos.join('\n');
                    navigator.clipboard.writeText(textToCopy);
                    this.copiedAll = true;
                    setTimeout(() => this.copiedAll = false, 1500);
                },

                saveFolder() {
                    this.isLoading = true;
                    fetch(`/workboard/${this.activeData.id}/update`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            status: this.activeData.status,
                            link_folder_kerja: this.tempLink
                        })
                    })
                    .then(response => {
                        this.activeData.linkFolder = this.tempLink;
                    })
                    .finally(() => {
                        this.isLoading = false;
                        this.isFolderOpen = false;
                    });
                }
            }
        }
    </script>
</x-app-layout>