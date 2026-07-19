{{-- resources/views/components/dashboard/booking-table.blade.php --}}
<div x-data="bookingTable()" 
    @reload-bookings.window="loadBookings()"
    @reload-data-silent.window="loadBookings(true)"
    @filter-changed.window="applyFilter($event.detail)"
    class="bg-white w-full overflow-hidden relative">

    {{-- State Loading --}}
    <div x-show="loading" class="h-[320px] flex flex-col items-center justify-center gap-3">
        <svg class="animate-spin w-8 h-8 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
        </svg>
        <p class="text-[14px] text-gray-400">Memuat data booking...</p>
    </div>

    {{-- Tabel Data --}}
    <div x-show="!loading" x-cloak class="overflow-x-auto">
        <table class="w-full min-w-[900px]">
            <thead>
                <tr class="text-left text-gray-500 text-sm border-b border-gray-100 bg-gray-50/50">
                    <th class="px-8 py-6 whitespace-nowrap font-semibold tracking-wide">KLIEN</th>
                    <th class="px-8 py-6 whitespace-nowrap font-semibold tracking-wide">LAYANAN</th>
                    <th class="px-8 py-6 whitespace-nowrap font-semibold tracking-wide">TANGGAL & WAKTU</th>
                    <th class="px-8 py-6 whitespace-nowrap font-semibold tracking-wide">STATUS BOOKING</th>
                    <th class="px-8 py-6 whitespace-nowrap font-semibold tracking-wide">STATUS PEMBAYARAN</th>
                    <th class="px-8 py-6 whitespace-nowrap font-semibold tracking-wide text-center">AKSI</th>
                </tr>
            </thead>
            <tbody>
                <template x-if="filteredBookings.length > 0">
                    <template x-for="booking in filteredBookings" :key="booking.id">
                        <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition">
                            <td class="px-8 py-6 align-top">
                                <p class="font-bold text-[15px] text-gray-900" x-text="booking.client_name"></p>
                                <p class="text-[13px] text-gray-500 mt-0.5" x-text="booking.client_contact || '-'"></p>
                                <div x-show="booking.client_address" class="flex items-start gap-1.5 mt-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-gray-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <p class="text-[12px] text-gray-400 leading-tight" x-text="booking.client_address"></p>
                                </div>
                            </td>
                            <td class="px-8 py-6 align-top">
                                <p class="font-semibold text-[14px] text-gray-900" x-text="booking.service_type?.name || '-'"></p>
                                <p class="text-[13px] text-gray-500 mt-0.5 font-medium" x-text="formatCurrency(booking.unit_price)"></p>
                                <p x-show="booking.service_type?.description" class="text-[12px] text-gray-400 mt-1 max-w-[200px] line-clamp-2" x-text="booking.service_type?.description"></p>
                            </td>
                            <td class="px-8 py-6 align-top whitespace-nowrap">
                                <template x-if="(booking.booking_date || booking.start_date) && (!booking.start_date || !booking.end_date || booking.start_date.substring(0,10) === booking.end_date.substring(0,10))">
                                    <div>
                                        <p class="text-[14px] font-medium text-gray-800" x-text="formatDate(booking.booking_date || booking.start_date)"></p>
                                        <p x-show="booking.booking_time" class="text-[12px] text-gray-500 mt-0.5" x-text="booking.booking_time"></p>
                                    </div>
                                </template>
                                <template x-if="booking.start_date && booking.end_date && booking.start_date.substring(0,10) !== booking.end_date.substring(0,10)">
                                    <div>
                                        <p class="text-[14px] font-medium text-gray-800" x-text="formatDate(booking.start_date)"></p>
                                        <p class="text-[12px] text-gray-500 mt-0.5">s/d <span x-text="formatDate(booking.end_date)"></span></p>
                                        <p x-show="booking.booking_time" class="text-[12px] text-gray-400 mt-0.5" x-text="booking.booking_time"></p>
                                    </div>
                                </template>
                                <template x-if="!booking.booking_date && !booking.start_date">
                                    <p class="text-[13px] text-gray-400">-</p>
                                </template>
                            </td>
                            <td class="px-8 py-6 align-top">
                                <span :class="statusClass(booking.status)" class="inline-flex items-center px-3 py-1 rounded-full text-[12px] font-semibold border">
                                    <span x-text="booking.status"></span>
                                </span>
                            </td>
                            <td class="px-8 py-6 align-top">
                                <span :class="paymentClass(booking.payment_status)" class="inline-flex items-center px-3 py-1 rounded-full text-[12px] font-semibold mb-2 border">
                                    <span x-text="paymentLabel(booking.payment_status)"></span>
                                </span>
                                <div class="space-y-0.5 mt-1">
                                    <p class="text-[13px] text-gray-600">Total: <span class="font-semibold text-gray-800" x-text="formatCurrency(booking.total)"></span></p>
                                    <p class="text-[13px] text-gray-500">Dibayar: <span x-text="formatCurrency(booking.paid_amount)"></span></p>
                                    <p x-show="booking.remaining > 0" class="text-[13px] font-semibold text-red-500">Sisa: <span x-text="formatCurrency(booking.remaining)"></span></p>
                                </div>
                            </td>
                            <td class="px-8 py-6 align-top text-center">
                                
                                {{-- JIKA STATUS TUNGGU KONFIRMASI --}}
                                <template x-if="booking.payment_status === 'Tunggu Konfirmasi'">
                                    <div class="flex items-center justify-center gap-3">
                                        <button type="button" @click="openConfirmModal(booking)" class="flex items-center gap-1.5 text-emerald-500 hover:text-emerald-600 font-semibold text-[13px] transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            Konfirmasi
                                        </button>
                                        <button type="button" @click="openRejectModal(booking)" class="flex items-center gap-1.5 text-rose-400 hover:text-rose-500 font-semibold text-[13px] transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            Tolak
                                        </button>
                                    </div>
                                </template>

                                {{-- JIKA BUKAN TUNGGU KONFIRMASI --}}
                                <template x-if="booking.payment_status !== 'Tunggu Konfirmasi'">
                                    <div class="flex items-center justify-center gap-2">
                                        <button type="button" @click="openInvoice(booking)" class="w-9 h-9 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-600 flex items-center justify-center transition" title="Generate Invoice">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                        </button>
                                        <a :href="`/bookings/${booking.id}/edit`" class="w-9 h-9 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-600 flex items-center justify-center transition" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                        </a>
                                        <button type="button" @click="deleteBooking(booking)" class="w-9 h-9 rounded-xl bg-red-50 hover:bg-red-100 text-red-500 flex items-center justify-center transition" title="Hapus">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </div>
                                </template>
                                
                            </td>
                        </tr>
                    </template>
                </template>

                {{-- State Kosong --}}
                <template x-if="!loading && filteredBookings.length === 0">
                    <tr>
                        <td colspan="6">
                            <div class="h-[320px] flex flex-col items-center justify-center px-4 text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-14 h-14 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <h1 class="text-[20px] sm:text-[24px] font-bold text-gray-400">Belum ada data</h1>
                            </div>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    {{-- ========================================================= --}}
    {{-- MODAL 1: KONFIRMASI PEMBAYARAN (DARK MODE) --}}
    {{-- ========================================================= --}}
    <div x-show="confirmModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center px-4" x-cloak>
        <div x-show="confirmModalOpen" x-transition.opacity class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="confirmModalOpen = false"></div>
        
        <div x-show="confirmModalOpen" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             class="relative bg-[#212121] border border-gray-700 rounded-lg shadow-2xl w-full max-w-3xl overflow-hidden z-10 flex flex-col max-h-[90vh]">
            
            {{-- Header --}}
            <div class="flex justify-between items-center px-6 py-4 border-b border-gray-700 shrink-0">
                <h3 class="text-white font-semibold text-[15px]">Konfirmasi Bukti Transfer</h3>
                <button type="button" @click="confirmModalOpen = false" class="text-gray-400 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            {{-- Body --}}
            <div class="p-6 overflow-y-auto">
                <div class="bg-[#dcfce7] rounded py-3 px-4 mb-6 shadow-sm border border-[#bbf7d0]">
                    <p class="text-[#166534] font-semibold text-[13px] mb-1">
                        Booking: INV-<span x-text="selectedBooking?.id"></span> &middot; <span x-text="selectedBooking?.client_name"></span>
                    </p>
                    <p class="text-[#166534] text-[12px]">
                        Jenis: <span class="font-bold uppercase" x-text="(selectedBooking?.payment_type || '').toUpperCase() === 'LUNAS' ? 'Lunas Penuh' : 'Down Payment (30%)'"></span> &middot; 
                        Nominal: <span class="font-bold" x-text="formatCurrency((selectedBooking?.payment_type || '').toUpperCase() === 'LUNAS' ? selectedBooking?.total : (selectedBooking?.total * 0.3))"></span>
                    </p>
                    <p class="text-[#15803d] text-[12px] mt-1">
                        Dikirim: <span x-text="formatDateTime(selectedBooking?.updated_at)"></span>
                    </p>
                </div>

                <p class="text-gray-500 text-center text-xs mb-3 font-medium">Foto Bukti Transfer</p>
                <div class="flex justify-center mb-4">
                    {{-- [PERBAIKAN] Path gambar dipastikan terbaca benar dari url public --}}
                    <template x-if="selectedBooking?.payment_proof">
                        <img :src="getImageUrl(selectedBooking?.payment_proof)" alt="Bukti Transfer" class="max-h-[350px] object-contain rounded border border-gray-700 bg-black">
                    </template>
                    <template x-if="!selectedBooking?.payment_proof">
                        <div class="h-32 w-48 bg-gray-800 border border-gray-700 rounded flex items-center justify-center text-gray-500 text-xs">
                            Gambar Tidak Tersedia
                        </div>
                    </template>
                </div>
            </div>

            {{-- Footer / Actions --}}
            <div class="px-6 py-4 border-t border-gray-700 bg-[#212121] flex gap-3 shrink-0">
                <button type="button" @click="submitConfirm()" :disabled="isProcessing" class="bg-emerald-600 hover:bg-emerald-500 text-white px-5 py-2 rounded font-semibold transition text-sm">
                    Submit
                </button>
                <button type="button" @click="confirmModalOpen = false" class="bg-[#333] hover:bg-[#444] border border-gray-600 text-white px-5 py-2 rounded font-semibold transition text-sm">
                    Batal
                </button>
            </div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- MODAL 2: TOLAK BUKTI PEMBAYARAN (DARK MODE) --}}
    {{-- ========================================================= --}}
    <div x-show="rejectModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center px-4" x-cloak>
        <div x-show="rejectModalOpen" x-transition.opacity class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="rejectModalOpen = false"></div>
        
        <div x-show="rejectModalOpen" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             class="relative bg-[#18181b] border border-gray-800 rounded-2xl shadow-2xl w-full max-w-md p-6 text-center z-10">
            
            <button type="button" @click="rejectModalOpen = false" class="absolute top-4 right-4 text-gray-500 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            <div class="w-16 h-16 rounded-full bg-red-500/10 text-red-500 flex items-center justify-center mx-auto mb-4 border border-red-500/20">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            </div>
            
            <h3 class="text-white font-bold text-lg mb-2">Tolak Bukti Transfer?</h3>
            <p class="text-gray-400 text-sm mb-6 px-2">Transaksi ini akan ditolak dan status jadwal akan dibatalkan.</p>
            
            <div class="text-left mb-6">
                <label class="text-gray-300 text-xs font-semibold mb-2 block">Alasan Penolakan (opsional)</label>
                <textarea x-model="rejectReason" class="w-full bg-[#27272a] border border-orange-500/40 rounded-xl p-3 text-white text-sm focus:outline-none focus:border-orange-500 transition placeholder-gray-500" rows="3" placeholder="Misal: Foto buram, nominal transfer kurang..."></textarea>
            </div>
            
            <div class="flex gap-3 justify-center">
                <button type="button" @click="rejectModalOpen = false" class="bg-[#27272a] hover:bg-[#3f3f46] border border-gray-700 text-white px-6 py-2.5 rounded-xl font-medium transition text-sm flex-1">
                    Cancel
                </button>
                <button type="button" @click="submitReject()" :disabled="isProcessing" class="bg-red-600 hover:bg-red-500 text-white px-6 py-2.5 rounded-xl font-medium transition text-sm flex-1">
                    Confirm Reject
                </button>
            </div>
        </div>
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

            // Modal States
            confirmModalOpen: false,
            rejectModalOpen: false,
            selectedBooking: null,
            rejectReason: '',
            isProcessing: false,

            init() {
                this.loadBookings()
            },

            async loadBookings(silent = false) {
                if (!silent) this.loading = true
                try {
                    const res = await fetch('/bookings', {
                        headers: { 'Accept': 'application/json' }
                    })
                    const result = await res.json()
                    
                    // Format response to ensure proper rendering
                    this.bookings = result.data.map(b => {
                        // Include full path for payment_proof directly from backend data mapping if needed
                        // Or just pass the raw path, we handle it in getImageUrl()
                        return b;
                    }) ?? [];
                    
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
                    const map = { dijadwalkan: 'Dijadwalkan', pembayaran_tertunda: 'Pembayaran Tertunda', proses_edit: 'Proses Edit', selesai: 'Selesai', dibatalkan: 'Dibatalkan' }
                    result = result.filter(b => b.status === (map[this.activeStatus] ?? this.activeStatus))
                }

                if (this.activePayment && this.activePayment !== 'semua') {
                    const map = { pending: 'Pending', tunggu_konfirmasi: 'Tunggu Konfirmasi', dp: 'Down Payment', lunas: 'Lunas', ditolak: 'Ditolak', refund: 'Refund', expired: 'Expired', cancelled: 'Cancelled' }
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
                        (b.service_type?.name ?? '').toLowerCase().includes(q)
                    )
                }

                switch (this.activeSortBy) {
                    case 'newest': result.sort((a, b) => new Date(b.created_at) - new Date(a.created_at)); break
                    case 'updated': result.sort((a, b) => new Date(b.updated_at) - new Date(a.updated_at)); break
                    case 'oldest': result.sort((a, b) => new Date(a.created_at) - new Date(b.created_at)); break
                }
                return result
            },

            // =====================================
            // LOGIKA MODAL KONFIRMASI / TOLAK
            // =====================================
            openConfirmModal(booking) {
                this.selectedBooking = booking;
                this.confirmModalOpen = true;
            },

            openRejectModal(booking) {
                this.selectedBooking = booking;
                this.rejectReason = '';
                this.rejectModalOpen = true;
            },

            async submitConfirm() {
                if (!this.selectedBooking) return;
                this.isProcessing = true;
                try {
                    const res = await fetch(`/bookings/${this.selectedBooking.id}/approve-payment`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    const data = await res.json();
                    if (!res.ok) throw new Error(data.message);
                    
                    this.confirmModalOpen = false;
                    Swal.fire({ icon: 'success', title: 'Dikonfirmasi!', text: data.message, timer: 2000, showConfirmButton: false, customClass: { popup: 'rounded-[28px]' } });
                    
                    this.loadBookings(true);
                    window.dispatchEvent(new CustomEvent('reload-bookings'));
                } catch (err) {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: err.message, customClass: { popup: 'rounded-[28px]' } });
                } finally {
                    this.isProcessing = false;
                }
            },

            async submitReject() {
                if (!this.selectedBooking) return;
                this.isProcessing = true;
                try {
                    const res = await fetch(`/bookings/${this.selectedBooking.id}/reject-payment`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ reason: this.rejectReason })
                    });
                    const data = await res.json();
                    if (!res.ok) throw new Error(data.message);
                    
                    this.rejectModalOpen = false;
                    Swal.fire({ icon: 'success', title: 'Ditolak!', text: data.message, timer: 2000, showConfirmButton: false, customClass: { popup: 'rounded-[28px]' } });
                    
                    this.loadBookings(true);
                    window.dispatchEvent(new CustomEvent('reload-bookings'));
                } catch (err) {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: err.message, customClass: { popup: 'rounded-[28px]' } });
                } finally {
                    this.isProcessing = false;
                }
            },

            openInvoice(booking) { window.dispatchEvent(new CustomEvent('open-invoice', { detail: booking })) },
            
            // Format URL Gambar Bukti Transfer yang Aman
            getImageUrl(path) {
                if (!path) return '';
                // Menghindari double slash jika path dari database sudah mengandung 'storage/'
                if (path.startsWith('storage/')) return '/' + path;
                if (path.startsWith('/storage/')) return path;
                return '/storage/' + path;
            },

            formatDate(dateStr) {
                if (!dateStr) return '-'
                return new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }).format(new Date(dateStr))
            },

            formatDateTime(dateStr) {
                if (!dateStr) return '-'
                return new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }).format(new Date(dateStr))
            },
            
            formatCurrency(value) { 
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value || 0) 
            },

            statusClass(status) {
                switch (status) {
                    case 'Selesai': return 'bg-emerald-50 text-emerald-700 border-emerald-200'
                    case 'Dibatalkan': return 'bg-rose-50 text-rose-700 border-rose-200'
                    case 'Pembayaran Tertunda': return 'bg-orange-50 text-orange-700 border-orange-200'
                    case 'Proses Edit': return 'bg-purple-50 text-purple-700 border-purple-200'
                    default: return 'bg-blue-50 text-blue-700 border-blue-200'
                }
            },
            
            paymentClass(status) {
                switch (status) {
                    case 'Lunas': 
                    case 'Down Payment': return 'bg-emerald-50 text-emerald-700 border-emerald-200'
                    case 'Tunggu Konfirmasi': return 'bg-purple-50 text-purple-700 border-purple-200 animate-pulse'
                    case 'Ditolak':
                    case 'Expired':
                    case 'Cancelled': return 'bg-rose-50 text-rose-700 border-rose-200'
                    case 'Refund': return 'bg-gray-100 text-gray-700 border-gray-300'
                    default: return 'bg-yellow-50 text-yellow-700 border-yellow-200'
                }
            },
            
            paymentLabel(status) {
                switch (status) {
                    case 'Lunas': return 'Lunas'
                    case 'Down Payment': return 'Down Payment (DP)'
                    case 'Tunggu Konfirmasi': return 'Tunggu Konfirmasi'
                    case 'Ditolak': return 'Ditolak'
                    case 'Refund': return 'Refund'
                    case 'Expired': return 'Expired'
                    case 'Cancelled': return 'Cancelled'
                    default: return 'Pending'
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
                    if (!res.ok) throw new Error(data.message)
                    
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
                        icon: 'error', title: 'Gagal!', text: err.message || 'Gagal terhubung ke server.',
                        confirmButtonColor: '#2563eb', customClass: { popup: 'rounded-[28px]' }
                    })
                }
            }
        }
    }
</script>