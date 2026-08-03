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
                        {{-- Tambahkan ID di sini agar angkanya bisa diupdate otomatis oleh JS --}}
                        <p id="count-{{ $key }}" class="text-2xl font-extrabold {{ $colorText[$s['color']] }}">{{ $counts[$key] ?? 0 }}</p>
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
                        $filteredPhotos = array_filter($selectedPhotosArr, function($item) {
                            return !preg_match('/^[a-zA-Z0-9_-]{20,}$/', $item);
                        });
                        $totalSelected = count($selectedPhotosArr);
                        $selectedPhotosJson = json_encode(array_values($selectedPhotosArr));
                    @endphp
                    
                    <div @click="openSidebar({
                            id: {{ $b->id }},
                            status: '{{ $b->status }}',
                            statusLabel: '{{ $ui['label'] }}',
                            linkFolder: '{{ $b->link_folder_kerja ?? '' }}',
                            linkOriginal: '{{ $b->link_original ?? '' }}',
                            linkHasil: '{{ $b->link_hasil ?? '' }}',
                            clientName: '{{ addslashes($b->client_name) }}',
                            clientEmail: '{{ addslashes($b->client_email ?? '') }}',
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
                            clientNotes: '{{ addslashes($b->client_notes ?? '') }}',
                            queueNumber: '{{ $b->queue_number ?? '' }}',
                            estimateDate: '{{ $b->estimate_date ?? '' }}'
                        })" 
                         class="bg-white border border-gray-200 rounded-xl p-5 flex flex-col h-full shadow-sm hover:shadow-md hover:border-blue-300 cursor-pointer transition">
                        
                        <div class="mb-4 flex justify-between items-center">
                            {{-- ID badge ditambahkan agar bisa diupdate otomatis lewat JS --}}
                            <span id="badge-{{ $b->id }}" class="inline-block {{ $ui['badge'] }} text-[11px] font-bold px-2.5 py-1 rounded-md transition-colors duration-300">
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

        {{-- TOAST NOTIFIKASI SUKSES --}}
        <div class="fixed top-6 right-6 z-[200] max-w-sm w-full bg-[#1f2937] text-white rounded-2xl p-5 shadow-2xl border border-gray-700 flex flex-col gap-3 transform transition-all duration-300"
             x-show="showToast" x-transition:enter="-translate-y-4 opacity-0" x-transition:enter-end="translate-y-0 opacity-100" x-transition:leave="-translate-y-4 opacity-0" x-cloak>
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-6 h-6 rounded-full border border-emerald-500 flex items-center justify-center shrink-0">
                        <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-400"></i>
                    </div>
                    <h4 class="font-bold text-[15px] text-white">Hasil foto disimpan</h4>
                </div>
                <button @click="showToast = false" class="text-gray-400 hover:text-white transition">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <p class="text-[13px] text-gray-300 leading-relaxed mb-1" x-text="toastMessage"></p>
            <div>
                {{-- Tombol WA tanpa ada action tutup toast, agar tetap ada sampai di-silang --}}
                <a :href="whatsappUrl" target="_blank" class="w-max bg-[#f59e0b] hover:bg-[#d97706] text-[#ffffff] font-bold text-[13px] py-2 px-5 rounded-lg transition shadow-sm inline-flex items-center justify-center">
                    Kirim WA ke Klien
                </a>
            </div>
        </div>

        {{-- OFFCANVAS SIDEBAR KANAN --}}
        <div class="fixed inset-0 z-[100] overflow-hidden" x-show="isOpen" x-cloak>
            <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="isOpen = false"></div>

            <div class="absolute inset-y-0 right-0 max-w-md w-full flex justify-end">
                <div class="w-full h-full bg-white shadow-2xl overflow-y-auto transform transition-transform duration-300 ease-in-out custom-scrollbar relative"
                     x-show="isOpen">
                    
                    <form :action="`/workboard/${activeData.id}/update`" method="POST" class="min-h-full flex flex-col">
                        @csrf

                        {{-- 1. MAIN SIDEBAR VIEW --}}
                        <div x-show="!isFolderOpen && !isUploadOpen && !isMulaiEditOpen && !isKirimHasilOpen" 
                             class="flex flex-col flex-grow w-full transition-opacity duration-300" x-cloak>
                            
                            {{-- Header --}}
                            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-start shrink-0">
                                <div>
                                    <span class="inline-block text-[10px] font-bold px-2 py-0.5 rounded-md mb-1.5"
                                          :class="activeData.status === 'Proses Edit' ? 'bg-purple-50 text-purple-600' : (activeData.status === 'Selesai' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600')" 
                                          x-text="activeData.statusLabel"></span>
                                    <h2 class="text-lg font-extrabold text-gray-900 leading-tight" x-text="activeData.clientName"></h2>
                                    <p class="text-[12px] font-mono text-gray-500" x-text="activeData.bookingCode"></p>
                                </div>
                                <button type="button" @click="isOpen = false" class="text-gray-400 hover:text-gray-700 bg-gray-50 p-2 rounded-full transition shrink-0 mt-1">
                                    <i data-lucide="x" class="w-4 h-4"></i>
                                </button>
                            </div>

                            {{-- Body --}}
                            <div class="px-6 py-4 space-y-4 flex-grow">
                                <div class="grid grid-cols-2 gap-y-3 gap-x-3">
                                    <div>
                                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Paket</p>
                                        <p class="text-[13px] font-semibold text-gray-900" x-text="activeData.packageName"></p>
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Tanggal Sesi</p>
                                        <p class="text-[13px] font-semibold text-gray-900" x-text="activeData.date"></p>
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">WhatsApp</p>
                                        <a :href="'https://wa.me/' + activeData.wa.replace(/[^0-9]/g, '')" target="_blank" class="text-[13px] font-semibold text-blue-600 hover:underline" x-text="activeData.wa"></a>
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Instagram</p>
                                        <span class="text-[13px] font-semibold text-gray-900" x-text="activeData.ig || '-'"></span>
                                    </div>
                                </div>

                                <div>
                                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Folder Kerja / Master Folder</p>
                                    <div x-show="activeData.linkFolder !== ''">
                                        <a :href="activeData.linkFolder" target="_blank" class="text-[13px] text-blue-600 hover:underline break-all block" x-text="activeData.linkFolder"></a>
                                    </div>
                                    <div x-show="activeData.linkFolder === ''" class="text-[13px] text-gray-400 italic">Belum diset</div>
                                </div>
                                
                                <div x-show="['Proses Edit', 'Selesai'].includes(activeData.status) && activeData.queueNumber">
                                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">No. Antrian</p>
                                    <p class="text-[14px] font-extrabold text-gray-900" x-text="'#' + activeData.queueNumber"></p>
                                </div>

                                <div x-show="['Pilihan Diterima', 'Proses Edit', 'Selesai'].includes(activeData.status)">
                                    <hr class="border-gray-100 my-1">
                                </div>

                                <div x-show="['Pilihan Diterima', 'Proses Edit', 'Selesai'].includes(activeData.status)" class="space-y-3">
                                    <div class="flex justify-between items-center bg-amber-50/70 border border-amber-200 rounded-xl p-3">
                                        <div>
                                            <p class="text-[10px] font-bold text-amber-800 uppercase tracking-wider mb-0.5">Foto Dipilih</p>
                                            <p class="text-xl font-extrabold text-amber-900 leading-none" x-text="activeData.totalSelected"></p>
                                        </div>
                                        <button type="button" @click="copyAllPhotos()" class="bg-white border border-amber-300 hover:bg-amber-100 text-amber-900 font-bold text-[11px] px-3 py-1.5 rounded-lg transition shadow-xs flex items-center gap-1 shrink-0">
                                            <span x-show="!copiedAll">📋 Salin</span>
                                            <span x-show="copiedAll" x-cloak class="text-emerald-600">Tersalin!</span>
                                        </button>
                                    </div>

                                    <div>
                                        <div class="flex justify-between items-center mb-1.5">
                                            <p class="text-[11px] font-bold text-gray-900">Semua Foto</p>
                                            <span class="text-[10px] font-bold text-gray-500 bg-gray-100 px-2 py-0.5 rounded" x-text="activeData.totalSelected + ' file'"></span>
                                        </div>
                                        <div class="border border-gray-200 rounded-xl bg-gray-50 p-2 space-y-1.5">
                                            <template x-for="(photo, idx) in activeData.selectedPhotos" :key="idx">
                                                <div class="bg-white border border-gray-200 rounded-lg px-2.5 py-1.5 text-[12px] font-mono font-medium text-gray-800 flex items-center shadow-2xs">
                                                    <span class="w-full truncate" x-text="(idx + 1) + '.  ' + photo"></span>
                                                </div>
                                            </template>
                                            <div x-show="activeData.selectedPhotos.length === 0" class="text-center py-2 text-[11px] text-gray-400">
                                                Belum ada foto yang dipilih.
                                            </div>
                                        </div>
                                    </div>

                                    <div x-show="activeData.clientNotes" class="bg-white border border-gray-200 rounded-xl p-3">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Catatan Klien</p>
                                        <p class="text-[12px] text-gray-800 italic" x-text="activeData.clientNotes"></p>
                                    </div>
                                </div>
                            </div>

                            {{-- Footer Aksi --}}
                            <div class="px-6 pt-0 pb-6 shrink-0 mt-auto">
                                <div class="mb-4 pb-4 border-b border-gray-200 mt-2">
                                    <button type="button" @click="openPanel('isFolderOpen'); tempLink = activeData.linkFolder || '';" class="w-full flex items-center justify-center gap-2 border border-gray-300 text-gray-700 bg-white hover:bg-gray-100 text-[12px] font-bold px-3 py-2.5 rounded-xl transition shadow-sm focus:outline-none">
                                        <span x-show="activeData.linkFolder !== ''" class="flex items-center gap-1.5">
                                            <i data-lucide="folder-edit" class="w-3.5 h-3.5"></i> Ubah Folder Kerja / Master Folder
                                        </span>
                                        <span x-show="activeData.linkFolder === ''" class="flex items-center gap-1.5">
                                            <i data-lucide="folder-plus" class="w-3.5 h-3.5"></i> Set Folder Kerja / Master Folder
                                        </span>
                                    </button>
                                    <p class="text-[10px] text-gray-400 leading-relaxed mt-1.5 text-center px-2">
                                        Folder internal tim. Tidak mengirim notifikasi ke klien.
                                    </p>
                                </div>

                                <div x-show="activeData.status === 'Proses Edit' && activeData.estimateDate" class="mb-4 pb-4 border-b border-gray-200">
                                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Deadline Editing</p>
                                    <p class="text-[13px] font-bold text-gray-900" x-text="formatDeadline(activeData.estimateDate)"></p>
                                </div>

                                <p class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2" x-show="activeData.status !== 'Selesai'">Aksi Cepat</p>
                                
                                <div x-show="activeData.status === 'Dijadwalkan'" class="space-y-2">
                                    <button type="button" @click="openPanel('isUploadOpen'); uploadLink = activeData.linkOriginal; uploadToggle = false;" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-[13px] py-2.5 rounded-xl transition shadow-sm">
                                        Tandai File Original Tersedia
                                    </button>
                                </div>

                                <div x-show="activeData.status === 'File Original Disiapkan'" class="space-y-2">
                                    <button type="submit" name="status" value="Proses Edit" class="w-full bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-bold text-[13px] py-2.5 rounded-xl transition shadow-sm">
                                        ✏️ Langsung ke Editing
                                    </button>
                                </div>

                                <div x-show="activeData.status === 'Pilih Foto' || activeData.status === 'Pilihan Diterima'">
                                    <button type="button" @click="openPanel('isMulaiEditOpen')" class="w-full bg-amber-500 hover:bg-amber-600 text-white font-bold text-[13px] py-2.5 rounded-xl transition shadow-sm">
                                        Tandai Mulai Diedit
                                    </button>
                                </div>
                                
                                <div x-show="activeData.status === 'Proses Edit'" class="space-y-2">
                                    <button type="button" @click="openPanel('isMulaiEditOpen')" class="w-full bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-bold text-[12px] py-2.5 rounded-xl transition shadow-sm">
                                        Update Antrian & Deadline
                                    </button>
                                    <button type="button" @click="openPanel('isKirimHasilOpen'); linkHasil = activeData.linkHasil || ''; adminNotes = '';" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold text-[12px] py-2.5 rounded-xl transition shadow-sm">
                                        Kirim Hasil Foto
                                    </button>
                                    <button type="button" @click="saveTandaiSelesai()" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[12px] py-2.5 rounded-xl transition shadow-sm flex justify-center items-center gap-1.5">
                                        <span x-show="!isCompleting">Tandai Selesai</span>
                                        <span x-show="isCompleting" class="animate-pulse">Menyimpan...</span>
                                    </button>
                                </div>

                                <div x-show="activeData.status === 'Selesai'" class="text-center bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl p-3 text-[13px] font-bold">
                                    ✨ Hasil foto sudah dikirim ke klien.
                                </div>
                            </div>
                        </div>

                        {{-- 2. SET FOLDER KERJA VIEW --}}
                        <div x-show="isFolderOpen" class="flex flex-col flex-grow w-full" x-cloak>
                            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center shrink-0">
                                <h2 class="text-[16px] font-extrabold text-gray-900 leading-tight">Folder Kerja / Master Folder</h2>
                                <button type="button" @click="openPanel('main')" class="text-gray-400 hover:text-gray-700 bg-gray-50 p-1.5 rounded-full transition"><i data-lucide="x" class="w-4 h-4"></i></button>
                            </div>
                            <div class="p-6 flex-grow no-scrollbar">
                                <div class="bg-blue-50/50 border border-blue-100 rounded-xl p-4 mb-6">
                                    <p class="text-[13px] text-gray-700 leading-relaxed font-semibold mb-2">Akses internal tim.</p>
                                    <ul class="text-[12px] text-gray-600 list-disc list-inside space-y-1 ml-1">
                                        <li>Tidak mengirim notifikasi ke klien.</li>
                                        <li>Untuk Calendar, pakai <code>{link_folder_kerja}</code>.</li>
                                    </ul>
                                </div>
                                <div>
                                    <label class="block text-[13px] font-bold text-gray-900 mb-2">Link Folder Master <span class="text-red-500">*</span></label>
                                    <input type="url" x-model="tempLink" @keydown.enter.prevent="saveFolder()" placeholder="https://drive.google.com/..." class="w-full h-11 rounded-xl border border-gray-300 px-4 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm text-blue-600">
                                    <p class="text-[11px] text-gray-500 mt-2">Kosongkan lalu simpan jika ingin menghapus link.</p>
                                </div>
                            </div>
                            <div class="p-6 border-t border-gray-100 bg-gray-50 shrink-0 flex gap-3 mt-auto">
                                <button type="button" @click="openPanel('main')" class="w-1/3 bg-white border border-gray-300 text-gray-700 font-bold text-[13px] py-3 rounded-xl transition shadow-sm">Batal</button>
                                <button type="button" @click="saveFolder()" :disabled="isLoading" class="w-2/3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-[13px] py-3 rounded-xl transition shadow-sm flex justify-center items-center gap-1.5">
                                    <span x-show="!isLoading">Simpan Link</span>
                                    <span x-show="isLoading" class="animate-pulse">Menyimpan...</span>
                                </button>
                            </div>
                        </div>

                        {{-- 3. UPLOAD FILE ORIGINAL VIEW --}}
                        <div x-show="isUploadOpen" class="flex flex-col flex-grow w-full" x-cloak>
                            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center shrink-0">
                                <h2 class="text-[16px] font-extrabold text-gray-900 leading-tight">Upload File Original</h2>
                                <button type="button" @click="openPanel('main')" class="text-gray-400 hover:text-gray-700 bg-gray-50 p-1.5 rounded-full transition"><i data-lucide="x" class="w-4 h-4"></i></button>
                            </div>
                            <div class="p-6 flex-grow no-scrollbar">
                                <div class="mb-6">
                                    <label class="block text-[13px] font-bold text-gray-900 mb-2">Link Google Drive (RAW) <span class="text-red-500">*</span></label>
                                    <input type="url" x-model="uploadLink" @keydown.enter.prevent="saveUpload()" placeholder="https://drive.google.com/..." class="w-full h-11 rounded-xl border border-gray-300 px-4 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm text-blue-600">
                                </div>
                                <div class="border border-gray-200 bg-white rounded-xl p-4 flex items-center justify-between gap-4 cursor-pointer hover:bg-gray-50 transition shadow-sm" @click="uploadToggle = !uploadToggle">
                                    <div class="flex-1">
                                        <p class="text-[13px] font-bold text-gray-900 mb-0.5">Tersedia untuk klien</p>
                                        <p class="text-[11px] text-gray-500">Munculkan di halaman tracking.</p>
                                    </div>
                                    <div class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none" :class="uploadToggle ? 'bg-blue-600' : 'bg-gray-200'">
                                        <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out" :class="uploadToggle ? 'translate-x-5' : 'translate-x-0'"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="p-6 border-t border-gray-100 bg-gray-50 shrink-0 flex gap-3 mt-auto">
                                <button type="button" @click="openPanel('main')" class="w-1/3 bg-white border border-gray-300 text-gray-700 font-bold text-[13px] py-3 rounded-xl transition shadow-sm">Batal</button>
                                <button type="button" @click="saveUpload()" :disabled="isLoading" class="w-2/3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-[13px] py-3 rounded-xl transition shadow-sm flex justify-center items-center gap-1.5">
                                    <span x-show="!isLoading">Simpan</span>
                                    <span x-show="isLoading" class="animate-pulse">Menyimpan...</span>
                                </button>
                            </div>
                        </div>

                        {{-- 4. TANDAI MULAI DIEDIT VIEW --}}
                        <div x-show="isMulaiEditOpen" class="flex flex-col flex-grow w-full" x-cloak>
                            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center shrink-0">
                                <h2 class="text-[16px] font-extrabold text-gray-900 leading-tight" x-text="activeData.status === 'Proses Edit' ? 'Update Antrian & Deadline' : 'Tandai Mulai Diedit'"></h2>
                                <button type="button" @click="openPanel('main')" class="text-gray-400 hover:text-gray-700 bg-gray-50 p-1.5 rounded-full transition"><i data-lucide="x" class="w-4 h-4"></i></button>
                            </div>
                            <div class="p-6 flex-grow no-scrollbar space-y-6">
                                <p class="text-[13px] text-gray-600 leading-relaxed" x-show="activeData.status !== 'Proses Edit'">
                                    Status booking berubah menjadi <b>Proses Editing</b>.
                                </p>
                                <div>
                                    <label class="block text-[13px] font-bold text-gray-900 mb-2">Nomor Antrian Editing</label>
                                    <input type="text" x-model="queueNumber" class="w-full h-11 rounded-xl border border-gray-300 px-4 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm text-gray-900">
                                </div>
                                <div>
                                    <label class="block text-[13px] font-bold text-gray-900 mb-2">Estimasi Selesai <span class="text-gray-400 font-normal">(opsional)</span></label>
                                    <input type="date" x-model="estimateDate" class="w-full h-11 rounded-xl border border-gray-300 px-4 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm text-gray-900 bg-white">
                                </div>
                                <div class="flex items-center gap-4 bg-gray-50 border border-gray-200 rounded-xl p-4 cursor-pointer hover:bg-gray-100 transition" @click="sendEmailToggle = !sendEmailToggle">
                                    <div class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none" :class="sendEmailToggle ? 'bg-blue-600' : 'bg-gray-200'">
                                        <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out" :class="sendEmailToggle ? 'translate-x-5' : 'translate-x-0'"></span>
                                    </div>
                                    <p class="text-[13px] font-bold text-gray-900">Kirim notifikasi email klien</p>
                                </div>
                            </div>
                            <div class="p-6 border-t border-gray-100 bg-gray-50 shrink-0 flex gap-3 mt-auto">
                                <button type="button" @click="openPanel('main')" class="w-1/3 bg-white border border-gray-300 text-gray-700 font-bold text-[13px] py-3 rounded-xl transition shadow-sm">Batal</button>
                                <button type="button" @click="saveMulaiEdit()" :disabled="isLoading" class="w-2/3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-[13px] py-3 rounded-xl transition shadow-sm flex justify-center items-center gap-1.5">
                                    <span x-show="!isLoading" x-text="activeData.status === 'Proses Edit' ? 'Simpan' : 'Tandai Diedit'"></span>
                                    <span x-show="isLoading" class="animate-pulse">Menyimpan...</span>
                                </button>
                            </div>
                        </div>

                        {{-- 5. KIRIM HASIL FOTO VIEW --}}
                        <div x-show="isKirimHasilOpen" class="flex flex-col flex-grow w-full" x-cloak>
                            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center shrink-0">
                                <h2 class="text-[16px] font-extrabold text-gray-900 leading-tight">Kirim Hasil Foto</h2>
                                <button type="button" @click="openPanel('main')" class="text-gray-400 hover:text-gray-700 bg-gray-50 p-1.5 rounded-full transition"><i data-lucide="x" class="w-4 h-4"></i></button>
                            </div>
                            <div class="p-6 flex-grow no-scrollbar space-y-6">
                                <div>
                                    <label class="block text-[13px] font-bold text-gray-900 mb-2">Link Google Drive Hasil Foto <span class="text-red-500">*</span></label>
                                    <input type="url" x-model="linkHasil" placeholder="https://drive.google.com/..." class="w-full h-11 rounded-xl border border-gray-300 px-4 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm text-blue-600">
                                </div>
                                <div class="bg-amber-50/70 border border-amber-200 rounded-xl p-4 text-xs text-amber-900 leading-relaxed">
                                    <b>Catatan:</b> Untuk hasil final ke klien. Folder kerja/calendar diisi dari Edit Booking agar tidak dianggap terkirim.
                                </div>
                                <div>
                                    <label class="block text-[13px] font-bold text-gray-900 mb-2">Catatan untuk Klien (opsional)</label>
                                    <textarea x-model="adminNotes" rows="3" placeholder="Contoh: File tersimpan 30 hari, segera download..." class="w-full rounded-xl border border-gray-300 p-3 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm"></textarea>
                                </div>
                                <div class="flex items-center gap-4 bg-gray-50 border border-gray-200 rounded-xl p-4 cursor-pointer hover:bg-gray-100 transition" @click="sendEmailToggle = !sendEmailToggle">
                                    <div class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none" :class="sendEmailToggle ? 'bg-blue-600' : 'bg-gray-200'">
                                        <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out" :class="sendEmailToggle ? 'translate-x-5' : 'translate-x-0'"></span>
                                    </div>
                                    <p class="text-[13px] font-bold text-gray-900">Kirim email notifikasi ke klien</p>
                                </div>
                            </div>
                            <div class="p-6 border-t border-gray-100 bg-gray-50 shrink-0 flex gap-3 mt-auto">
                                <button type="button" @click="openPanel('main')" class="w-1/3 bg-white border border-gray-300 text-gray-700 font-bold text-[13px] py-3 rounded-xl transition shadow-sm">Batal</button>
                                <button type="button" @click="saveKirimHasil()" :disabled="isLoading || !linkHasil" class="w-2/3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[13px] py-3 rounded-xl transition shadow-sm flex justify-center items-center gap-1.5 disabled:opacity-50">
                                    <span x-show="!isLoading">Kirim Hasil Foto</span>
                                    <span x-show="isLoading" class="animate-pulse">Mengirim...</span>
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Script styling untuk custom scrollbar yang tipis & elegan --}}
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent; 
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1; 
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8; 
        }
    </style>

    <script>
        function workboardOffcanvas() {
            return {
                isOpen: false,
                isFolderOpen: false,
                isUploadOpen: false,
                isMulaiEditOpen: false,
                isKirimHasilOpen: false,
                isLoading: false,
                isCompleting: false,
                tempLink: '',
                uploadLink: '',
                linkHasil: '',
                adminNotes: '',
                uploadToggle: false,
                queueNumber: '1',
                estimateDate: '',
                sendEmailToggle: true,
                copiedAll: false,
                
                // State untuk Toast Notifikasi Berkelanjutan
                showToast: false,
                toastMessage: '',
                whatsappUrl: '',

                activeData: {
                    id: null, status: '', statusLabel: '', linkFolder: '', linkHasil: '', clientName: '', clientEmail: '', bookingCode: '', packageName: '', date: '', wa: '', ig: '', totalSelected: 0, selectedPhotos: [], clientNotes: '', queueNumber: '', estimateDate: '', companyName: ''
                },

                // FUNGSI UNTUK MERUBAH ANGKA STATISTIK (DIBAGIAN ATAS) SECARA OTOMATIS
                updateStatCount(oldStatus, newStatus) {
                    const statusMap = {
                        'Dijadwalkan': 'belum_upload',
                        'File Original Disiapkan': 'belum_pilih_foto',
                        'Pilih Foto': 'seleksi_masuk',
                        'Pilihan Diterima': 'seleksi_masuk',
                        'Proses Edit': 'sedang_diedit',
                        'Selesai': 'terkirim'
                    };

                    let oldKey = statusMap[oldStatus];
                    let newKey = statusMap[newStatus];

                    if (oldKey && newKey && oldKey !== newKey) {
                        let oldEl = document.getElementById('count-' + oldKey);
                        let newEl = document.getElementById('count-' + newKey);
                        
                        if (oldEl) {
                            let currentOld = parseInt(oldEl.innerText) || 0;
                            oldEl.innerText = Math.max(0, currentOld - 1);
                        }
                        if (newEl) {
                            let currentNew = parseInt(newEl.innerText) || 0;
                            newEl.innerText = currentNew + 1;
                        }
                    }
                },

                openPanel(name) {
                    this.isFolderOpen = false;
                    this.isUploadOpen = false;
                    this.isMulaiEditOpen = false;
                    this.isKirimHasilOpen = false;
                    
                    if (name !== 'main') {
                        this[name] = true;
                    }
                },

                openSidebar(data) {
                    this.activeData = { ...data };
                    this.tempLink = data.linkFolder || '';
                    this.uploadLink = data.linkOriginal || '';
                    this.linkHasil = data.linkHasil || '';
                    this.queueNumber = data.queueNumber || '1';
                    this.estimateDate = data.estimateDate || '';
                    this.adminNotes = '';
                    this.uploadToggle = data.status === 'File Original Disiapkan';
                    
                    this.isOpen = true;
                    this.openPanel('main');
                    this.copiedAll = false;
                },

                formatDeadline(dateString) {
                    if (!dateString) return '';
                    const d = new Date(dateString);
                    const today = new Date();
                    today.setHours(0,0,0,0);
                    const diffTime = d.getTime() - today.getTime();
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                    const formattedDate = `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;

                    if (diffDays > 0) return `${formattedDate} — ${diffDays} hari lagi`;
                    if (diffDays === 0) return `${formattedDate} — Hari ini`;
                    return `${formattedDate} — Terlewat ${Math.abs(diffDays)} hari`;
                },

                copyAllPhotos() {
                    let textToCopy = this.activeData.selectedPhotos.map(p => typeof p === 'object' ? p.filename : p).join('\n');
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
                            link_folder_kerja: this.tempLink ? this.tempLink.trim() : ''
                        })
                    })
                    .then(async response => {
                        if (!response.ok) throw new Error(await response.text());
                        return response.json();
                    })
                    .then(data => {
                        this.activeData.linkFolder = this.tempLink;
                        this.openPanel('main');
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat menyimpan data. Pastikan format valid.');
                    })
                    .finally(() => {
                        this.isLoading = false;
                    });
                },

                saveUpload() {
                    this.isLoading = true;
                    let oldStatus = this.activeData.status;
                    let newStatus = oldStatus;
                    
                    if (this.uploadToggle && oldStatus === 'Dijadwalkan') {
                        newStatus = 'File Original Disiapkan';
                    } else if (!this.uploadToggle && oldStatus === 'File Original Disiapkan') {
                        newStatus = 'Dijadwalkan';
                    }

                    fetch(`/workboard/${this.activeData.id}/update`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            status: newStatus,
                            link_original: this.uploadLink ? this.uploadLink.trim() : ''
                        })
                    })
                    .then(async response => {
                        if (!response.ok) throw new Error(await response.text());
                        return response.json();
                    })
                    .then(data => {
                        this.updateStatCount(oldStatus, newStatus);
                        this.activeData.status = newStatus;
                        this.activeData.linkOriginal = this.uploadLink;
                        
                        if(newStatus === 'File Original Disiapkan') {
                             this.activeData.statusLabel = 'Belum Pilih Foto';
                        } else {
                             this.activeData.statusLabel = 'Belum Upload';
                        }
                        
                        // VISUAL UPDATE INSTAN PADA KARTU GRID
                        let badgeElement = document.getElementById('badge-' + this.activeData.id);
                        if (badgeElement) {
                            if(newStatus === 'File Original Disiapkan') {
                                badgeElement.textContent = 'Belum Pilih Foto';
                                badgeElement.className = 'inline-block bg-orange-50 text-orange-600 text-[11px] font-bold px-2.5 py-1 rounded-md transition-colors duration-300';
                            } else {
                                badgeElement.textContent = 'Belum Upload';
                                badgeElement.className = 'inline-block bg-gray-100 text-gray-600 text-[11px] font-bold px-2.5 py-1 rounded-md transition-colors duration-300';
                            }
                        }

                        this.openPanel('main'); 
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat menyimpan data.');
                    })
                    .finally(() => {
                        this.isLoading = false;
                    });
                },

                saveMulaiEdit() {
                    this.isLoading = true;
                    let oldStatus = this.activeData.status;

                    fetch(`/workboard/${this.activeData.id}/update`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            status: 'Proses Edit',
                            queue_number: this.queueNumber,
                            estimate_date: this.estimateDate,
                            send_email: this.sendEmailToggle
                        })
                    })
                    .then(async response => {
                        if (!response.ok) throw new Error(await response.text());
                        return response.json();
                    })
                    .then(data => {
                        if (data.success || data.message) {
                            this.updateStatCount(oldStatus, 'Proses Edit');
                            this.activeData.status = 'Proses Edit';
                            this.activeData.statusLabel = 'Sedang Diedit';
                            this.activeData.queueNumber = this.queueNumber;
                            this.activeData.estimateDate = this.estimateDate;

                            // VISUAL UPDATE INSTAN PADA KARTU GRID
                            let badgeElement = document.getElementById('badge-' + this.activeData.id);
                            if (badgeElement) {
                                badgeElement.textContent = 'Sedang Diedit';
                                badgeElement.className = 'inline-block bg-purple-50 text-purple-600 text-[11px] font-bold px-2.5 py-1 rounded-md transition-colors duration-300';
                            }

                            this.openPanel('main');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat menyimpan data. Cek log console.');
                    })
                    .finally(() => {
                        this.isLoading = false;
                    });
                },

                saveKirimHasil() {
                    this.isLoading = true;
                    let oldStatus = this.activeData.status;

                    fetch(`/workboard/${this.activeData.id}/update`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            status: 'Selesai',
                            link_hasil: this.linkHasil ? this.linkHasil.trim() : '',
                            admin_notes: this.adminNotes,
                            send_email: this.sendEmailToggle
                        })
                    })
                    .then(async response => {
                        if (!response.ok) throw new Error(await response.text());
                        return response.json();
                    })
                    .then(data => {
                        if (data.success || data.message) {
                            this.updateStatCount(oldStatus, 'Selesai');
                            
                            // Update internal state
                            this.activeData.status = 'Selesai';
                            this.activeData.statusLabel = 'Terkirim';
                            this.activeData.linkHasil = this.linkHasil;
                            
                            // Tutup sidebar sepenuhnya
                            this.isOpen = false; 

                            // VISUAL UPDATE INSTAN PADA KARTU GRID
                            let badgeElement = document.getElementById('badge-' + this.activeData.id);
                            if (badgeElement) {
                                badgeElement.textContent = 'Terkirim';
                                badgeElement.className = 'inline-block bg-emerald-50 text-emerald-600 text-[11px] font-bold px-2.5 py-1 rounded-md transition-colors duration-300';
                            }

                            // Siapkan Data untuk Notifikasi Toast
                            let clientEmailStr = this.activeData.clientEmail ? this.activeData.clientEmail : 'klien';
                            this.toastMessage = `Link disimpan dan email sudah dikirim ke ${clientEmailStr}.`;

                            // Siapkan URL WA dengan format yang diminta dan convert '0' ke '62'
                            let waNum = this.activeData.wa ? this.activeData.wa.replace(/[^0-9]/g, '') : '';
                            if (waNum.startsWith('0')) {
                                waNum = '62' + waNum.substring(1);
                            }
                            
                            let company = this.activeData.companyName || 'Studio Foto';
                            let waText = encodeURIComponent(`Halo ${this.activeData.clientName}!\n\nHasil foto kamu sudah siap.\n\nKode         : ${this.activeData.bookingCode}\nLink Download: ${this.linkHasil}\n\nTerima kasih sudah bersama ${company}.`);
                            
                            // Menggunakan API whatsapp yang lebih solid
                            this.whatsappUrl = `https://api.whatsapp.com/send?phone=${waNum}&text=${waText}`;

                            // Munculkan Toast TANPA fungsi hilangkan otomatis
                            this.showToast = true;
                        }
                    })
                    .catch(error => {
                        console.error('Error saat Kirim Hasil:', error);
                        alert('Gagal mengirim hasil foto. Pastikan link terisi dengan format https:// yang benar.');
                    })
                    .finally(() => {
                        this.isLoading = false;
                    });
                },

                saveTandaiSelesai() {
                    this.isCompleting = true;
                    let oldStatus = this.activeData.status;

                    fetch(`/workboard/${this.activeData.id}/update`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            status: 'Selesai'
                        })
                    })
                    .then(async response => {
                        if (!response.ok) throw new Error(await response.text());
                        return response.json();
                    })
                    .then(data => {
                        if (data.success || data.message) {
                            this.updateStatCount(oldStatus, 'Selesai');
                            this.activeData.status = 'Selesai';
                            this.activeData.statusLabel = 'Terkirim';
                            
                            // VISUAL UPDATE INSTAN PADA KARTU GRID
                            let badgeElement = document.getElementById('badge-' + this.activeData.id);
                            if (badgeElement) {
                                badgeElement.textContent = 'Terkirim';
                                badgeElement.className = 'inline-block bg-emerald-50 text-emerald-600 text-[11px] font-bold px-2.5 py-1 rounded-md transition-colors duration-300';
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat menandai selesai.');
                    })
                    .finally(() => {
                        this.isCompleting = false;
                    });
                }
            }
        }
    </script>
</x-app-layout>