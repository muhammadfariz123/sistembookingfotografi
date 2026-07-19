{{-- resources/views/components/dashboard/dashboard-header.blade.php --}}
<div x-data="dashboardHeader()" @reload-bookings.window="load()" @reload-data-silent.window="load()" class="w-full mb-7">

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5 items-stretch">

        {{-- KOLOM KIRI (2/3): INFORMASI STATUS --}}
        <div class="xl:col-span-2 bg-white rounded-[24px] shadow-sm border border-gray-100 p-5 flex flex-col justify-between gap-5">
            
            {{-- STATUS BOOKING --}}
            <div class="flex flex-col flex-1">
                <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2.5 shrink-0">Ringkasan Status Booking</h2>
                {{-- Diubah grid cols agar muat 5 status --}}
                <div class="grid grid-cols-2 md:grid-cols-5 gap-3 h-full">
                    <template x-for="item in bookingCards" :key="item.key">
                        <div class="border border-gray-100 bg-gray-50/30 rounded-xl p-3 flex flex-col items-start gap-2.5 h-full">
                            <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0" :class="item.bg">
                                <i :data-lucide="item.icon" class="w-4 h-4" :class="item.text"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-gray-500 font-medium text-[11px] truncate" x-text="item.title"></p>
                                <h1 class="text-xl font-bold text-gray-900 leading-tight mt-0.5" x-text="summary[item.key] ?? 0"></h1>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- STATUS PEMBAYARAN --}}
            <div class="flex flex-col flex-1">
                <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2.5 shrink-0">Ringkasan Status Pembayaran</h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 h-full">
                    <template x-for="item in paymentCards" :key="item.key">
                        <div class="border border-gray-100 bg-gray-50/30 rounded-xl p-3 flex flex-col sm:flex-row items-start sm:items-center gap-2.5 h-full">
                            <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0" :class="item.bg">
                                <i :data-lucide="item.icon" class="w-4 h-4" :class="item.text"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-gray-500 font-medium text-[11px] truncate" x-text="item.title"></p>
                                <h1 class="text-xl font-bold text-gray-900 leading-tight mt-0.5" x-text="summary[item.key] ?? 0"></h1>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

        </div>

        {{-- KOLOM KANAN (1/3): PANEL TINDAKAN UTAMA --}}
        <div class="xl:col-span-1 bg-white rounded-[24px] shadow-sm border border-gray-100 p-5 flex flex-col h-full">
            
            <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3 shrink-0">Tindakan Cepat</h2>
            <div class="flex flex-col gap-2.5">
                <a href="{{ route('bookings.create') }}" class="h-[46px] w-full rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-[13px] flex items-center justify-center gap-2 shadow-lg shadow-blue-500/20 transition-all">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i> Tambah Booking Baru
                </a>
                <a href="{{ route('service-types.index') }}" class="h-[46px] w-full rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50 hover:text-blue-600 font-bold text-[13px] flex items-center justify-center gap-2 transition-all">
                    <i data-lucide="briefcase" class="w-4 h-4 text-gray-400"></i> Kelola Layanan / Paket
                </a>
            </div>

            <hr class="border-gray-100 my-4">

            <div class="mt-auto">
                <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2.5">Tautan Form Klien Publik</h2>
                <x-dashboard.booking-link />
            </div>

        </div>

    </div>
</div>

<script>
function dashboardHeader() {
    return {
        // [DIUBAH]: Tambah state baru di summary
        summary: { 
            dijadwalkan:0, pembayaran_tertunda:0, proses_edit:0, selesai:0, dibatalkan:0, 
            pending:0, dp:0, lunas:0 
        }, 

        // [DIUBAH]: Tambahkan kartu baru di bookingCards
        bookingCards: [
            { key:'dijadwalkan', title:'Dijadwalkan', icon:'calendar-clock', text:'text-blue-600', bg:'bg-blue-100' },
            { key:'pembayaran_tertunda', title:'Pembayaran Tertunda', icon:'clock', text:'text-orange-600', bg:'bg-orange-100' }, 
            { key:'proses_edit', title:'Proses Edit', icon:'image', text:'text-purple-600', bg:'bg-purple-100' },      
            { key:'selesai', title:'Selesai', icon:'check-circle-2', text:'text-green-600', bg:'bg-green-100' },
            { key:'dibatalkan', title:'Dibatalkan', icon:'x-circle', text:'text-red-600', bg:'bg-red-100' },
        ],
        paymentCards: [
            { key:'pending', title:'Pending', icon:'alert-circle', text:'text-yellow-600', bg:'bg-yellow-100' },
            { key:'dp', title:'Down Payment', icon:'credit-card', text:'text-orange-600', bg:'bg-orange-100' },
            { key:'lunas', title:'Lunas', icon:'badge-dollar-sign', text:'text-green-600', bg:'bg-green-100' },
        ],

        init() { this.load() },

        async load() {
            try {
                const res = await fetch('/bookings', { headers: { 'Accept': 'application/json' } })
                const r   = await res.json()
                if (r.summary) {
                    this.summary = {
                        dijadwalkan:         r.summary.dijadwalkan ?? 0,
                        pembayaran_tertunda: r.summary.pembayaran_tertunda ?? 0,
                        proses_edit:         r.summary.proses_edit ?? 0,
                        selesai:             r.summary.selesai     ?? 0,
                        dibatalkan:          r.summary.dibatalkan  ?? 0,
                        pending:             r.summary.pending     ?? 0,
                        dp:                  r.summary.dp          ?? 0,
                        lunas:               r.summary.lunas       ?? 0,
                    }
                }
                this.$nextTick(() => { if (window.lucide) lucide.createIcons() })
            } catch {}
        }
    }
}
</script>