{{-- resources/views/dashboard.blade.php --}}
<x-app-layout>
    <div x-data="dashboardApp()" class="px-4 sm:px-6 lg:px-8 py-8 bg-[#f5f7fb] min-h-screen overflow-x-hidden">

        {{-- 1. HEADER (Hanya Informasi & CTA) --}}
        <x-dashboard.dashboard-header />

        {{-- 2. TABEL DATA & TOOLBAR --}}
        <div id="data-area" class="w-full">
            
            {{-- TOOLBAR CONTROL (Search, Filters, View Toggle) Diletakkan Tepat di Atas Tabel --}}
            <div class="bg-white p-4 rounded-t-[24px] border border-gray-100 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
                
                {{-- Kiri: View Toggle --}}
                <div class="flex bg-gray-100 p-1.5 rounded-xl border border-gray-200 shrink-0 w-full md:w-auto">
                    <button @click="viewMode = 'table'" 
                        :class="viewMode === 'table' ? 'bg-white shadow-sm text-blue-600 font-bold' : 'text-gray-500 hover:text-gray-700'"
                        class="h-[38px] px-5 rounded-lg text-[13px] flex-1 md:flex-none flex items-center justify-center gap-2 transition-all">
                        <i data-lucide="list" class="w-4 h-4"></i> Tabel
                    </button>
                    <button @click="viewMode = 'calendar'" 
                        :class="viewMode === 'calendar' ? 'bg-white shadow-sm text-blue-600 font-bold' : 'text-gray-500 hover:text-gray-700'"
                        class="h-[38px] px-5 rounded-lg text-[13px] flex-1 md:flex-none flex items-center justify-center gap-2 transition-all">
                        <i data-lucide="calendar-days" class="w-4 h-4"></i> Kalender
                    </button>
                </div>

                {{-- Kanan: Filters & Search --}}
                <div class="flex flex-col sm:flex-row w-full md:w-auto gap-3 items-center">
                    
                    {{-- Filter Dropdowns --}}
                    <div class="flex gap-2 w-full sm:w-auto">
                        <select x-model="filterStatus" @change="emitFilter()" class="h-[42px] px-4 rounded-xl border-gray-200 bg-gray-50 text-[13px] text-gray-700 focus:ring-blue-500 outline-none w-full sm:w-[150px]">
                            <option value="semua">Semua Status</option>
                            <option value="dijadwalkan">Dijadwalkan</option>
                            <option value="selesai">Selesai</option>
                            <option value="dibatalkan">Dibatalkan</option>
                        </select>
                        <select x-model="filterPayment" @change="emitFilter()" class="h-[42px] px-4 rounded-xl border-gray-200 bg-gray-50 text-[13px] text-gray-700 focus:ring-blue-500 outline-none w-full sm:w-[160px]">
                            <option value="semua">Semua Pembayaran</option>
                            <option value="belum_bayar">Belum Bayar</option>
                            <option value="dp">Down Payment</option>
                            <option value="lunas">Lunas</option>
                        </select>
                    </div>

                    {{-- Search Bar --}}
                    <div class="relative w-full sm:w-[240px]">
                        <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                        <input type="text" x-model="searchQuery" @input.debounce.300ms="emitFilter()"
                            placeholder="Cari data booking..."
                            class="w-full h-[42px] pl-10 pr-4 rounded-xl border border-gray-200 bg-white text-[13px] focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    </div>

                </div>
            </div>

            {{-- AREA DATA (Tabel atau Kalender) --}}
            <div class="bg-white border-x border-b border-gray-100 rounded-b-[24px] shadow-sm overflow-hidden">
                <div x-show="viewMode === 'table'" x-transition.opacity>
                    <x-dashboard.booking-table />
                </div>
                <div x-show="viewMode === 'calendar'" x-transition.opacity x-cloak class="p-6">
                    <x-dashboard.booking-calendar />
                </div>
            </div>

        </div>

        {{-- 3. Modal: generate invoice --}}
        <x-dashboard.invoice-modal />

    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        function dashboardApp() {
            return {
                viewMode: 'table',
                filterStatus: 'semua',
                filterPayment: 'semua',
                searchQuery: '',

                currentDate: new Date(),
                monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],

                init() {
                    this.$nextTick(() => { if (window.lucide) lucide.createIcons() })

                    // Mengirim inisialisasi filter pertama kali ke tabel
                    this.emitFilter();

                    let pollInterval;
                    const startPolling = () => {
                        if (!pollInterval) {
                            pollInterval = setInterval(() => {
                                if (document.visibilityState === 'visible') {
                                    window.dispatchEvent(new CustomEvent('reload-data-silent'));
                                }
                            }, 5000);
                        }
                    };

                    const stopPolling = () => {
                        clearInterval(pollInterval);
                        pollInterval = null;
                    };

                    document.addEventListener('visibilitychange', () => {
                        if (document.visibilityState === 'visible') {
                            window.dispatchEvent(new CustomEvent('reload-data-silent'));
                            startPolling();
                        } else {
                            stopPolling();
                        }
                    });

                    startPolling();
                },

                // Kirim data filter ke komponen Tabel
                emitFilter() {
                    window.dispatchEvent(new CustomEvent('filter-changed', {
                        detail: {
                            status: this.filterStatus,
                            payment: this.filterPayment,
                            search: this.searchQuery
                        }
                    }));
                },

                get calendarTitle() { return `Kalender ${this.monthNames[this.currentDate.getMonth()]} ${this.currentDate.getFullYear()}` },
                get calendarDates() {
                    const y = this.currentDate.getFullYear(), m = this.currentDate.getMonth()
                    let d = new Date(y, m, 1).getDay(); d = d === 0 ? 6 : d - 1
                    const total = new Date(y, m + 1, 0).getDate()
                    const dates = [...Array(d).fill(null)]
                    for (let i = 1; i <= total; i++) dates.push(i)
                    while (dates.length % 7) dates.push(null)
                    return dates
                },
                prevMonth() { this.currentDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() - 1, 1); this.$nextTick(() => { if (window.lucide) lucide.createIcons() }) },
                nextMonth() { this.currentDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() + 1, 1); this.$nextTick(() => { if (window.lucide) lucide.createIcons() }) },
            }
        }

        document.addEventListener('DOMContentLoaded', () => { if (window.lucide) lucide.createIcons() })
    </script>

    <style>
        [x-cloak] { display: none !important; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
    </style>
</x-app-layout>