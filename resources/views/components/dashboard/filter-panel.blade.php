{{-- resources/views/components/dashboard/filter-panel.blade.php --}}
{{-- 
    [PENJELASAN UNTUK SIDANG]
    Filter Panel ini mengadopsi "Client-Side Filtering" dengan Alpine.js.
    Alih-alih melakukan query berulang ke database (server) setiap kali admin mengetik
    pencarian, penyaringan dilakukan langsung di browser (Frontend).
    Ini sangat menghemat resource server (Optimasi Kinerja Sistem).
--}}
<div x-data="filterPanel()" 
     @sync-filter-status.window="setStatus($event.detail)"
     @sync-filter-payment.window="setPayment($event.detail)"
     class="bg-white rounded-[28px] shadow-sm mt-7 border border-gray-100 p-4 sm:p-6 overflow-hidden">

    {{-- Memanggil komponen link booking klien --}}
    <x-dashboard.booking-link />

    <div class="mt-7 flex flex-col xl:flex-row xl:items-center xl:justify-between gap-8">

        {{-- BAGIAN KIRI: Toggle Tabel/Kalender & Pencarian --}}
        <div class="flex flex-wrap xl:flex-nowrap items-center gap-3 min-w-0 xl:flex-1">

            <button @click="setView('table')" 
                :class="activeView === 'table' ? 'bg-blue-600 text-white shadow-lg shadow-blue-200' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                class="px-6 h-[48px] rounded-2xl text-sm font-medium flex items-center gap-2 transition whitespace-nowrap shrink-0">
                <i data-lucide="list" class="w-4 h-4"></i> Tabel
            </button>

            <button @click="setView('calendar')" 
                :class="activeView === 'calendar' ? 'bg-blue-600 text-white shadow-lg shadow-blue-200' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                class="px-6 h-[48px] rounded-2xl text-sm font-medium flex items-center gap-2 transition whitespace-nowrap shrink-0">
                <i data-lucide="calendar-days" class="w-4 h-4"></i> Kalender
            </button>

            <div class="relative min-w-0 flex-1 w-full xl:max-w-[320px]">
                <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                <input type="text" x-model="search" @input.debounce.300ms="emit()"
                    placeholder="Cari nama klien, kontak, alamat, layanan..."
                    class="w-full pl-11 pr-10 h-[48px] rounded-2xl border border-gray-200 bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                
                <button x-show="search.trim()" type="button" @click="search = ''; emit()"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- BAGIAN TENGAH: Opsi Filter Data --}}
        <div class="flex flex-col gap-4 w-full xl:w-auto xl:flex-shrink-0">

            <x-dashboard.filter-row label="Status">
                <div class="grid grid-cols-2 gap-2">
                    <template x-for="item in statusButtons" :key="item.key">
                        <button @click="setStatus(item.key)"
                            :class="activeStatus === item.key ? 'bg-[#2d3d5c] text-white' : 'bg-gray-100 text-gray-700'"
                            class="px-4 h-[36px] rounded-xl text-sm font-semibold whitespace-nowrap transition">
                            <span x-text="item.title"></span>
                        </button>
                    </template>
                </div>
            </x-dashboard.filter-row>

            <x-dashboard.filter-row label="Pembayaran">
                <div class="grid grid-cols-2 gap-2">
                    <template x-for="item in paymentButtons" :key="item.key">
                        <button @click="setPayment(item.key)"
                            :class="activePayment === item.key ? 'bg-[#2d3d5c] text-white' : 'bg-gray-100 text-gray-700'"
                            class="px-4 h-[36px] rounded-xl text-sm font-semibold whitespace-nowrap transition">
                            <span x-text="item.title"></span>
                        </button>
                    </template>
                </div>
            </x-dashboard.filter-row>

            <x-dashboard.filter-row label="Bulan">
                <select x-model="filterMonth" @change="emit()"
                    class="h-[42px] rounded-2xl border-gray-300 text-sm w-full sm:w-[180px] focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">Semua Bulan</option>
                    <option value="1">Januari</option>
                    <option value="2">Februari</option>
                    <option value="3">Maret</option>
                    <option value="4">April</option>
                    <option value="5">Mei</option>
                    <option value="6">Juni</option>
                    <option value="7">Juli</option>
                    <option value="8">Agustus</option>
                    <option value="9">September</option>
                    <option value="10">Oktober</option>
                    <option value="11">November</option>
                    <option value="12">Desember</option>
                </select>
            </x-dashboard.filter-row>

            <x-dashboard.filter-row label="Urutkan">
                <select x-model="sortBy" @change="emit()"
                    class="h-[42px] rounded-2xl border-gray-300 text-sm w-full sm:w-[220px] focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="newest">Terbaru Dibuat</option>
                    <option value="updated">Terbaru Diperbarui</option>
                    <option value="oldest">Terlama Dibuat</option>
                    <option value="date_asc">Tanggal Booking ↑</option>
                    <option value="date_desc">Tanggal Booking ↓</option>
                    <option value="name_az">Nama Klien A-Z</option>
                </select>
            </x-dashboard.filter-row>
        </div>

        {{-- BAGIAN KANAN: Tombol Aksi (Kelola & Tambah) --}}
        <div class="flex flex-wrap sm:flex-nowrap items-center justify-start xl:justify-end gap-3 w-full xl:w-auto xl:flex-shrink-0">
            <a href="{{ route('service-types.index') }}"
                class="h-[48px] px-6 rounded-2xl border-2 border-blue-600 text-blue-600 font-semibold text-sm flex items-center gap-2 whitespace-nowrap hover:bg-blue-50 transition">
                <i data-lucide="briefcase" class="w-4 h-4"></i> Kelola Layanan
            </a>
            <a href="{{ route('bookings.create') }}"
                class="h-[48px] px-6 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm flex items-center justify-center gap-2 whitespace-nowrap shadow-lg shadow-blue-200 transition">
                <i data-lucide="plus" class="w-4 h-4"></i> Tambah Booking
            </a>
        </div>
    </div>
