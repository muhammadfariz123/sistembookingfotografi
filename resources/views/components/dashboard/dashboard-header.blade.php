{{-- resources/views/components/dashboard/dashboard-header.blade.php --}}
<div x-data="dashboardHeader()" 
     @reload-bookings.window="load()" 
     @reload-data-silent.window="load()"
     @sync-filter-status.window="activeStatus = $event.detail"
     @sync-filter-payment.window="activePayment = $event.detail"
     class="bg-white rounded-[24px] shadow-sm border border-gray-100 p-5 lg:p-6 mb-7 w-full">

    <div class="flex flex-col lg:flex-row gap-6 lg:gap-8 items-stretch">

        {{-- KOLOM KIRI (50%): SUMMARY CARDS --}}
        <div class="flex-1 min-w-0 flex flex-col justify-between gap-5">
            <div class="flex-1 flex flex-col">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-xs font-bold text-gray-500 uppercase tracking-wider flex items-center gap-2">Status Booking</h2>
                    <span class="text-[10px] font-medium text-blue-600 bg-blue-50 px-2 py-1 rounded border border-blue-100 flex items-center gap-1">
                        <i data-lucide="mouse-pointer-click" class="w-3 h-3"></i> Klik untuk filter
                    </span>
                </div>
                <div class="grid grid-cols-2 gap-3 h-full">
                    <template x-for="item in bookingCards" :key="item.key">
                        <button @click="filterByStatus(item.key)"
                            :class="activeStatus === item.key ? item.active : 'border-gray-200 hover:border-gray-300 bg-gray-50 hover:bg-gray-100/50'"
                            class="border rounded-2xl p-3 flex items-center gap-3 shadow-sm transition text-left group h-full">
                            <div class="w-10 h-10 rounded-[10px] flex items-center justify-center shrink-0 transition-transform group-hover:scale-105" :class="item.bg">
                                <i :data-lucide="item.icon" class="w-[18px] h-[18px]" :class="item.text"></i>
                            </div>
                            <div class="text-left min-w-0 flex-1">
                                <p class="text-gray-500 font-semibold text-[10px] sm:text-[11px] leading-tight truncate" x-text="item.title"></p>
                                <h1 class="text-[20px] sm:text-[22px] font-bold text-gray-800 leading-tight mt-0.5" x-text="summary[item.key] ?? 0"></h1>
                            </div>
                        </button>
                    </template>
                </div>
            </div>

            <div class="flex-1 flex flex-col">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-xs font-bold text-gray-500 uppercase tracking-wider flex items-center gap-2">Status Pembayaran</h2>
                    <span class="text-[10px] font-medium text-indigo-600 bg-indigo-50 px-2 py-1 rounded border border-indigo-100 flex items-center gap-1"
                          x-show="activeStatus !== 'semua'">
                        <i data-lucide="link" class="w-3 h-3"></i> Bisa dikombinasi
                    </span>
                </div>
                <div class="grid grid-cols-2 gap-3 h-full">
                    <template x-for="item in paymentCards" :key="item.key">
                        <button @click="filterByPayment(item.key)"
                            :class="activePayment === item.key ? item.active : 'border-gray-200 hover:border-gray-300 bg-gray-50 hover:bg-gray-100/50'"
                            class="border rounded-2xl p-3 flex items-center gap-3 shadow-sm transition text-left group h-full">
                            <div class="w-10 h-10 rounded-[10px] flex items-center justify-center shrink-0 transition-transform group-hover:scale-105" :class="item.bg">
                                <i :data-lucide="item.icon" class="w-[18px] h-[18px]" :class="item.text"></i>
                            </div>
                            <div class="text-left min-w-0 flex-1">
                                <p class="text-gray-500 font-semibold text-[10px] sm:text-[11px] leading-tight truncate" x-text="item.title"></p>
                                <h1 class="text-[20px] sm:text-[22px] font-bold text-gray-800 leading-tight mt-0.5" x-text="summary[item.key] ?? 0"></h1>
                            </div>
                        </button>
                    </template>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN (50%): NAVIGASI, PENCARIAN & AKSI --}}
        <div class="flex-1 min-w-0 flex flex-col justify-between">
            <div class="w-full">
                <x-dashboard.booking-link />
            </div>
            
            <div class="flex flex-col gap-3 my-auto py-4 lg:py-0">
                <div class="flex flex-col sm:flex-row gap-3 items-end">
                    <div class="w-full sm:w-auto flex flex-col justify-end">
                        <h2 class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">Navigasi Tampilan</h2>
                        <div class="flex bg-gray-100 p-1 rounded-xl border border-gray-200 shrink-0 h-[46px]">
                            <button @click="setView('table')" 
                                :class="activeView === 'table' ? 'bg-white shadow-sm text-blue-600 font-bold' : 'text-gray-600 hover:bg-gray-200'"
                                class="h-full px-5 rounded-lg text-[13px] flex flex-1 sm:flex-none items-center justify-center gap-2 transition-all">
                                <i data-lucide="list" class="w-4 h-4"></i> Tabel
                            </button>
                            <button @click="setView('calendar')" 
                                :class="activeView === 'calendar' ? 'bg-white shadow-sm text-blue-600 font-bold' : 'text-gray-600 hover:bg-gray-200'"
                                class="h-full px-5 rounded-lg text-[13px] flex flex-1 sm:flex-none items-center justify-center gap-2 transition-all">
                                <i data-lucide="calendar-days" class="w-4 h-4"></i> Kalender
                            </button>
                        </div>
                    </div>
                    <div class="w-full flex-1 flex flex-col justify-end">
                        <h2 class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2 hidden sm:block">Pencarian Cepat</h2>
                        <div class="relative w-full h-[46px]">
                            <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                            <input type="text" x-model="search" @input.debounce.300ms="handleSearch()"
                                placeholder="Cari klien, acara, kontak..."
                                class="w-full h-full pl-10 pr-8 rounded-xl border border-gray-200 bg-gray-50 text-[13px] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            <button x-show="search.trim()" type="button" @click="search = ''; handleSearch()"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-auto">
                <h2 class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">Tindakan Cepat</h2>
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('service-types.index') }}"
                        class="h-[46px] w-full rounded-xl border-2 border-blue-600 text-blue-600 font-semibold text-[13px] flex items-center justify-center gap-2 hover:bg-blue-50 transition">
                        <i data-lucide="briefcase" class="w-4 h-4"></i> Kelola Layanan
                    </a>
                    <a href="{{ route('bookings.create') }}"
                        class="h-[46px] w-full rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-[13px] flex items-center justify-center gap-2 shadow-md shadow-blue-200 transition">
                        <i data-lucide="plus" class="w-4 h-4"></i> Tambah Booking
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function dashboardHeader() {
    return {
        activeView: 'table',
        activeStatus:  'semua',
        activePayment: 'semua',
        search: '',
        filterMonth: '', 
        sortBy: 'newest', 
        summary: { semua:0, dijadwalkan:0, selesai:0, dibatalkan:0, belum_bayar:0, dp:0, lunas:0 },

        bookingCards: [
            { key:'semua',       title:'Semua Status Booking', icon:'users',          text:'text-blue-600',  bg:'bg-blue-100',  active:'border-blue-500 ring-2 ring-blue-100 bg-blue-50/30'  },
            { key:'dijadwalkan', title:'Dijadwalkan',   icon:'calendar-days',  text:'text-blue-600',  bg:'bg-blue-100',  active:'border-blue-500 ring-2 ring-blue-100 bg-blue-50/30'  },
            { key:'selesai',     title:'Selesai',       icon:'check-circle-2', text:'text-green-600', bg:'bg-green-100', active:'border-green-500 ring-2 ring-green-100 bg-green-50/30' },
            { key:'dibatalkan',  title:'Dibatalkan',    icon:'x-circle',       text:'text-red-600',   bg:'bg-red-100',   active:'border-red-500 ring-2 ring-red-100 bg-red-50/30'    },
        ],
        paymentCards: [
            { key:'semua',       title:'Semua Status Pembayaran', icon:'wallet',             text:'text-indigo-600', bg:'bg-indigo-100', active:'border-indigo-500 ring-2 ring-indigo-100 bg-indigo-50/30' },
            { key:'belum_bayar', title:'Belum Bayar',   icon:'alert-circle',       text:'text-yellow-500', bg:'bg-yellow-100', active:'border-yellow-400 ring-2 ring-yellow-100 bg-yellow-50/30' },
            { key:'dp',          title:'DP',            icon:'credit-card',        text:'text-orange-500', bg:'bg-orange-100', active:'border-orange-500 ring-2 ring-orange-100 bg-orange-50/30' },
            { key:'lunas',       title:'Lunas',         icon:'badge-dollar-sign',  text:'text-green-600',  bg:'bg-green-100',  active:'border-green-500 ring-2 ring-green-100 bg-green-50/30'  },
        ],

        init() {
            this.load()
            window.addEventListener('filter-changed', (e) => {
                if (e.detail.status  !== undefined) this.activeStatus  = e.detail.status
                if (e.detail.payment !== undefined) this.activePayment = e.detail.payment
            })
        },

        async load() {
            try {
                const res = await fetch('/bookings', { headers: { 'Accept': 'application/json' } })
                const r   = await res.json()
                
                if (r.summary) {
                    this.summary = {
                        semua:       r.summary.total         ?? 0,
                        dijadwalkan: r.summary.dijadwalkan   ?? 0,
                        selesai:     r.summary.selesai       ?? 0,
                        dibatalkan:  r.summary.dibatalkan    ?? 0,
                        belum_bayar: r.summary.belum_bayar   ?? 0,
                        dp:          r.summary.dp            ?? 0,
                        lunas:       r.summary.lunas         ?? 0,
                    }
                }
                this.$nextTick(() => { if (window.lucide) lucide.createIcons() })
            } catch {}
        },

        scrollToTable() {
            const tableElement = document.getElementById('tabel-booking');
            if (tableElement) {
                tableElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
            } else {
                window.scrollBy({ top: 300, behavior: 'smooth' });
            }
        },

        setView(mode) {
            this.activeView = mode
            window.dispatchEvent(new CustomEvent('set-view-mode', { detail: mode }))
            this.scrollToTable()
        },

        filterByStatus(key) {
            this.activeStatus = key
            this.emit()
            this.scrollToTable()
        },
        
        filterByPayment(key) {
            this.activePayment = key
            this.emit()
            this.scrollToTable()
        },

        handleSearch() {
            this.emit()
            if (this.search.trim() !== '') {
                this.scrollToTable()
            }
        },

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
        }
    }
}
</script>