{{-- resources/views/bookings/calendar.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-[20px] text-gray-800 leading-tight">
                    Kalender Booking
                </h2>
                <p class="text-[13px] text-gray-500 mt-0.5">
                    Lihat jadwal booking klien dalam tampilan kalender bulanan
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-5 sm:p-6"
            x-data="bookingCalendar()"
            x-init="loadBookings()"
            @reload-bookings.window="loadBookings()"
            @reload-data-silent.window="loadBookings(true)">

            {{-- HEADER KALENDER & TOMBOL BULAN --}}
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-5">
                <div>
                    <h2 class="text-[18px] font-bold text-gray-800" x-text="calendarTitle"></h2>
                    <div class="flex flex-wrap items-center gap-4 mt-2 text-xs">
                        <div class="flex items-center gap-1.5"><div class="w-2.5 h-2.5 rounded-full bg-blue-600"></div> Dijadwalkan</div>
                        <div class="flex items-center gap-1.5"><div class="w-2.5 h-2.5 rounded-full bg-green-600"></div> Selesai</div>
                        <div class="flex items-center gap-1.5"><div class="w-2.5 h-2.5 rounded-full bg-red-600"></div> Dibatalkan</div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button @click="prevMonth()" class="w-9 h-9 rounded-xl bg-gray-100 hover:bg-gray-200 transition flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button @click="nextMonth()" class="w-9 h-9 rounded-xl bg-gray-100 hover:bg-gray-200 transition flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>

            {{-- LOADING SPINNER --}}
            <div x-show="loading" class="flex justify-center items-center py-16">
                <svg class="animate-spin w-8 h-8 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
                </svg>
            </div>

            {{-- KONTEN UTAMA KALENDER --}}
            <div x-show="!loading" x-cloak>
                <div class="grid grid-cols-7 gap-2 mb-2">
                    <template x-for="day in days" :key="day">
                        <div class="text-center text-xs font-semibold text-gray-400 uppercase"><span x-text="day"></span></div>
                    </template>
                </div>

                <div class="grid grid-cols-7 gap-2">
                    <template x-for="(item, index) in calendarDates" :key="index">
                        <div>
                            {{-- Kotak kosong untuk tanggal di luar bulan aktif --}}
                            <div x-show="!item" class="h-[105px] rounded-xl bg-transparent"></div>
                            
                            {{-- Kotak Tanggal Aktif (Tinggi dipangkas ke 105px agar lebih kompak) --}}
                            <div x-show="item" :class="isToday(item) ? 'border-blue-400 bg-blue-50/30 ring-1 ring-blue-400' : 'border-gray-200 bg-white'" class="h-[105px] rounded-xl border p-1.5 relative overflow-hidden flex flex-col">
                                <div class="flex justify-end mb-1">
                                    <span :class="isToday(item) ? 'bg-blue-600 text-white w-5 h-5 rounded-full flex items-center justify-center text-[11px] font-bold' : 'text-[12px] font-semibold text-gray-500'" x-text="item"></span>
                                </div>
                                <div class="flex-1 overflow-y-auto no-scrollbar space-y-1">
                                    <template x-for="booking in getBookingsForDay(item)" :key="booking.id">
                                        <div :class="bookingDotClass(booking.status)" @click="openDayDetail(item)" class="text-white text-[10px] font-medium px-1.5 py-0.5 rounded-md truncate cursor-pointer leading-tight shadow-sm" :title="booking.client_name + ' - ' + (booking.service_type?.name ?? '')" x-text="booking.client_name"></div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- JIKA KOSONG --}}
                <template x-if="bookings.length === 0">
                    <div class="h-[240px] flex flex-col items-center justify-center px-4 text-center mt-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <h1 class="text-[18px] font-bold text-gray-400">Belum ada data booking</h1>
                    </div>
                </template>
            </div>

            {{-- MODAL DETAIL HARIAN --}}
            <div x-show="showDetail" x-cloak x-transition.opacity class="fixed inset-0 z-50">
                <div @click="showDetail = false" class="absolute inset-0 bg-black/35 backdrop-blur-sm"></div>
                <div class="relative min-h-screen flex items-center justify-center p-4">
                    <div @click.stop class="bg-white w-full max-w-[440px] rounded-2xl shadow-2xl border border-gray-200 overflow-hidden">
                        <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="text-[15px] font-bold text-gray-900" x-text="'Booking ' + detailDate"></h3>
                            <button @click="showDetail = false" class="text-gray-400 hover:text-gray-600"><svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                        </div>
                        <div class="px-5 py-4 max-h-[55vh] overflow-y-auto no-scrollbar space-y-3">
                            <template x-for="booking in detailBookings" :key="booking.id">
                                <div class="border border-gray-100 rounded-xl p-3.5 bg-gray-50/50">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex-1 min-w-0">
                                            <p class="font-bold text-[13px] text-gray-900 truncate" x-text="booking.client_name"></p>
                                            <p class="text-[11px] text-gray-500 mt-0.5" x-text="booking.service_type?.name ?? '-'"></p>
                                            
                                            <div x-show="booking.booking_time" class="flex items-center gap-1 mt-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <p class="text-[11px] font-medium text-gray-600" x-text="booking.booking_time"></p>
                                            </div>

                                            <p x-show="booking.client_contact" class="text-[11px] text-gray-400 mt-0.5" x-text="booking.client_contact"></p>
                                        </div>
                                        <div class="flex flex-col items-end gap-1 shrink-0">
                                            <span :class="statusBadgeClass(booking.status)" class="text-[10px] font-semibold px-2 py-0.5 rounded-full" x-text="booking.status"></span>
                                            <span :class="paymentBadgeClass(booking.payment_status)" class="text-[10px] font-semibold px-2 py-0.5 rounded-full" x-text="booking.payment_status"></span>
                                        </div>
                                    </div>
                                    <div class="mt-2.5 pt-2.5 border-t border-gray-100 grid grid-cols-2 gap-2 text-[11px]">
                                        <div><p class="text-gray-400">Total</p><p class="font-semibold text-gray-800" x-text="formatCurrency(booking.total)"></p></div>
                                        <div><p class="text-gray-400">Sisa</p><p class="font-semibold text-red-500" x-text="formatCurrency(booking.remaining)"></p></div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
    function bookingCalendar() {
        return {
            bookings: [],
            loading: true,
            showDetail: false,
            detailDate: '',
            detailBookings: [],

            days: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
            monthNames: ['Januari','Februari','Maret','April','Mei','Juni',
                         'Juli','Agustus','September','Oktober','November','Desember'],
            currentDate: new Date(),

            async loadBookings(silent = false) {
                if (!silent) this.loading = true
                try {
                    const res = await fetch('/bookings', { headers: { 'Accept': 'application/json' } })
                    const result = await res.json()
                    this.bookings = result.data ?? []
                } catch (e) {
                    this.bookings = []
                } finally {
                    if (!silent) this.loading = false
                }
            },

            get calendarTitle() { return `${this.monthNames[this.currentDate.getMonth()]} ${this.currentDate.getFullYear()}` },

            get calendarDates() {
                const year = this.currentDate.getFullYear()
                const month = this.currentDate.getMonth()
                let firstDay = new Date(year, month, 1).getDay()
                firstDay = firstDay === 0 ? 6 : firstDay - 1
                const totalDays = new Date(year, month + 1, 0).getDate()
                const dates = []
                for (let i = 0; i < firstDay; i++) dates.push(null)
                for (let day = 1; day <= totalDays; day++) dates.push(day)
                while (dates.length % 7 !== 0) dates.push(null)
                return dates
            },

            isToday(day) {
                if (!day) return false
                const today = new Date()
                return (day === today.getDate() && this.currentDate.getMonth() === today.getMonth() && this.currentDate.getFullYear() === today.getFullYear())
            },

            getBookingsForDay(day) {
                if (!day) return []
                const year = this.currentDate.getFullYear()
                const month = this.currentDate.getMonth() + 1
                const pad = n => String(n).padStart(2, '0')
                const dateStr = `${year}-${pad(month)}-${pad(day)}`

                return this.bookings.filter(b => {
                    if (b.booking_date) {
                        if (String(b.booking_date).substring(0, 10) === dateStr) return true
                    }
                    if (b.start_date && b.end_date) {
                        const start = String(b.start_date).substring(0, 10)
                        const end   = String(b.end_date).substring(0, 10)
                        if (dateStr >= start && dateStr <= end) return true
                    }
                    return false
                })
            },

            bookingDotClass(status) {
                switch (status) {
                    case 'Selesai':    return 'bg-green-500 hover:bg-green-600'
                    case 'Dibatalkan': return 'bg-red-500 hover:bg-red-600'
                    default:           return 'bg-blue-600 hover:bg-blue-700'
                }
            },

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

            formatCurrency(value) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value || 0)
            },

            prevMonth() { this.currentDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() - 1, 1) },
            nextMonth() { this.currentDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() + 1, 1) },

            openDayDetail(day) {
                const bookingsOnDay = this.getBookingsForDay(day)
                if (bookingsOnDay.length === 0) return
                const pad = n => String(n).padStart(2, '0')
                this.detailDate = `${pad(day)} ${this.monthNames[this.currentDate.getMonth()]} ${this.currentDate.getFullYear()}`
                this.detailBookings = bookingsOnDay
                this.showDetail = true
            }
        }
    }
    </script>
    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</x-app-layout>