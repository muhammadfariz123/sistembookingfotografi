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
                        placeholder="Cari transaksi atau kode..."
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
                        class="flex items-center justify-between px-8 py-3 bg-red-50 border-b border-red-100">
                        <div class="relative" x-data="{ bulkMenuOpen: false }">
                            <button type="button" @click="bulkMenuOpen = !bulkMenuOpen"
                                class="flex items-center gap-2 px-3 py-2 bg-white border border-gray-200 rounded-lg text-[13px] font-semibold text-gray-700 hover:bg-gray-50 transition">
                                <i data-lucide="more-vertical" class="w-4 h-4"></i>
                                Tindakan massal <span class="text-red-600" x-text="`(${selectedIds.length})`"></span>
                            </button>
                            <div x-show="bulkMenuOpen" @click.outside="bulkMenuOpen = false" x-transition
                                class="absolute left-0 mt-2 w-52 bg-white border border-gray-100 rounded-lg shadow-lg z-20 py-1">
                                <button type="button" @click="bulkMenuOpen = false; bulkDeleteModalOpen = true"
                                    class="w-full flex items-center gap-2 px-4 py-2 text-[13px] text-red-600 hover:bg-red-50 transition font-medium">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i> Hapus yang dipilih
                                </button>
                            </div>
                        </div>
                        <button type="button" @click="selectedIds = []" class="text-[13px] font-semibold text-red-600 hover:text-red-700 transition">
                            Batalkan Pilihan
                        </button>
                    </div>

                    {{-- Tabel Data Transaksi (Per Baris Riwayat) --}}
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[1150px]">
                            <thead>
                                <tr class="text-left text-gray-500 text-sm border-b border-gray-100 bg-gray-50/50">
                                    <th class="px-6 py-5 w-10">
                                        <input type="checkbox" :checked="isAllSelected" @change="toggleSelectAll()"
                                            class="w-4 h-4 rounded border-gray-300 text-orange-500 focus:ring-orange-400 cursor-pointer">
                                    </th>
                                    <th class="px-4 py-5 font-semibold tracking-wide">ID TRANSAKSI</th>
                                    <th class="px-4 py-5 font-semibold tracking-wide">BOOKING</th>
                                    <th class="px-4 py-5 font-semibold tracking-wide">TIPE BAYAR</th>
                                    <th class="px-4 py-5 font-semibold tracking-wide">NOMINAL</th>
                                    <th class="px-4 py-5 font-semibold tracking-wide">STATUS</th>
                                    <th class="px-4 py-5 font-semibold tracking-wide">DIBAYAR PADA</th>
                                    <th class="px-4 py-5 font-semibold tracking-wide text-center">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-if="filteredTransactions.length > 0">
                                    <template x-for="tx in filteredTransactions" :key="tx.id">
                                        <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition">
                                            
                                            {{-- Checkbox --}}
                                            <td class="px-6 py-5 align-top">
                                                <input type="checkbox" :checked="selectedIds.includes(tx.id)"
                                                    @change="toggleSelect(tx.id)"
                                                    class="w-4 h-4 rounded border-gray-300 text-orange-500 focus:ring-orange-400 cursor-pointer">
                                            </td>
                                            {{-- Transaction ID Unik --}}
                                            <td class="px-4 py-5 align-top">
                                                <p class="font-mono text-[12px] font-bold text-gray-800" x-text="tx.transaction_id"></p>
                                            </td>
                                            {{-- Booking & Nama Klien --}}
                                            <td class="px-4 py-5 align-top">
                                                <p class="text-[11px] font-bold text-blue-600 uppercase" x-text="tx.booking_code"></p>
                                                <p class="font-bold text-[14px] text-gray-900" x-text="tx.client_name"></p>
                                                <p class="text-[12px] text-gray-500" x-text="tx.service_type?.name || '-'"></p>
                                            </td>
                                            {{-- Type (DP / Pelunasan / Lunas) --}}
                                            <td class="px-4 py-5 align-top">
                                                <span :class="paymentTypeClass(tx.payment_type)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold border">
                                                    <span x-text="paymentTypeLabel(tx.payment_type)"></span>
                                                </span>
                                            </td>
                                            {{-- Amount --}}
                                            <td class="px-4 py-5 align-top">
                                                <p class="font-bold text-[13px] text-gray-900" x-text="formatCurrency(tx.amount)"></p>
                                            </td>
                                            {{-- Status --}}
                                            <td class="px-4 py-5 align-top">
                                                <span :class="paymentClass(tx.payment_status)" class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold border">
                                                    <span x-text="paymentLabel(tx.payment_status)"></span>
                                                </span>
                                            </td>
                                            {{-- Paid At --}}
                                            <td class="px-4 py-5 align-top">
                                                <p class="text-[12px] text-gray-600 font-medium" x-text="formatDateTime(tx.paid_at)"></p>
                                            </td>
                                            {{-- Aksi (Konfirmasi / Tolak / Mata) --}}
                                            <td class="px-4 py-5 align-top text-center">
                                                <div class="flex items-center justify-center gap-3">
                                                    <template x-if="tx.payment_status === 'Tunggu Konfirmasi'">
                                                        <div class="flex items-center gap-3">
                                                            <button type="button" @click="openConfirmModal(tx)" class="flex items-center gap-1 text-[#059669] hover:text-emerald-700 font-bold text-[13px] transition">
                                                                <i data-lucide="check-circle-2" class="w-4 h-4"></i> Konfirmasi
                                                            </button>
                                                            <button type="button" @click="openRejectModal(tx)" class="flex items-center gap-1 text-[#ef4444] hover:text-red-700 font-bold text-[13px] transition">
                                                                <i data-lucide="x-circle" class="w-4 h-4"></i> Tolak
                                                            </button>
                                                        </div>
                                                    </template>
                                                    <button type="button" @click="openDetailModal(tx)" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 flex items-center justify-center transition" title="Lihat Detail">
                                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                </template>
                                <template x-if="!loading && filteredTransactions.length === 0">
                                    <tr><td colspan="8"><div class="h-[300px] flex items-center justify-center text-gray-400 font-bold text-xl">Tidak ada transaksi</div></td></tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL KONFIRMASI PEMBAYARAN --}}
        <div x-show="confirmModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center px-4" x-cloak>
            <div x-show="confirmModalOpen" x-transition.opacity class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="confirmModalOpen = false"></div>
            <div x-show="confirmModalOpen" class="relative bg-[#212121] border border-gray-700 rounded-lg shadow-2xl w-full max-w-3xl overflow-hidden z-10 flex flex-col max-h-[90vh]">
                <div class="flex justify-between items-center px-6 py-4 border-b border-gray-700 shrink-0">
                    <h3 class="text-white font-semibold text-[15px]">Konfirmasi Bukti Transfer</h3>
                    <button type="button" @click="confirmModalOpen = false" class="text-gray-400 hover:text-white transition"><i data-lucide="x" class="w-5 h-5"></i></button>
                </div>
                <div class="p-6 overflow-y-auto">
                    <div class="bg-[#dcfce7] rounded-xl py-4 px-5 mb-6 shadow-sm border border-[#bbf7d0]">
                        <p class="text-[#166534] font-bold text-[14px] mb-2 flex items-center gap-2">
                            <i data-lucide="book-check" class="w-4 h-4"></i> 
                            Booking: <span x-text="selectedTx?.booking_code"></span> &middot; <span x-text="selectedTx?.client_name"></span>
                        </p>
                        <p class="text-[#15803d] text-[13px] mb-1">
                            Jenis: <span class="font-bold uppercase" x-text="paymentTypeLabel(selectedTx?.payment_type)"></span> &middot; 
                            Nominal: <span class="font-bold" x-text="formatCurrency(selectedTx?.amount)"></span>
                        </p>
                        <p class="text-[#166534] text-[12px] opacity-90 mt-2 flex items-center gap-1.5">
                            <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                            Dikirim: <span x-text="formatDateTime(selectedTx?.created_at)"></span>
                        </p>
                    </div>
                    <p class="text-gray-500 text-center text-sm mb-3 font-semibold">Foto Bukti Transfer</p>
                    <div class="flex justify-center mb-4">
                        <template x-if="selectedTx?.payment_proof">
                            <img :src="getImageUrl(selectedTx?.payment_proof)" alt="Bukti Transfer" class="max-h-[350px] object-contain rounded-xl border border-gray-700 bg-black shadow-lg">
                        </template>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-700 bg-[#212121] flex gap-3 shrink-0">
                    <button type="button" @click="submitConfirm()" :disabled="isProcessing" class="bg-emerald-600 hover:bg-emerald-500 text-white px-5 py-2.5 rounded-lg font-bold transition text-[13px] flex items-center gap-2 disabled:opacity-50">
                        <i data-lucide="check" class="w-4 h-4"></i> 
                        <span x-show="!isProcessing">Konfirmasi Pembayaran</span>
                        <span x-show="isProcessing">Memproses...</span>
                    </button>
                    <button type="button" @click="confirmModalOpen = false" class="bg-[#333] hover:bg-[#444] text-white px-5 py-2.5 rounded-lg font-bold transition text-[13px]">
                        Batal
                    </button>
                </div>
            </div>
        </div>

        {{-- MODAL TOLAK --}}
        <div x-show="rejectModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center px-4" x-cloak>
            <div x-show="rejectModalOpen" x-transition.opacity class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="rejectModalOpen = false"></div>
            <div x-show="rejectModalOpen" class="relative bg-[#18181b] border border-gray-800 rounded-2xl shadow-2xl w-full max-w-md p-6 text-center z-10">
                <div class="w-14 h-14 rounded-full bg-red-500/10 text-red-500 flex items-center justify-center mx-auto mb-4 border border-red-500/20">
                    <i data-lucide="x-circle" class="w-7 h-7"></i>
                </div>
                <h3 class="text-white font-bold text-lg mb-2">Tolak Bukti Transfer?</h3>
                <textarea x-model="rejectReason" class="w-full bg-[#27272a] border border-red-500/40 rounded-xl p-3 text-white text-sm mb-4 outline-none" rows="3" placeholder="Alasan penolakan..."></textarea>
                <div class="flex gap-3 justify-center">
                    <button type="button" @click="rejectModalOpen = false" class="bg-[#27272a] hover:bg-[#3f3f46] border border-gray-700 text-white px-6 py-2.5 rounded-xl font-medium transition text-sm flex-1">
                        Batal
                    </button>
                    <button type="button" @click="submitReject()" :disabled="isProcessing" class="bg-red-600 hover:bg-red-500 text-white px-6 py-2.5 rounded-xl font-medium transition text-sm flex-1 disabled:opacity-50">
                        <span x-show="!isProcessing">Tolak Pembayaran</span>
                        <span x-show="isProcessing">Memproses...</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- MODAL DETAIL --}}
        <div x-show="detailModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center px-4" x-cloak>
            <div x-show="detailModalOpen" x-transition.opacity class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="detailModalOpen = false"></div>
            <div x-show="detailModalOpen" class="relative bg-white border border-gray-100 rounded-2xl shadow-2xl w-full max-w-lg p-6 z-10">
                <div class="flex justify-between items-center mb-4 border-b pb-3">
                    <h3 class="font-bold text-gray-900 text-lg">Detail Transaksi</h3>
                    <button @click="detailModalOpen = false" class="text-gray-400 hover:text-gray-600"><i data-lucide="x" class="w-5 h-5"></i></button>
                </div>
                <div class="space-y-3 text-sm text-gray-600">
                    <p><b>ID Transaksi:</b> <span class="font-mono text-gray-900 font-bold" x-text="selectedTx?.transaction_id"></span></p>
                    <p><b>Kode Booking:</b> <span class="font-mono text-blue-600 font-bold" x-text="selectedTx?.booking_code"></span></p>
                    <p><b>Nama Klien:</b> <span class="text-gray-900 font-semibold" x-text="selectedTx?.client_name"></span></p>
                    <p><b>Kontak:</b> <span class="text-gray-900" x-text="selectedTx?.client_contact || '-'"></span></p>
                    <p><b>Layanan:</b> <span class="text-gray-900" x-text="selectedTx?.service_type?.name"></span></p>
                    <p><b>Nominal:</b> <span class="text-gray-900 font-bold" x-text="formatCurrency(selectedTx?.amount)"></span></p>
                    <p><b>Status:</b> <span class="text-gray-900 font-semibold" x-text="selectedTx?.payment_status"></span></p>
                </div>
                <div class="mt-6 text-right">
                    <button @click="detailModalOpen = false" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-bold text-[13px] transition">Tutup</button>
                </div>
            </div>
        </div>

        {{-- MODAL HAPUS MASSAL (BULK DELETE) --}}
        <div x-show="bulkDeleteModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center px-4" x-cloak>
            <div x-show="bulkDeleteModalOpen" x-transition.opacity class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="bulkDeleteModalOpen = false"></div>
            <div x-show="bulkDeleteModalOpen" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 class="relative bg-[#18181b] border border-gray-800 rounded-2xl shadow-2xl w-full max-w-md p-6 text-center z-10">
                
                <button type="button" @click="bulkDeleteModalOpen = false" class="absolute top-4 right-4 text-gray-500 hover:text-white transition">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
                
                <div class="w-16 h-16 rounded-full bg-red-500/10 text-red-500 flex items-center justify-center mx-auto mb-4 border border-red-500/20">
                    <i data-lucide="trash-2" class="w-8 h-8"></i>
                </div>
                <h3 class="text-white font-bold text-lg mb-2">Hapus Data Terpilih?</h3>
                <p class="text-gray-400 text-sm mb-6 px-2">
                    <span x-text="selectedIds.length"></span> transaksi akan dihapus permanen dan tidak bisa dikembalikan.
                </p>
                <div class="flex gap-3 justify-center">
                    <button type="button" @click="bulkDeleteModalOpen = false" class="bg-[#27272a] hover:bg-[#3f3f46] border border-gray-700 text-white px-6 py-2.5 rounded-xl font-medium transition text-sm flex-1">
                        Batal
                    </button>
                    <button type="button" @click="submitBulkDelete()" :disabled="isProcessing" class="bg-red-600 hover:bg-red-500 text-white px-6 py-2.5 rounded-xl font-medium transition text-sm flex-1 disabled:opacity-50">
                        <span x-show="!isProcessing">Hapus Sekarang</span>
                        <span x-show="isProcessing">Menghapus...</span>
                    </button>
                </div>
            </div>
        </div>

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
                    { key: 'ditolak', label: 'Ditolak' },
                ],
                selectedIds: [],
                confirmModalOpen: false,
                rejectModalOpen: false,
                detailModalOpen: false,
                bulkDeleteModalOpen: false,
                selectedTx: null,
                rejectReason: '',
                isProcessing: false, 

                init() {
                    this.$nextTick(() => { if (window.lucide) lucide.createIcons() })
                    this.loadTransactions();
                },
                
                async loadTransactions(silent = false) {
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
                
                get allTransactions() {
                    let txs = [];
                    this.bookings.forEach(b => {
                        if (b.transactions && b.transactions.length > 0) {
                            b.transactions.forEach(tx => {
                                txs.push(tx);
                            });
                        }
                    });
                    return txs.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
                },
                
                mapStatusToTabKey(status) {
                    if (status === 'Tunggu Konfirmasi') return 'tunggu_konfirmasi';
                    if (status === 'Berhasil' || status === 'Lunas') return 'berhasil';
                    if (status === 'Pending') return 'pending';
                    if (status === 'Ditolak') return 'ditolak';
                    return null;
                },
                
                get statusCounts() {
                    const counts = { semua: this.allTransactions.length, tunggu_konfirmasi: 0, berhasil: 0, pending: 0, ditolak: 0 };
                    this.allTransactions.forEach(tx => {
                        const key = this.mapStatusToTabKey(tx.payment_status);
                        if (key && counts.hasOwnProperty(key)) counts[key]++;
                    });
                    return counts;
                },
                
                get filteredTransactions() {
                    let result = [...this.allTransactions];
                    if (this.activePayment && this.activePayment !== 'semua') {
                        result = result.filter(tx => this.mapStatusToTabKey(tx.payment_status) === this.activePayment)
                    }
                    if (this.activeSearch.trim()) {
                        const q = this.activeSearch.toLowerCase().trim()
                        result = result.filter(tx => 
                            (tx.client_name ?? '').toLowerCase().includes(q) || 
                            (tx.transaction_id ?? '').toLowerCase().includes(q) ||
                            (tx.booking_code ?? '').toLowerCase().includes(q)
                        )
                    }
                    return result;
                },
                
                get isAllSelected() {
                    return this.filteredTransactions.length > 0 && this.filteredTransactions.every(tx => this.selectedIds.includes(tx.id));
                },
                
                toggleSelectAll() {
                    const idsInView = this.filteredTransactions.map(tx => tx.id);
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
                    this.isProcessing = true;
                    try {
                        // [FIX] Mengubah endpoint ke /transactions/bulk-delete
                        const res = await fetch('/transactions/bulk-delete', {
                            method: 'POST',
                            headers: { 
                                'Content-Type': 'application/json', 
                                'Accept': 'application/json', 
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content 
                            },
                            body: JSON.stringify({ ids: this.selectedIds })
                        });
                        
                        const data = await res.json();
                        if (!res.ok) throw new Error(data.message || 'Gagal menghapus data');
                        
                        this.selectedIds = [];
                        this.bulkDeleteModalOpen = false;
                        
                        if (window.Swal) Swal.fire({ icon: 'success', title: 'Terhapus!', text: data.message, timer: 2000, showConfirmButton: false, customClass: { popup: 'rounded-[28px]' } });
                        
                        this.loadTransactions(true);
                    } catch (err) { 
                        if (window.Swal) Swal.fire({ icon: 'error', title: 'Gagal', text: err.message, customClass: { popup: 'rounded-[28px]' } }); 
                        else alert(err.message); 
                    } finally { 
                        this.isProcessing = false; 
                    }
                },
                
                openConfirmModal(tx) { this.selectedTx = tx; this.confirmModalOpen = true; this.$nextTick(() => { if (window.lucide) lucide.createIcons() }); },
                openRejectModal(tx) { this.selectedTx = tx; this.rejectReason = ''; this.rejectModalOpen = true; },
                openDetailModal(tx) { this.selectedTx = tx; this.detailModalOpen = true; },
                
                async submitConfirm() {
                    if (!this.selectedTx) return;
                    this.isProcessing = true;
                    try {
                        const targetId = this.selectedTx.booking_id || this.selectedTx.id;
                        const res = await fetch(`/bookings/${targetId}/approve-payment`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                        });
                        if (!res.ok) throw new Error('Gagal konfirmasi');
                        this.confirmModalOpen = false;
                        this.loadTransactions(true);
                    } catch (err) { alert(err.message); } finally { this.isProcessing = false; }
                },
                
                async submitReject() {
                    if (!this.selectedTx) return;
                    this.isProcessing = true;
                    try {
                        const targetId = this.selectedTx.booking_id || this.selectedTx.id;
                        const res = await fetch(`/bookings/${targetId}/reject-payment`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                            body: JSON.stringify({ reason: this.rejectReason })
                        });
                        if (!res.ok) throw new Error('Gagal menolak');
                        this.rejectModalOpen = false;
                        this.loadTransactions(true);
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
                paymentClass(status) {
                    if (status === 'Berhasil' || status === 'Lunas') return 'bg-emerald-50 text-emerald-700 border-emerald-200';
                    if (status === 'Tunggu Konfirmasi') return 'bg-yellow-50 text-yellow-700 border-yellow-200 animate-pulse';
                    if (status === 'Pending') return 'bg-gray-100 text-gray-600 border-gray-300';
                    if (status === 'Ditolak') return 'bg-red-50 text-red-600 border-red-200';
                    return 'bg-gray-100 text-gray-700 border-gray-300';
                },
                paymentLabel(status) {
                    return status || '-';
                },
                paymentTypeLabel(t) {
                    const v = (t || '').toUpperCase();
                    if (v === 'LUNAS') return 'Lunas Penuh';
                    if (v === 'PELUNASAN') return 'Pelunasan';
                    if (v === 'DP') return 'DP';
                    return v;
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