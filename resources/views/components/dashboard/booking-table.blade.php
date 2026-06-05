{{-- resources/views/components/dashboard/booking-table.blade.php --}}
{{-- 
    [PENJELASAN UNTUK SIDANG]
    Tabel Booking bertindak sebagai pusat informasi (Storage/Output TPS).
    Optimalisasi Sistem: Fitur Long Polling (query ke database setiap 1 detik) telah dicabut
    demi prinsip Clean Code & efisiensi server. Sebagai gantinya, tabel ini diperbarui 
    (reactive) secara asinkron via Alpine.js Event Dispatcher hanya saat terjadi perubahan data.
--}}
<div x-data="bookingTable()" 
    @reload-bookings.window="loadBookings()"
    @filter-changed.window="applyFilter($event.detail)"
    class="bg-white rounded-[28px] shadow-sm mt-7 border border-gray-100 overflow-hidden">

    <div x-show="loading" class="h-[320px] flex flex-col items-center justify-center gap-3">
        <svg class="animate-spin w-8 h-8 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
        </svg>
        <p class="text-[14px] text-gray-400">Memuat data booking...</p>
    </div>

    <div x-show="!loading" x-cloak class="overflow-x-auto">
        <table class="w-full min-w-[900px]">
            <thead>
                <tr class="text-left text-gray-500 text-sm border-b border-gray-100">
                    <th class="px-8 py-6 whitespace-nowrap font-semibold tracking-wide">KLIEN</th>
                    <th class="px-8 py-6 whitespace-nowrap font-semibold tracking-wide">LAYANAN</th>
                    <th class="px-8 py-6 whitespace-nowrap font-semibold tracking-wide">TANGGAL & WAKTU</th>
                    <th class="px-8 py-6 whitespace-nowrap font-semibold tracking-wide">STATUS</th>
                    <th class="px-8 py-6 whitespace-nowrap font-semibold tracking-wide">PEMBAYARAN</th>
                    <th class="px-8 py-6 whitespace-nowrap font-semibold tracking-wide">AKSI</th>
                </tr>
            </thead>
            <tbody>
                <template x-if="filteredBookings.length > 0">
                    <template x-for="booking in filteredBookings" :key="booking.id">
                        <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition">
                            <td class="px-8 py-6 align-top">
                                <p class="font-bold text-[15px] text-gray-900" x-text="booking.client_name"></p>
                                <p class="text-[13px] text-gray-500 mt-0.5" x-text="booking.client_contact || ''"></p>
                                <div x-show="booking.client_address" class="flex items-center gap-1 mt-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <p class="text-[12px] text-gray-400" x-text="booking.client_address"></p>
                                </div>
                            </td>
                            <td class="px-8 py-6 align-top">
                                <p class="font-semibold text-[14px] text-gray-900" x-text="booking.service_type?.name || '-'"></p>
                                <p class="text-[13px] text-gray-500 mt-0.5" x-text="formatCurrency(booking.unit_price)"></p>
                                <p x-show="booking.service_type?.description" class="text-[12px] text-gray-400 mt-1 max-w-[200px] line-clamp-2" x-text="booking.service_type?.description"></p>
                            </td>
                            <td class="px-8 py-6 align-top whitespace-nowrap">
                                <template x-if="booking.booking_date && !booking.start_date">
                                    <div>
                                        <p class="text-[14px] font-medium text-gray-800" x-text="formatDate(booking.booking_date)"></p>
                                        <p x-show="booking.booking_time" class="text-[12px] text-gray-500 mt-0.5" x-text="booking.booking_time"></p>
                                    </div>
                                </template>
                                <template x-if="booking.start_date">
                                    <div>
                                        <p class="text-[14px] font-medium text-gray-800" x-text="formatDate(booking.start_date)"></p>
                                        <p class="text-[12px] text-gray-500 mt-0.5">s/d <span x-text="formatDate(booking.end_date)"></span></p>
                                        <p x-show="booking.booking_time" class="text-[12px] text-gray-400" x-text="booking.booking_time"></p>
                                    </div>
                                </template>
                                <template x-if="!booking.booking_date && !booking.start_date">
                                    <p class="text-[13px] text-gray-400">-</p>
                                </template>
                            </td>
                            <td class="px-8 py-6 align-top">
                                <span :class="statusClass(booking.status)" class="inline-flex items-center px-3 py-1 rounded-full text-[12px] font-semibold">
                                    <span x-text="booking.status"></span>
                                </span>
                            </td>
                            <td class="px-8 py-6 align-top">
                                <span :class="paymentClass(booking.payment_status)" class="inline-flex items-center px-3 py-1 rounded-full text-[12px] font-semibold mb-2">
                                    <span x-text="paymentLabel(booking.payment_status)"></span>
                                </span>
                                <div class="space-y-0.5 mt-1">
                                    <p class="text-[13px] text-gray-600">Total: <span class="font-semibold text-gray-800" x-text="formatCurrency(booking.total)"></span></p>
                                    <p class="text-[13px] text-gray-500">Dibayar: <span x-text="formatCurrency(booking.paid_amount)"></span></p>
                                    <p x-show="booking.remaining > 0" class="text-[13px] font-semibold text-red-500">Sisa: <span x-text="formatCurrency(booking.remaining)"></span></p>
                                </div>
                            </td>
                            <td class="px-8 py-6 align-top">
                                <div class="flex items-center gap-2">
                                    <button type="button" @click="openInvoice(booking)" class="w-9 h-9 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-600 flex items-center justify-center transition" title="Generate Invoice">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </button>

                                    <a :href="`/bookings/${booking.id}/edit`" class="w-9 h-9 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-600 flex items-center justify-center transition" title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>

                                    <button type="button" @click="deleteBooking(booking)" class="w-9 h-9 rounded-xl bg-red-50 hover:bg-red-100 text-red-500 flex items-center justify-center transition" title="Hapus">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </template>

                <template x-if="!loading && filteredBookings.length === 0 && bookings.length === 0">
                    <tr>
                        <td colspan="6">
                            <div class="h-[320px] flex flex-col items-center justify-center px-4 text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-14 h-14 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <h1 class="text-[20px] sm:text-[24px] font-bold text-gray-400">Belum ada data booking</h1>
                                <p class="text-gray-400 mt-2 text-[13px] sm:text-[14px]">Klik Tambah Booking untuk memulai</p>
                            </div>
                        </td>
                    </tr>
                </template>

                <template x-if="!loading && filteredBookings.length === 0 && bookings.length > 0">
                    <tr>
                        <td colspan="6">
                            <div class="h-[320px] flex flex-col items-center justify-center px-4 text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-14 h-14 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <h1 class="text-[18px] sm:text-[20px] font-bold text-gray-400">Tidak ada hasil ditemukan</h1>
                                <template x-if="activeSearch.trim()">
                                    <p class="text-gray-400 mt-2 text-[13px] sm:text-[14px]">Tidak ada booking yang cocok dengan "<span class="font-semibold text-gray-600" x-text="activeSearch"></span>"</p>
                                </template>
                                <template x-if="!activeSearch.trim()">
                                    <p class="text-gray-400 mt-2 text-[13px] sm:text-[14px]">Coba ubah filter status, pembayaran, atau bulan</p>
                                </template>
                            </div>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</div>

