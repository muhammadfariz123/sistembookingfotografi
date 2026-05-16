<!-- resources/views/components/dashboard/booking-calendar.blade.php -->
<div
    x-data="bookingCalendar()"
    x-init="loadBookings()"
    @reload-bookings.window="loadBookings()"
    class="bg-white rounded-[28px] shadow-sm mt-7 border border-gray-100 p-5 overflow-hidden">

    <!-- HEADER -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5 mb-5">
        <div>
            <!-- JUDUL BULAN & TAHUN DINAMIS -->
            <h2 class="text-[20px] font-bold text-gray-800" x-text="calendarTitle"></h2>
            <!-- LEGEND -->
            <div class="flex flex-wrap items-center gap-5 mt-3 text-sm">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-blue-600"></div>
                    Dijadwalkan
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-green-600"></div>
                    Selesai
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-red-600"></div>
                    Dibatalkan
                </div>
            </div>
        </div>
        <!-- NAVIGATION -->
        <div class="flex items-center gap-2">
            <button @click="prevMonth()"
                class="w-10 h-10 rounded-xl bg-gray-100 hover:bg-gray-200 transition flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            <button @click="nextMonth()"
                class="w-10 h-10 rounded-xl bg-gray-100 hover:bg-gray-200 transition flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- LOADING -->
    <div x-show="loading" class="flex justify-center items-center py-16">
        <svg class="animate-spin w-8 h-8 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
        </svg>
    </div>

    <!-- KALENDER -->
    <div x-show="!loading">
        <!-- HARI -->
        <div class="grid grid-cols-7 gap-2 mb-3">
            <template x-for="day in days" :key="day">
                <div class="text-center text-sm font-medium text-gray-500">
                    <span x-text="day"></span>
                </div>
            </template>
        </div>

        <!-- TANGGAL -->
        <div class="grid grid-cols-7 gap-2">
            <template x-for="(item, index) in calendarDates" :key="index">
                <div>
                    <!-- KOTAK KOSONG (padding awal bulan) -->
                    <div x-show="!item" class="h-[140px] rounded-2xl bg-transparent"></div>

                    <!-- TANGGAL AKTIF -->
                    <div x-show="item"
                        :class="isToday(item) ? 'border-blue-400 bg-blue-50/30' : 'border-gray-200 bg-white'"
                        class="h-[140px] rounded-2xl border p-2 relative overflow-hidden flex flex-col">

                        <!-- NOMOR TANGGAL -->
                        <div class="flex justify-end mb-1">
                            <span
                                :class="isToday(item)
                                    ? 'bg-blue-600 text-white w-6 h-6 rounded-full flex items-center justify-center text-[12px] font-bold'
                                    : 'text-[13px] font-semibold text-gray-500'"
                                x-text="item">
                            </span>
                        </div>

                        <!-- BOOKING DI TANGGAL INI -->
                        <div class="flex-1 overflow-y-auto no-scrollbar space-y-1">
                            <template x-for="booking in getBookingsForDay(item)" :key="booking.id">
                                <div
                                    :class="bookingDotClass(booking.status)"
                                    class="text-white text-[10px] font-medium px-1.5 py-0.5 rounded-lg truncate cursor-pointer leading-tight"
                                    :title="booking.client_name + ' - ' + (booking.service_type?.name ?? '')"
                                    x-text="booking.client_name">
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- EMPTY STATE — tampil jika tidak ada booking sama sekali -->
        <template x-if="bookings.length === 0">
            <div class="py-16 text-center">
                <h3 class="text-[22px] font-bold text-gray-400">Belum ada data booking</h3>
                <p class="text-gray-400 mt-2">Klik Tambah Booking untuk memulai</p>
            </div>
        </template>
    </div>

    <!-- MODAL DETAIL BOOKING (klik tanggal yang ada booking) -->
    <div x-show="showDetail" x-cloak x-transition.opacity class="fixed inset-0 z-50">
        <div @click="showDetail = false" class="absolute inset-0 bg-black/35 backdrop-blur-sm"></div>
        <div class="relative min-h-screen flex items-center justify-center p-4">
            <div @click.stop class="bg-white w-full max-w-[480px] rounded-2xl shadow-2xl border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-[16px] font-bold text-gray-900"
                        x-text="'Booking ' + detailDate"></h3>
                    <button @click="showDetail = false" class="text-gray-400 hover:text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="px-5 py-4 max-h-[60vh] overflow-y-auto no-scrollbar space-y-3">
                    <template x-for="booking in detailBookings" :key="booking.id">
                        <div class="border border-gray-100 rounded-2xl p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-[14px] text-gray-900 truncate" x-text="booking.client_name"></p>
                                    <p class="text-[12px] text-gray-500 mt-0.5" x-text="booking.service_type?.name ?? '-'"></p>
                                    <p x-show="booking.client_contact" class="text-[12px] text-gray-400" x-text="booking.client_contact"></p>
                                </div>
                                <div class="flex flex-col items-end gap-1 shrink-0">
                                    <span :class="statusBadgeClass(booking.status)"
                                        class="text-[11px] font-semibold px-2 py-0.5 rounded-full"
                                        x-text="booking.status"></span>
                                    <span :class="paymentBadgeClass(booking.payment_status)"
                                        class="text-[11px] font-semibold px-2 py-0.5 rounded-full"
                                        x-text="booking.payment_status"></span>
                                </div>
                            </div>
                            <div class="mt-3 pt-3 border-t border-gray-50 grid grid-cols-2 gap-2 text-[12px]">
                                <div>
                                    <p class="text-gray-400">Total</p>
                                    <p class="font-semibold text-gray-800" x-text="formatCurrency(booking.total)"></p>
                                </div>
                                <div>
                                    <p class="text-gray-400">Sisa</p>
                                    <p class="font-semibold text-red-500" x-text="formatCurrency(booking.remaining)"></p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function bookingCalendar() {
    return {
        bookings:   [],
        loading:    true,
        showDetail: false,
        detailDate: '',
        detailBookings: [],

        // ── Data kalender ──────────────────────────────────────────
        days:       ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
        monthNames: ['Januari','Februari','Maret','April','Mei','Juni',
                     'Juli','Agustus','September','Oktober','November','Desember'],
        currentDate: new Date(),

        // ── Load booking dari backend ──────────────────────────────
        async loadBookings() {
            this.loading = true
            try {
                const res    = await fetch('/bookings', {
                    headers: { 'Accept': 'application/json' }
                })
                const result = await res.json()
                this.bookings = result.data ?? []
            } catch (e) {
                this.bookings = []
            } finally {
                this.loading = false
            }
        },

        // ── Judul bulan & tahun ────────────────────────────────────
        get calendarTitle() {
            return `${this.monthNames[this.currentDate.getMonth()]} ${this.currentDate.getFullYear()}`
        },

        // ── Grid tanggal ───────────────────────────────────────────
        get calendarDates() {
            const year     = this.currentDate.getFullYear()
            const month    = this.currentDate.getMonth()
            let firstDay   = new Date(year, month, 1).getDay()
            firstDay       = firstDay === 0 ? 6 : firstDay - 1
            const totalDays = new Date(year, month + 1, 0).getDate()
            const dates    = []
            for (let i = 0; i < firstDay; i++) dates.push(null)
            for (let day = 1; day <= totalDays; day++) dates.push(day)
            while (dates.length % 7 !== 0) dates.push(null)
            return dates
        },

        // ── Cek apakah tanggal adalah hari ini ─────────────────────
        isToday(day) {
            if (!day) return false
            const today = new Date()
            return (
                day === today.getDate() &&
                this.currentDate.getMonth() === today.getMonth() &&
                this.currentDate.getFullYear() === today.getFullYear()
            )
        },

        // ── Ambil booking untuk tanggal tertentu ───────────────────
        getBookingsForDay(day) {
            if (!day) return []
            const year  = this.currentDate.getFullYear()
            const month = this.currentDate.getMonth() + 1
            // Format: YYYY-MM-DD
            const pad   = n => String(n).padStart(2, '0')
            const dateStr = `${year}-${pad(month)}-${pad(day)}`

            return this.bookings.filter(b => {
                // Cek booking_date (1 hari)
                if (b.booking_date) {
                    const bd = String(b.booking_date).substring(0, 10)
                    if (bd === dateStr) return true
                }
                // Cek multi-day (start_date s/d end_date)
                if (b.start_date && b.end_date) {
                    const start = String(b.start_date).substring(0, 10)
                    const end   = String(b.end_date).substring(0, 10)
                    if (dateStr >= start && dateStr <= end) return true
                }
                return false
            })
        },

        // ── Warna dot berdasarkan status ───────────────────────────
        bookingDotClass(status) {
            switch (status) {
                case 'Selesai':    return 'bg-green-500'
                case 'Dibatalkan': return 'bg-red-500'
                default:           return 'bg-blue-600'   // Dijadwalkan
            }
        },

        // ── Badge status di modal detail ───────────────────────────
        statusBadgeClass(status) {
            switch (status) {
                case 'Selesai':    return 'bg-green-100 text-green-700'
                case 'Dibatalkan': return 'bg-red-100 text-red-600'
                default:           return 'bg-blue-100 text-blue-700'
            }
        },
        paymentBadgeClass(status) {
            switch (status) {
                case 'Lunas':        return 'bg-green-100 text-green-700'
                case 'Down Payment': return 'bg-orange-100 text-orange-600'
                default:             return 'bg-yellow-100 text-yellow-700'
            }
        },

        // ── Format currency ────────────────────────────────────────
        formatCurrency(value) {
            return new Intl.NumberFormat('id-ID', {
                style:                 'currency',
                currency:              'IDR',
                minimumFractionDigits: 0
            }).format(value || 0)
        },

        // ── Navigasi bulan ─────────────────────────────────────────
        prevMonth() {
            this.currentDate = new Date(
                this.currentDate.getFullYear(),
                this.currentDate.getMonth() - 1, 1
            )
        },
        nextMonth() {
            this.currentDate = new Date(
                this.currentDate.getFullYear(),
                this.currentDate.getMonth() + 1, 1
            )
        },

        // ── Klik tanggal — tampilkan detail booking ────────────────
        openDayDetail(day) {
            const bookingsOnDay = this.getBookingsForDay(day)
            if (bookingsOnDay.length === 0) return
            const pad = n => String(n).padStart(2, '0')
            this.detailDate    = `${pad(day)} ${this.monthNames[this.currentDate.getMonth()]} ${this.currentDate.getFullYear()}`
            this.detailBookings = bookingsOnDay
            this.showDetail    = true
        }
    }
}
</script>