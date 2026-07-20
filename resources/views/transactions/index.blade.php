{{-- resources/views/transactions/index.blade.php --}}
<x-app-layout>
    <div x-data="transactionPage()" x-init="init()" class="px-4 sm:px-6 lg:px-8 py-8 bg-[#f5f7fb] min-h-screen overflow-x-hidden">
        
        <div id="data-area" class="w-full">
            <div class="bg-white p-4 rounded-t-[24px] border border-gray-100 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2 px-2">
                    <i data-lucide="wallet" class="w-5 h-5 text-gray-400"></i>
                    <h2 class="text-[16px] font-bold text-gray-800">Daftar Transaksi Klien</h2>
                </div>
                <div class="relative w-full md:w-[260px]">
                    <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                    <input type="text" x-model="activeSearch"
                        placeholder="Cari transaksi..."
                        class="w-full h-[42px] pl-10 pr-4 rounded-xl border border-gray-200 bg-white text-[13px] focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                </div>
            </div>

            <div class="bg-white border-x border-b border-gray-100 rounded-b-[24px] shadow-sm overflow-hidden">
                
                {{-- State Loading --}}
                <div x-show="loading" class="h-[320px] flex flex-col items-center justify-center gap-3">
                    <svg class="animate-spin w-8 h-8 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
                    </svg>
                    <p class="text-[14px] text-gray-400">Memuat data transaksi...</p>
                </div>

                <div x-show="!loading" x-cloak>
                    
                    {{-- FILTER TAB STATUS PEMBAYARAN --}}
                    <div class="flex justify-center py-4 border-b border-gray-100">
                        <div class="inline-flex items-center gap-1 bg-gray-50 rounded-xl p-1 border border-gray-100 flex-wrap">
                            <template x-for="tab in statusTabs" :key="tab.key">
                                <button type="button"
                                    @click="activePayment = tab.key"
                                    :class="activePayment === tab.key
                                        ? 'bg-orange-50 text-orange-600 border border-orange-200'
                                        : 'text-gray-500 hover:bg-gray-100 border border-transparent'"
                                    class="flex items-center gap-2 px-4 py-2 rounded-lg text-[13px] font-semibold transition">
                                    <span x-text="tab.label"></span>
                                    <span :class="activePayment === tab.key ? 'bg-orange-100 text-orange-600' : 'bg-gray-200 text-gray-600'"
                                        class="text-[11px] font-bold px-2 py-0.5 rounded-full min-w-[20px] text-center"
                                        x-text="statusCounts[tab.key] ?? 0"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    {{-- BULK ACTIONS TOOLBAR --}}
                    <div x-show="selectedIds.length > 0" x-cloak x-transition
                        class="flex items-center justify-between px-8 py-3 bg-orange-50/60 border-b border-orange-100">
                        <div class="relative" x-data="{ bulkMenuOpen: false }">
                            <button type="button" @click="bulkMenuOpen = !bulkMenuOpen"
                                class="flex items-center gap-2 px-3 py-2 bg-white border border-gray-200 rounded-lg text-[13px] font-semibold text-gray-700 hover:bg-gray-50 transition">
                                <i data-lucide="more-vertical" class="w-4 h-4"></i>
                                Bulk actions <span class="text-orange-600" x-text="`(${selectedIds.length})`"></span>
                            </button>
                            <div x-show="bulkMenuOpen" @click.outside="bulkMenuOpen = false" x-transition
                                class="absolute left-0 mt-2 w-52 bg-white border border-gray-100 rounded-lg shadow-lg z-20 py-1">
                                <button type="button" @click="bulkMenuOpen = false; bulkDeleteModalOpen = true"
                                    class="w-full flex items-center gap-2 px-4 py-2 text-[13px] text-red-500 hover:bg-red-50 transition font-medium">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i> Delete selected
                                </button>
                            </div>
                        </div>
                        <button type="button" @click="selectedIds = []" class="text-[13px] font-semibold text-orange-600 hover:text-orange-700 transition">
                            Deselect all
                        </button>
                    </div>

                    {{-- Tabel Data --}}
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[1150px]">
                            <thead>
                                <tr class="text-left text-gray-500 text-sm border-b border-gray-100 bg-gray-50/50">
                                    <th class="px-6 py-6 w-10">
                                        <input type="checkbox" :checked="isAllSelected" @change="toggleSelectAll()"
                                            class="w-4 h-4 rounded border-gray-300 text-orange-500 focus:ring-orange-400 cursor-pointer">
                                    </th>
                                    <th class="px-4 py-6 font-semibold tracking-wide">INFO TRANSAKSI</th>
                                    <th class="px-4 py-6 font-semibold tracking-wide">TYPE</th>
                                    <th class="px-4 py-6 font-semibold tracking-wide">AMOUNT</th>
                                    <th class="px-4 py-6 font-semibold tracking-wide">STATUS PEMBAYARAN</th>
                                    <th class="px-4 py-6 font-semibold tracking-wide">PAID AT</th>
                                    <th class="px-4 py-6 font-semibold tracking-wide text-center">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-if="filteredBookings.length > 0">
                                    <template x-for="booking in filteredBookings" :key="booking.id">
                                        <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition"
                                            :class="selectedIds.includes(booking.id) ? 'bg-orange-50/40' : ''">
                                            
                                            {{-- Checkbox --}}
                                            <td class="px-6 py-6 align-top">
                                                <input type="checkbox" :checked="selectedIds.includes(booking.id)"
                                                    @change="toggleSelect(booking.id)"
                                                    class="w-4 h-4 rounded border-gray-300 text-orange-500 focus:ring-orange-400 cursor-pointer">
                                            </td>

                                            {{-- Info Transaksi (Menggunakan booking_code) --}}
                                            <td class="px-4 py-6 align-top">
                                                <p class="text-[11px] font-bold text-gray-400 mb-1 tracking-wider uppercase" x-text="booking.booking_code"></p>
                                                <p class="font-bold text-[15px] text-gray-900" x-text="booking.client_name"></p>
                                                <p class="text-[13px] text-gray-500 mt-0.5"><span x-text="booking.service_type?.name || '-'"></span></p>
                                            </td>

                                            {{-- Type --}}
                                            <td class="px-4 py-6 align-top">
                                                <span :class="paymentTypeClass(booking.payment_type)" class="inline-flex items-center px-3 py-1 rounded-full text-[12px] font-semibold border">
                                                    <span x-text="paymentTypeLabel(booking.payment_type)"></span>
                                                </span>
                                            </td>

                                            {{-- Amount --}}
                                            <td class="px-4 py-6 align-top">
                                                <div class="space-y-1">
                                                    <p class="text-[13px] text-gray-600">Total: <span class="font-semibold text-gray-800" x-text="formatCurrency(booking.total)"></span></p>
                                                    <p class="text-[13px] text-emerald-600 font-medium">Dibayar: <span x-text="formatCurrency(booking.paid_amount)"></span></p>
                                                    <p x-show="booking.remaining > 0" class="text-[13px] font-semibold text-red-500">Sisa: <span x-text="formatCurrency(booking.remaining)"></span></p>
                                                </div>
                                            </td>

                                            {{-- Status Pembayaran --}}
                                            <td class="px-4 py-6 align-top">
                                                <span :class="paymentClass(booking.payment_status, booking)" class="inline-flex items-center px-3 py-1 rounded-full text-[12px] font-semibold border">
                                                    <span x-text="paymentLabel(booking.payment_status, booking)"></span>
                                                </span>
                                            </td>

                                            {{-- Paid At --}}
                                            <td class="px-4 py-6 align-top">
                                                <p class="text-[13px] text-gray-600" x-text="formatDateTime(getPaidAt(booking))"></p>
                                            </td>

                                            {{-- Aksi (Dilengkapi Tombol Mata & Kebab Notes) --}}
                                            <td class="px-4 py-6 align-top text-center">
                                                <div class="flex items-center justify-center gap-2">
                                                    
                                                    {{-- Tombol Konfirmasi jika Tunggu Konfirmasi --}}
                                                    <template x-if="booking.payment_status === 'Tunggu Konfirmasi'">
                                                        <div class="flex items-center gap-2">
                                                            <button type="button" @click="openConfirmModal(booking)" class="px-3 py-1.5 bg-emerald-50 text-emerald-600 hover:bg-emerald-100 rounded-lg font-semibold text-[12px] transition border border-emerald-200">
                                                                Cek Bukti
                                                            </button>
                                                            <button type="button" @click="openRejectModal(booking)" class="px-3 py-1.5 bg-red-50 text-red-500 hover:bg-red-100 rounded-lg font-semibold text-[12px] transition border border-red-200">
                                                                Tolak
                                                            </button>
                                                        </div>
                                                    </template>

                                                    {{-- Tombol Cetak Invoice jika Lunas --}}
                                                    <template x-if="booking.payment_status === 'Lunas'">
                                                        <button type="button" @click="window.dispatchEvent(new CustomEvent('open-invoice', { detail: booking }))" class="flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-lg font-semibold text-[12px] transition border border-gray-200">
                                                            <i data-lucide="printer" class="w-3.5 h-3.5"></i> Invoice
                                                        </button>
                                                    </template>

                                                    {{-- Tombol Lihat (Mata) --}}
                                                    <button type="button" @click="openDetailModal(booking)" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 flex items-center justify-center transition" title="Lihat Data">
                                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                                    </button>

                                                    {{-- Titik Tiga (Dropdown Aksi Tambahan: Add Notes) --}}
                                                    <div class="relative" x-data="{ openKebab: false }">
                                                        <button @click="openKebab = !openKebab" @click.outside="openKebab = false" class="w-8 h-8 rounded-lg bg-gray-50 text-gray-600 hover:bg-gray-100 flex items-center justify-center transition border border-gray-200">
                                                            <i data-lucide="more-vertical" class="w-4 h-4"></i>
                                                        </button>
                                                        <div x-show="openKebab" x-transition class="absolute right-0 mt-2 w-40 bg-white border border-gray-100 rounded-xl shadow-lg z-30 py-1">
                                                            <button @click="openNotesModal(booking); openKebab = false" class="w-full text-left px-4 py-2 text-[13px] font-medium text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                                                                <i data-lucide="file-text" class="w-4 h-4 text-gray-400"></i> Add Notes
                                                            </button>
                                                        </div>
                                                    </div>

                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                </template>
                                <template x-if="!loading && filteredBookings.length === 0">
                                    <tr><td colspan="7"><div class="h-[300px] flex items-center justify-center text-gray-400 font-bold text-xl">Tidak ada transaksi</div></td></tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- MODAL 1: KONFIRMASI PEMBAYARAN --}}
        {{-- ========================================================= --}}
        <div x-show="confirmModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center px-4" x-cloak>
            <div x-show="confirmModalOpen" x-transition.opacity class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="confirmModalOpen = false"></div>
            <div x-show="confirmModalOpen" class="relative bg-[#212121] border border-gray-700 rounded-lg shadow-2xl w-full max-w-3xl overflow-hidden z-10 flex flex-col max-h-[90vh]">
                <div class="flex justify-between items-center px-6 py-4 border-b border-gray-700 shrink-0">
                    <h3 class="text-white font-semibold text-[15px]">Konfirmasi Bukti Transfer</h3>
                    <button type="button" @click="confirmModalOpen = false" class="text-gray-400 hover:text-white transition"><i data-lucide="x" class="w-5 h-5"></i></button>
                </div>
                <div class="p-6 overflow-y-auto">
                    <div class="bg-[#dcfce7] rounded py-3 px-4 mb-6 shadow-sm border border-[#bbf7d0]">
                        <p class="text-[#166534] font-semibold text-[13px] mb-1">
                            Booking: <span x-text="selectedBooking?.booking_code"></span> &middot; <span x-text="selectedBooking?.client_name"></span>
                        </p>
                        <p class="text-[#166534] text-[12px]">
                            Nominal: <span class="font-bold" x-text="formatCurrency(selectedBooking?.remaining > 0 ? selectedBooking?.remaining : selectedBooking?.total)"></span>
                        </p>
                    </div>
                    <p class="text-gray-500 text-center text-xs mb-3 font-medium">Foto Bukti Transfer</p>
                    <div class="flex justify-center mb-4">
                        <template x-if="selectedBooking?.payment_proof">
                            <img :src="getImageUrl(selectedBooking?.payment_proof)" alt="Bukti Transfer" class="max-h-[350px] object-contain rounded border border-gray-700 bg-black">
                        </template>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-700 bg-[#212121] flex gap-3 shrink-0">
                    <button type="button" @click="submitConfirm()" :disabled="isProcessing" class="bg-emerald-600 hover:bg-emerald-500 text-white px-5 py-2 rounded font-semibold transition text-sm">Konfirmasi Pembayaran</button>
                    <button type="button" @click="confirmModalOpen = false" class="bg-[#333] hover:bg-[#444] text-white px-5 py-2 rounded font-semibold transition text-sm">Batal</button>
                </div>
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- MODAL 2: TOLAK BUKTI --}}
        {{-- ========================================================= --}}
        <div x-show="rejectModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center px-4" x-cloak>
            <div x-show="rejectModalOpen" x-transition.opacity class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="rejectModalOpen = false"></div>
            <div x-show="rejectModalOpen" class="relative bg-[#18181b] border border-gray-800 rounded-2xl shadow-2xl w-full max-w-md p-6 text-center z-10">
                <h3 class="text-white font-bold text-lg mb-2">Tolak Bukti Transfer?</h3>
                <textarea x-model="rejectReason" class="w-full bg-[#27272a] border border-orange-500/40 rounded-xl p-3 text-white text-sm mb-4" rows="3" placeholder="Alasan penolakan..."></textarea>
                <div class="flex gap-3 justify-center">
                    <button type="button" @click="rejectModalOpen = false" class="bg-[#27272a] text-white px-6 py-2.5 rounded-xl text-sm flex-1">Batal</button>
                    <button type="button" @click="submitReject()" class="bg-red-600 text-white px-6 py-2.5 rounded-xl text-sm flex-1">Tolak</button>
                </div>
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- MODAL 3: LIHAT DATA (IKON MATA) --}}
        {{-- ========================================================= --}}
        <div x-show="detailModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center px-4" x-cloak>
            <div x-show="detailModalOpen" x-transition.opacity class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="detailModalOpen = false"></div>
            <div x-show="detailModalOpen" class="relative bg-white border border-gray-100 rounded-2xl shadow-2xl w-full max-w-lg p-6 z-10">
                <div class="flex justify-between items-center mb-4 border-b pb-3">
                    <h3 class="font-bold text-gray-900 text-lg">Detail Transaksi</h3>
                    <button @click="detailModalOpen = false" class="text-gray-400 hover:text-gray-600"><i data-lucide="x" class="w-5 h-5"></i></button>
                </div>
                <div class="space-y-3 text-sm text-gray-600">
                    <p><b>Kode Booking:</b> <span class="font-mono text-gray-900" x-text="selectedBooking?.booking_code"></span></p>
                    <p><b>Nama Klien:</b> <span class="text-gray-900 font-semibold" x-text="selectedBooking?.client_name"></span></p>
                    <p><b>Kontak:</b> <span class="text-gray-900" x-text="selectedBooking?.client_contact || '-'"></span></p>
                    <p><b>Alamat:</b> <span class="text-gray-900" x-text="selectedBooking?.client_address || '-'"></span></p>
                    <p><b>Layanan:</b> <span class="text-gray-900" x-text="selectedBooking?.service_type?.name"></span></p>
                    <p><b>Total Biaya:</b> <span class="text-gray-900 font-bold" x-text="formatCurrency(selectedBooking?.total)"></span></p>
                    <p><b>Catatan:</b> <span class="text-gray-900 italic" x-text="selectedBooking?.notes || 'Tidak ada catatan.'"></span></p>
                </div>
                <div class="mt-6 text-right">
                    <button @click="detailModalOpen = false" class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-semibold text-sm">Tutup</button>
                </div>
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- MODAL 4: ADD NOTES (TITIK TIGA) --}}
        {{-- ========================================================= --}}
        <div x-show="notesModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center px-4" x-cloak>
            <div x-show="notesModalOpen" x-transition.opacity class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="notesModalOpen = false"></div>
            <div x-show="notesModalOpen" class="relative bg-white border border-gray-100 rounded-2xl shadow-2xl w-full max-w-md p-6 z-10">
                <div class="flex justify-between items-center mb-4 border-b pb-3">
                    <h3 class="font-bold text-gray-900 text-lg">Catatan Transaksi</h3>
                    <button @click="notesModalOpen = false" class="text-gray-400 hover:text-gray-600"><i data-lucide="x" class="w-5 h-5"></i></button>
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Catatan / Notes</label>
                    <textarea x-model="currentNotes" rows="4" class="w-full rounded-xl border-gray-200 text-sm focus:ring-blue-500" placeholder="Tulis catatan di sini..."></textarea>
                </div>
                <div class="flex gap-3 justify-end">
                    <button @click="notesModalOpen = false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl text-sm font-semibold">Batal</button>
                    <button @click="saveNotes()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-semibold">Simpan Catatan</button>
                </div>
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- MODAL 5: HAPUS MASSAL (BULK DELETE) --}}
        {{-- ========================================================= --}}
        <div x-show="bulkDeleteModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center px-4" x-cloak>
            <div x-show="bulkDeleteModalOpen" x-transition.opacity class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="bulkDeleteModalOpen = false"></div>
            <div x-show="bulkDeleteModalOpen" class="relative bg-[#18181b] border border-gray-800 rounded-2xl shadow-2xl w-full max-w-md p-6 text-center z-10">
                <h3 class="text-white font-bold text-lg mb-2">Hapus Data Terpilih?</h3>
                <p class="text-gray-400 text-sm mb-6"><span x-text="selectedIds.length"></span> transaksi akan dihapus permanen.</p>
                <div class="flex gap-3 justify-center">
                    <button type="button" @click="bulkDeleteModalOpen = false" class="bg-[#27272a] text-white px-6 py-2.5 rounded-xl text-sm flex-1">Batal</button>
                    <button type="button" @click="submitBulkDelete()" class="bg-red-600 text-white px-6 py-2.5 rounded-xl text-sm flex-1">Hapus</button>
                </div>
            </div>
        </div>

        <x-dashboard.invoice-modal />
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        function transactionPage() {
            return {
                bookings: [],
                loading: true,
                activeSearch: '',
                activePayment: 'semua',
                statusTabs: [
                    { key: 'semua', label: 'All' },
                    { key: 'tunggu_konfirmasi', label: 'Tunggu Konfirmasi' },
                    { key: 'berhasil', label: 'Berhasil' },
                    { key: 'pending', label: 'Pending' },
                    { key: 'expired', label: 'Expired' },
                ],
                selectedIds: [],
                bulkDeleteModalOpen: false,
                confirmModalOpen: false,
                rejectModalOpen: false,
                detailModalOpen: false,
                notesModalOpen: false,
                selectedBooking: null,
                rejectReason: '',
                currentNotes: '',
                isProcessing: false,

                init() {
                    this.$nextTick(() => { if (window.lucide) lucide.createIcons() })
                    this.loadBookings();
                    setInterval(() => { if (document.visibilityState === 'visible') this.loadBookings(true); }, 5000);
                },

                async loadBookings(silent = false) {
                    if (!silent) this.loading = true
                    try {
                        const res = await fetch('/bookings', { headers: { 'Accept': 'application/json' } })
                        const result = await res.json()
                        this.bookings = result.data || [];
                    } catch (e) {
                        this.bookings = []
                    } finally {
                        if (!silent) this.loading = false
                        this.$nextTick(() => { if (window.lucide) lucide.createIcons() })
                    }
                },

                mapStatusToTabKey(status, booking) {
                    if (status === 'Tunggu Konfirmasi') return 'tunggu_konfirmasi';
                    if (status === 'Lunas' || status === 'Down Payment') return 'berhasil';
                    if (status === 'Pending' || status === 'Belum Bayar') return 'pending';
                    if (['Expired', 'Ditolak', 'Cancelled', 'Dibatalkan'].includes(status)) return 'expired';
                    return null;
                },

                get statusCounts() {
                    const counts = { semua: this.bookings.length, tunggu_konfirmasi: 0, berhasil: 0, pending: 0, expired: 0 };
                    this.bookings.forEach(b => {
                        const key = this.mapStatusToTabKey(b.payment_status, b);
                        if (key && counts.hasOwnProperty(key)) counts[key]++;
                    });
                    return counts;
                },

                get filteredBookings() {
                    let result = [...this.bookings];
                    if (this.activePayment && this.activePayment !== 'semua') {
                        result = result.filter(b => this.mapStatusToTabKey(b.payment_status, b) === this.activePayment)
                    }
                    if (this.activeSearch.trim()) {
                        const q = this.activeSearch.toLowerCase().trim()
                        result = result.filter(b => (b.client_name ?? '').toLowerCase().includes(q) || (b.client_contact ?? '').toLowerCase().includes(q) || (b.booking_code ?? '').toLowerCase().includes(q))
                    }
                    return result.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
                },

                get isAllSelected() {
                    return this.filteredBookings.length > 0 && this.filteredBookings.every(b => this.selectedIds.includes(b.id));
                },

                toggleSelectAll() {
                    const idsInView = this.filteredBookings.map(b => b.id);
                    if (this.isAllSelected) {
                        this.selectedIds = this.selectedIds.filter(id => !idsInView.includes(id));
                    } else {
                        this.selectedIds = [...new Set([...this.selectedIds, ...idsInView])];
                    }
                },

                toggleSelect(id) {
                    if (this.selectedIds.includes(id)) {
                        this.selectedIds = this.selectedIds.filter(sid => sid !== id);
                    } else {
                        this.selectedIds.push(id);
                    }
                },

                async submitBulkDelete() {
                    if (this.selectedIds.length === 0) return;
                    try {
                        const res = await fetch('/bookings/bulk-delete', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                            body: JSON.stringify({ ids: this.selectedIds })
                        });
                        if (!res.ok) throw new Error('Gagal menghapus data');
                        this.bulkDeleteModalOpen = false;
                        this.selectedIds = [];
                        this.loadBookings(true);
                    } catch (err) { alert(err.message); }
                },

                openConfirmModal(booking) { this.selectedBooking = booking; this.confirmModalOpen = true; },
                openRejectModal(booking) { this.selectedBooking = booking; this.rejectReason = ''; this.rejectModalOpen = true; },
                
                openDetailModal(booking) { 
                    this.selectedBooking = booking; 
                    this.detailModalOpen = true; 
                },

                openNotesModal(booking) {
                    this.selectedBooking = booking;
                    this.currentNotes = booking.notes || '';
                    this.notesModalOpen = true;
                },

                async saveNotes() {
                    if (!this.selectedBooking) return;
                    try {
                        const res = await fetch(`/bookings/${this.selectedBooking.id}/update-notes`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                            body: JSON.stringify({ notes: this.currentNotes })
                        });
                        if (!res.ok) throw new Error('Gagal menyimpan catatan');
                        this.notesModalOpen = false;
                        this.loadBookings(true);
                    } catch (err) { alert(err.message); }
                },

                async submitConfirm() {
                    if (!this.selectedBooking) return;
                    this.isProcessing = true;
                    try {
                        const res = await fetch(`/bookings/${this.selectedBooking.id}/approve-payment`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                        });
                        if (!res.ok) throw new Error('Gagal konfirmasi');
                        this.confirmModalOpen = false;
                        this.loadBookings(true);
                    } catch (err) { alert(err.message); } finally { this.isProcessing = false; }
                },

                async submitReject() {
                    if (!this.selectedBooking) return;
                    this.isProcessing = true;
                    try {
                        const res = await fetch(`/bookings/${this.selectedBooking.id}/reject-payment`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                            body: JSON.stringify({ reason: this.rejectReason })
                        });
                        if (!res.ok) throw new Error('Gagal menolak');
                        this.rejectModalOpen = false;
                        this.loadBookings(true);
                    } catch (err) { alert(err.message); } finally { this.isProcessing = false; }
                },

                getImageUrl(path) {
                    if (!path) return '';
                    if (path.startsWith('storage/')) return '/' + path;
                    if (path.startsWith('/storage/')) return path;
                    return '/storage/' + path;
                },

                formatCurrency(val) { return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val || 0) },
                
                formatDateTime(dateStr) {
                    if (!dateStr) return '-'
                    return new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }).format(new Date(dateStr))
                },

                getPaidAt(booking) {
                    if (booking.paid_at) return booking.paid_at;
                    if (booking.payment_status === 'Lunas' || booking.payment_status === 'Down Payment') {
                        return booking.updated_at;
                    }
                    return null;
                },

                paymentClass(s, booking) {
                    const key = this.mapStatusToTabKey(s, booking);
                    if (key === 'berhasil') return 'bg-emerald-50 text-emerald-700 border-emerald-200';
                    if (key === 'tunggu_konfirmasi') return 'bg-yellow-50 text-yellow-700 border-yellow-200 animate-pulse';
                    if (key === 'pending') return 'bg-red-50 text-red-600 border-red-200';
                    if (key === 'expired') return 'bg-gray-100 text-gray-500 border-gray-300';
                    return 'bg-gray-100 text-gray-700 border-gray-300';
                },

                paymentLabel(s, booking) {
                    const key = this.mapStatusToTabKey(s, booking);
                    if (key === 'tunggu_konfirmasi') return 'Tunggu Konfirmasi';
                    if (key === 'berhasil') return 'Berhasil';
                    if (key === 'pending') return 'Pending';
                    if (key === 'expired') return 'Expired';
                    return s || '-';
                },

                paymentTypeLabel(t) {
                    const v = (t || '').toUpperCase();
                    if (v === 'LUNAS') return 'Lunas';
                    if (v === 'PELUNASAN') return 'Pelunasan';
                    if (v === 'DP') return 'DP';
                    return '-';
                },

                paymentTypeClass(t) {
                    const v = (t || '').toUpperCase();
                    if (v === 'LUNAS') return 'bg-emerald-50 text-emerald-700 border-emerald-200';
                    if (v === 'PELUNASAN') return 'bg-blue-50 text-blue-700 border-blue-200';
                    if (v === 'DP') return 'bg-amber-50 text-amber-700 border-amber-200';
                    return 'bg-gray-100 text-gray-500 border-gray-200';
                }
            }
        }
        document.addEventListener('DOMContentLoaded', () => { if (window.lucide) lucide.createIcons() })
    </script>
</x-app-layout>