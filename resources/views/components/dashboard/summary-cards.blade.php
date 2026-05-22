{{-- resources/views/components/dashboard/summary-cards.blade.php --}}
{{-- Komponen mandiri — fetch summary sendiri dari GET /bookings --}}
<div x-data="summaryCards()" @reload-bookings.window="load()">

    {{-- Status Booking --}}
    <div>
        <h2 class="text-[18px] font-semibold text-gray-800 mb-4">Status Booking</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
            <template x-for="item in bookingCards" :key="item.key">
                <button @click="filterByStatus(item.key)"
                    :class="activeStatus === item.key ? item.active : 'border-transparent'"
                    class="bg-white border-2 rounded-[22px] p-5 flex items-center justify-between shadow-sm transition">
                    <div class="text-left min-w-0">
                        <p class="text-gray-700 font-semibold text-[15px] truncate" x-text="item.title"></p>
                        <h1 class="text-[28px] font-bold mt-2" :class="item.text" x-text="summary[item.key] ?? 0"></h1>
                    </div>
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center shrink-0" :class="item.bg">
                        <i :data-lucide="item.icon" class="w-7 h-7" :class="item.text"></i>
                    </div>
                </button>
            </template>
        </div>
    </div>

    {{-- Status Pembayaran --}}
    <div class="mt-8">
        <h2 class="text-[18px] font-semibold text-gray-800 mb-4">Status Pembayaran</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <template x-for="item in paymentCards" :key="item.key">
                <button @click="filterByPayment(item.key)"
                    :class="activePayment === item.key ? item.active : 'border-transparent'"
                    class="bg-white border-2 rounded-[22px] p-5 flex items-center justify-between shadow-sm transition">
                    <div class="text-left min-w-0">
                        <p class="text-gray-700 font-semibold text-[15px] truncate" x-text="item.title"></p>
                        <h1 class="text-[28px] font-bold mt-2" :class="item.text" x-text="summary[item.key] ?? 0"></h1>
                    </div>
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center shrink-0" :class="item.bg">
                        <i :data-lucide="item.icon" class="w-7 h-7" :class="item.text"></i>
                    </div>
                </button>
            </template>
        </div>
    </div>
</div>

<script>
// summaryCards() — fetch summary + emit filter saat kartu diklik
function summaryCards() {
    return {
        activeStatus:  'semua',
        activePayment: 'semua',
        summary: { semua:0, dijadwalkan:0, selesai:0, dibatalkan:0, belum_bayar:0, dp:0, lunas:0 },

        bookingCards: [
            { key:'semua',       title:'Total Booking', icon:'users',           text:'text-blue-600',  bg:'bg-blue-100',  active:'border-blue-500'  },
            { key:'dijadwalkan', title:'Dijadwalkan',   icon:'calendar-days',   text:'text-blue-600',  bg:'bg-blue-100',  active:'border-blue-500'  },
            { key:'selesai',     title:'Selesai',       icon:'check-circle-2',  text:'text-green-600', bg:'bg-green-100', active:'border-green-500' },
            { key:'dibatalkan',  title:'Dibatalkan',    icon:'x-circle',        text:'text-red-600',   bg:'bg-red-100',   active:'border-red-500'   },
        ],
        paymentCards: [
            { key:'belum_bayar', title:'Belum Bayar', icon:'alert-circle',      text:'text-yellow-500', bg:'bg-yellow-100', active:'border-yellow-400' },
            { key:'dp',          title:'DP',          icon:'credit-card',       text:'text-orange-500', bg:'bg-orange-100', active:'border-orange-500' },
            { key:'lunas',       title:'Lunas',       icon:'badge-dollar-sign', text:'text-green-600',  bg:'bg-green-100',  active:'border-green-500'  },
        ],

        init() {
            this.load()
            // Sinkron highlight saat filter-panel mengubah status
            window.addEventListener('filter-changed', (e) => {
                if (e.detail.status  !== undefined) this.activeStatus  = e.detail.status
                if (e.detail.payment !== undefined) this.activePayment = e.detail.payment
            })
        },

        async load() {
            try {
                const res = await fetch('/bookings', { headers: { 'Accept': 'application/json' } })
                const r   = await res.json()
                if (r.summary) this.summary = {
                    semua:       r.summary.total       ?? 0,
                    dijadwalkan: r.summary.dijadwalkan ?? 0,
                    selesai:     r.summary.selesai     ?? 0,
                    dibatalkan:  r.summary.dibatalkan  ?? 0,
                    belum_bayar: r.summary.belum_bayar ?? 0,
                    dp:          r.summary.dp          ?? 0,
                    lunas:       r.summary.lunas       ?? 0,
                }
                this.$nextTick(() => { if (window.lucide) lucide.createIcons() })
            } catch {}
        },

        // Klik kartu → emit filter-changed ke booking-table
        filterByStatus(key) {
            this.activeStatus = key
            window.dispatchEvent(new CustomEvent('filter-changed', { detail: { status: key } }))
        },
        filterByPayment(key) {
            this.activePayment = key
            window.dispatchEvent(new CustomEvent('filter-changed', { detail: { payment: key } }))
        },
    }
}
</script>