</div>

<script>
    function filterPanel() {
        return {
            activeView: 'table',
            activeStatus: 'semua',
            activePayment: 'semua',
            search: '',
            filterMonth: '',
            sortBy: 'newest',

            statusButtons: [
                { key: 'semua', title: 'Semua' },
                { key: 'dijadwalkan', title: 'Dijadwalkan' },
                { key: 'selesai', title: 'Selesai' },
                { key: 'dibatalkan', title: 'Dibatalkan' },
            ],
            paymentButtons: [
                { key: 'semua', title: 'Semua' },
                { key: 'belum_bayar', title: 'Belum Bayar' },
                { key: 'dp', title: 'DP' },
                { key: 'lunas', title: 'Lunas' },
            ],

            // Emit Event: Switch antara view Tabel dan Kalender
            setView(mode) {
                this.activeView = mode
                window.dispatchEvent(new CustomEvent('set-view-mode', { detail: mode }))
            },

            // Set filter & otomatis sinkronisasi warna kartu (Summary Cards)
            setStatus(key) {
                this.activeStatus = key
                this.emit()
                window.dispatchEvent(new CustomEvent('sync-filter-status', { detail: key }))
            },

            setPayment(key) {
                this.activePayment = key
                this.emit()
                window.dispatchEvent(new CustomEvent('sync-filter-payment', { detail: key }))
            },

            // Broadcast (Emit) semua pengaturan filter ke Tabel Booking
            emit() {
                window.dispatchEvent(new CustomEvent('filter-changed', {
                    detail: {
                        status: this.activeStatus,
                        payment: this.activePayment,
                        search: this.search,
                        month: this.filterMonth,
                        sortBy: this.sortBy,
                    }
                }))
            },
        }
    }
</script>