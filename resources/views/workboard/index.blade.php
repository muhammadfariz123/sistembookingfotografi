{{-- resources/views/workboard/index.blade.php --}}
<x-app-layout>
    <div x-data="workboardModal()" class="w-full">
        {{-- Kita bungkus dengan max-w-7xl dan beri padding secukupnya agar proporsional dan tidak mepet sidebar --}}
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4 px-4 sm:px-0">
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

            {{-- Stat Cards (klik = filter) --}}
            @php
                $stats = [
                    'semua'            => ['label' => 'Semua',            'desc' => 'Semua stage aktif',        'color' => 'blue'],
                    'belum_upload'     => ['label' => 'Belum Upload',     'desc' => 'Menunggu file original',   'color' => 'gray'],
                    'belum_pilih_foto' => ['label' => 'Belum Pilih Foto', 'desc' => 'File ada, klien belum pilih','color' => 'orange'],
                    'seleksi_masuk'    => ['label' => 'Seleksi Masuk',    'desc' => 'Klien sudah submit pilihan','color' => 'amber'],
                    'sedang_diedit'    => ['label' => 'Sedang Diedit',    'desc' => 'Status booking: editing',   'color' => 'purple'],
                    'terkirim'         => ['label' => 'Terkirim',         'desc' => 'Selesai dikerjakan',       'color' => 'emerald'],
                ];
                $colorText = [
                    'blue' => 'text-blue-600', 'gray' => 'text-gray-700', 'orange' => 'text-orange-500',
                    'amber' => 'text-amber-500', 'purple' => 'text-purple-600', 'emerald' => 'text-emerald-600',
                ];
            @endphp
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-6 px-4 sm:px-0">
                @foreach($stats as $key => $s)
                    <a href="{{ route('workboard.index', ['tab' => $key]) }}"
                       class="rounded-xl border p-4 transition shadow-sm {{ $tab === $key ? 'border-blue-400 bg-blue-50' : 'border-gray-200 bg-white hover:border-gray-300 hover:shadow-md' }}">
                        <p class="text-2xl font-extrabold {{ $colorText[$s['color']] }}">{{ $counts[$key] ?? 0 }}</p>
                        <p class="text-sm font-bold text-gray-900 mt-1">{{ $s['label'] }}</p>
                        <p class="text-[11px] text-gray-500 mt-0.5">{{ $s['desc'] }}</p>
                    </a>
                @endforeach
            </div>

            {{-- Search & Filter Bar --}}
            <div class="flex items-center gap-3 mb-4 px-4 sm:px-0">
                <div class="flex-1 flex items-center gap-2 bg-white border border-gray-200 rounded-xl px-4 h-11 shadow-sm focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500 transition">
                    <i data-lucide="search" class="w-4 h-4 text-gray-400"></i>
                    <input type="text" placeholder="Cari nama klien atau kode booking..." class="flex-1 bg-transparent text-sm text-gray-900 placeholder-gray-400 focus:outline-none w-full border-none focus:ring-0 px-0">
                </div>
                <button type="button" class="h-11 w-11 flex items-center justify-center bg-white border border-gray-200 rounded-xl text-gray-500 hover:text-gray-900 hover:bg-gray-50 shadow-sm transition">
                    <i data-lucide="list" class="w-4 h-4"></i>
                </button>
                <button type="button" class="h-11 w-11 flex items-center justify-center bg-gray-100 border border-gray-200 rounded-xl text-gray-900 shadow-inner">
                    <i data-lucide="layout-grid" class="w-4 h-4"></i>
                </button>
            </div>

            <div class="flex items-center justify-between mb-6 text-[12px] text-gray-500 px-4 sm:px-0">
                <span class="flex items-center gap-1.5"><i data-lucide="refresh-cw" class="w-3 h-3"></i> Tampilan Grid</span>
                <span>
                    {{ $bookings->count() ? $bookings->firstItem() . '-' . $bookings->lastItem() . ' dari ' . $bookings->total() : '0' }} booking
                </span>
            </div>

            {{-- Card Grid --}}
            @php
                $statusUi = [
                    'Dijadwalkan'              => ['label' => 'Belum Upload',     'border' => 'border-l-gray-400',   'badge' => 'bg-gray-100 text-gray-600',   'dot' => 'bg-gray-500'],
                    'File Original Disiapkan'  => ['label' => 'Belum Pilih Foto', 'border' => 'border-l-orange-400', 'badge' => 'bg-orange-50 text-orange-600','dot' => 'bg-orange-500'],
                    'Pilih Foto'               => ['label' => 'Seleksi Masuk',    'border' => 'border-l-amber-400',  'badge' => 'bg-amber-50 text-amber-600', 'dot' => 'bg-amber-500'],
                    'Proses Edit'              => ['label' => 'Sedang Diedit',    'border' => 'border-l-purple-400', 'badge' => 'bg-purple-50 text-purple-600','dot' => 'bg-purple-500'],
                    'Selesai'                  => ['label' => 'Terkirim',         'border' => 'border-l-emerald-400','badge' => 'bg-emerald-50 text-emerald-600','dot' => 'bg-emerald-500'],
                ];
            @endphp
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 px-4 sm:px-0">
                @forelse($bookings as $b)
                    @php $ui = $statusUi[$b->status] ?? $statusUi['Dijadwalkan']; @endphp
                    <div class="bg-white border border-gray-200 {{ $ui['border'] }} border-l-4 rounded-xl p-4 flex flex-col h-full shadow-sm hover:shadow-md transition">
                        <div class="flex items-center justify-between mb-3">
                            <span class="inline-flex items-center gap-1.5 {{ $ui['badge'] }} text-[11px] font-bold px-2.5 py-1 rounded-md">
                                <span class="w-1.5 h-1.5 rounded-full {{ $ui['dot'] }}"></span> {{ $ui['label'] }}
                            </span>

                            {{-- Fitur tambahan visual --}}
                            @if($b->status === 'File Original Disiapkan' && !empty($b->due_in_days))
                                <span class="text-[11px] font-bold text-orange-500">Sisa {{ $b->due_in_days }} hari</span>
                            @elseif($b->status === 'Proses Edit' && !empty($b->queue_position))
                                <span class="text-[11px] font-bold bg-purple-50 text-purple-600 px-2 py-0.5 rounded-full">Antrian #{{ $b->queue_position }}</span>
                            @endif
                        </div>

                        <p class="font-bold text-gray-900 text-[15px] leading-tight">{{ $b->client_name }}</p>
                        <p class="text-[12px] font-mono text-gray-400 font-semibold mt-0.5">{{ $b->booking_code }}</p>

                        <div class="mt-3">
                            <span class="inline-block bg-blue-50 text-blue-600 text-[11px] font-bold px-3 py-1 rounded-lg">
                                {{ $b->serviceType->name ?? 'Paket Layanan' }}
                            </span>
                        </div>

                        <p class="text-[13px] text-gray-500 font-medium mt-3 flex items-center gap-1.5">
                            <i data-lucide="calendar" class="w-3.5 h-3.5 text-gray-400"></i>
                            {{ \Carbon\Carbon::parse($b->booking_date ?? $b->start_date)->format('d M Y') }}
                        </p>

                        @if($b->status === 'File Original Disiapkan')
                            <p class="text-[12px] text-gray-500 mt-1">Maks. {{ $b->max_photos ?? '-' }} foto</p>
                        @elseif($b->status === 'Proses Edit')
                            <p class="text-[12px] text-purple-500 mt-1">{{ $b->selected_photos_count ?? 0 }} foto dipilih</p>
                        @endif

                        {{-- Spacer agar tombol kelola selalu di bawah --}}
                        <div class="flex-grow"></div>

                        <button @click="openModal({{ $b->id }}, '{{ $b->status }}', '{{ $b->link_hasil }}', '{{ addslashes($b->client_name) }}')"
                                class="mt-4 w-full inline-flex items-center justify-center gap-2 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 hover:text-blue-600 text-[12px] font-bold px-4 py-2 rounded-lg transition shadow-sm">
                            <i data-lucide="edit-3" class="w-3.5 h-3.5"></i> Kelola Pengerjaan
                        </button>
                    </div>
                @empty
                    <div class="col-span-full py-16 text-center text-gray-400">
                        <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3 shadow-inner">
                            <i data-lucide="inbox" class="w-6 h-6 text-gray-400"></i>
                        </div>
                        <p class="font-medium text-sm text-gray-500">Belum ada data pengerjaan di tahap ini.</p>
                    </div>
                @endforelse
            </div>

            {{-- Menampilkan Paginasi Laravel --}}
            <div class="mt-8 px-4 sm:px-0">
                {{ $bookings->links() }}
            </div>

        </div>

        {{-- Modal Update Status & Link --}}
        <div x-show="isOpen" class="fixed inset-0 z-[100] flex items-center justify-center px-4" x-cloak>
            <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" @click="isOpen = false" x-show="isOpen" x-transition.opacity></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden z-10"
                 x-show="isOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">

                <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <div>
                        <h3 class="text-[16px] font-bold text-gray-900">Kelola Pengerjaan</h3>
                        <p class="text-[12px] text-gray-500 font-medium" x-text="clientName"></p>
                    </div>
                    <button type="button" @click="isOpen = false" class="text-gray-400 hover:text-gray-600"><i data-lucide="x" class="w-5 h-5"></i></button>
                </div>

                <form :action="`/workboard/${activeId}/update`" method="POST" class="p-6">
                    @csrf

                    <div class="mb-5">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Update Status Pengerjaan</label>
                        <select name="status" x-model="status" class="w-full h-11 rounded-xl border border-gray-300 px-4 text-sm font-semibold focus:border-blue-500 focus:ring-blue-500 shadow-sm bg-white cursor-pointer">
                            <option value="Dijadwalkan">Belum Upload (Dijadwalkan)</option>
                            <option value="File Original Disiapkan">Belum Pilih Foto (File Disiapkan)</option>
                            <option value="Pilih Foto">Seleksi Masuk (Sedang Pilih Foto)</option>
                            <option value="Proses Edit">Sedang Diedit (Proses Editing)</option>
                            <option value="Selesai">Terkirim (Selesai)</option>
                        </select>
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Link Drive (File Original / Hasil)</label>
                        <input type="url" name="link_hasil" x-model="linkUrl" placeholder="https://drive.google.com/..." class="w-full h-11 rounded-xl border border-gray-300 px-4 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm text-blue-600">
                        <p class="text-[11px] text-gray-500 mt-2 leading-snug">Link ini akan muncul di halaman <b>Tracking Klien</b>. Bisa digunakan untuk mengirim file mentah agar klien bisa memilih foto, maupun untuk mengirim hasil akhir foto yang sudah diedit.</p>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="isOpen = false" class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-xl text-sm font-semibold hover:bg-gray-50 transition shadow-sm">Batal</button>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl text-sm font-bold shadow-sm transition flex items-center gap-2">
                            <i data-lucide="save" class="w-4 h-4"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function workboardModal() {
            return {
                isOpen: false,
                activeId: null,
                clientName: '',
                status: '',
                linkUrl: '',

                openModal(id, currentStatus, currentLink, name) {
                    this.activeId = id;
                    this.status = currentStatus;
                    this.linkUrl = currentLink || '';
                    this.clientName = name;
                    this.isOpen = true;
                }
            }
        }
    </script>
</x-app-layout>