<script>
    function bookingTable() {
        return {
            bookings: [],
            loading: true,
            activeStatus: 'semua',
            activePayment: 'semua',
            activeMonth: '',
            activeSortBy: 'newest',
            activeSearch: '',

            init() {
                // Diinisialisasi sekali saat pertama komponen dimuat (menghapus setInterval)
                this.loadBookings()
            },

            async loadBookings(silent = false) {
                if (!silent) this.loading = true
                try {
                    const res = await fetch('/bookings', {
                        headers: { 'Accept': 'application/json' }
                    })
                    const result = await res.json()
                    this.bookings = result.data ?? []
                } catch (e) {
                    this.bookings = []
                } finally {
                    if (!silent) this.loading = false
                }
            },

            applyFilter(detail) {
                if (detail.status !== undefined) this.activeStatus = detail.status
                if (detail.payment !== undefined) this.activePayment = detail.payment
                if (detail.month !== undefined) this.activeMonth = detail.month
                if (detail.sortBy !== undefined) this.activeSortBy = detail.sortBy
                if (detail.search !== undefined) this.activeSearch = detail.search
            },

            get filteredBookings() {
                let result = [...this.bookings]
                if (this.activeStatus && this.activeStatus !== 'semua') {
                    const map = { dijadwalkan: 'Dijadwalkan', selesai: 'Selesai', dibatalkan: 'Dibatalkan' }
                    result = result.filter(b => b.status === (map[this.activeStatus] ?? this.activeStatus))
                }
                if (this.activePayment && this.activePayment !== 'semua') {
                    const map = { belum_bayar: 'Belum Bayar', dp: 'Down Payment', lunas: 'Lunas' }
                    result = result.filter(b => b.payment_status === (map[this.activePayment] ?? this.activePayment))
                }
                if (this.activeMonth && this.activeMonth !== '') {
                    const month = parseInt(this.activeMonth)
                    result = result.filter(b => {
                        const dateStr = b.booking_date ?? b.start_date
                        if (!dateStr) return false
                        return new Date(dateStr).getMonth() + 1 === month
                    })
                }
                if (this.activeSearch.trim()) {
                    const q = this.activeSearch.toLowerCase().trim()
                    result = result.filter(b =>
                        (b.client_name ?? '').toLowerCase().includes(q) ||
                        (b.client_contact ?? '').toLowerCase().includes(q) ||
                        (b.client_address ?? '').toLowerCase().includes(q) ||
                        (b.service_type?.name ?? '').toLowerCase().includes(q)
                    )
                }
                switch (this.activeSortBy) {
                    case 'newest': result.sort((a, b) => new Date(b.created_at) - new Date(a.created_at)); break
                    case 'updated': result.sort((a, b) => new Date(b.updated_at) - new Date(a.updated_at)); break
                    case 'oldest': result.sort((a, b) => new Date(a.created_at) - new Date(b.created_at)); break
                    case 'date_asc': result.sort((a, b) => new Date(a.booking_date ?? a.start_date ?? 0) - new Date(b.booking_date ?? b.start_date ?? 0)); break
                    case 'date_desc': result.sort((a, b) => new Date(b.booking_date ?? b.start_date ?? 0) - new Date(a.booking_date ?? a.start_date ?? 0)); break
                    case 'name_az': result.sort((a, b) => (a.client_name ?? '').localeCompare(b.client_name ?? '', 'id')); break
                }
                return result
            },

            openInvoice(booking) {
                window.dispatchEvent(new CustomEvent('open-invoice', { detail: booking }))
            },

            formatDate(dateStr) {
                if (!dateStr) return '-'
                return new Intl.DateTimeFormat('id-ID', {
                    weekday: 'short', day: 'numeric', month: 'long', year: 'numeric'
                }).format(new Date(dateStr))
            },

            formatCurrency(value) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency', currency: 'IDR', minimumFractionDigits: 0
                }).format(value || 0)
            },

            statusClass(status) {
                switch (status) {
                    case 'Selesai': return 'bg-green-100 text-green-700'
                    case 'Dibatalkan': return 'bg-red-100 text-red-600'
                    default: return 'bg-blue-100 text-blue-700'
                }
            },

            paymentClass(status) {
                switch (status) {
                    case 'Lunas': return 'bg-green-100 text-green-700'
                    case 'Down Payment': return 'bg-orange-100 text-orange-600'
                    default: return 'bg-yellow-100 text-yellow-700'
                }
            },

            paymentLabel(status) {
                switch (status) {
                    case 'Lunas': return 'Lunas'
                    case 'Down Payment': return 'Down Payment (DP)'
                    default: return 'Belum Bayar'
                }
            },

            async deleteBooking(booking) {
                const confirm = await Swal.fire({
                    title: `Hapus booking "${booking.client_name}"?`,
                    text: 'Tindakan ini tidak dapat dibatalkan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: { popup: 'rounded-[28px]' }
                })
                if (!confirm.isConfirmed) return
                
                try {
                    const res = await fetch(`/bookings/${booking.id}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    const data = await res.json()
                    if (!res.ok) {
                        Swal.fire({
                            icon: 'error', title: 'Gagal!', text: data.message ?? 'Gagal menghapus.',
                            confirmButtonColor: '#2563eb', customClass: { popup: 'rounded-[28px]' }
                        })
                        return
                    }
                    this.bookings = this.bookings.filter(b => b.id !== booking.id)
                    Swal.fire({
                        icon: 'success', title: 'Dihapus!', text: data.message,
                        confirmButtonColor: '#2563eb', timer: 2000,
                        timerProgressBar: true, showConfirmButton: false,
                        customClass: { popup: 'rounded-[28px]' }
                    }).then(() => {
                        window.dispatchEvent(new CustomEvent('reload-bookings'))
                    })
                } catch (err) {
                    Swal.fire({
                        icon: 'error', title: 'Gagal!', text: 'Gagal terhubung ke server.',
                        confirmButtonColor: '#2563eb', customClass: { popup: 'rounded-[28px]' }
                    })
                }
            }
        }
    }
</script>