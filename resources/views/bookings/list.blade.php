{{-- resources/views/bookings/list.blade.php --}}
<x-app-layout>
    <div x-data="bookingListApp()" x-init="init()" class="px-4 sm:px-6 lg:px-8 py-8 bg-[#f5f7fb] min-h-screen overflow-x-hidden">

        {{-- ========================================================= --}}
        {{-- TOOLBAR: JUDUL, SEARCH & TOMBOL BUAT BOOKING BARU --}}
        {{-- ========================================================= --}}
        <div id="data-area" class="w-full">
            <div class="bg-white p-4 rounded-t-[24px] border border-gray-100 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
                
                <div class="flex items-center gap-2 px-2">
                    <i data-lucide="calendar-days" class="w-5 h-5 text-gray-400"></i>
                    <h2 class="text-[16px] font-bold text-gray-800">Daftar Booking Klien</h2>
                </div>

                <div class="flex flex-col sm:flex-row w-full md:w-auto gap-3 items-center">
                    {{-- Search Bar --}}
                    <div class="relative w-full sm:w-[240px]">
                        <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                        <input type="text" x-model="searchQuery"
                            placeholder="Cari klien atau paket..."
                            class="w-full h-[42px] pl-10 pr-4 rounded-xl border border-gray-200 bg-white text-[13px] focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    </div>

                    {{-- Tombol Buat Booking Baru (Sesuai Referensi) --}}
                    <a href="{{ route('booking.public.form', ['ownerId' => Auth::id()]) }}" target="_blank"
                        class="h-[42px] px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-[13px] rounded-xl flex items-center justify-center gap-2 shadow-sm transition-all whitespace-nowrap w-full sm:w-auto">
                        <i data-lucide="plus" class="w-4 h-4"></i> Buat Booking Baru
                    </a>
                </div>
            </div>

            <div class="bg-white border-x border-b border-gray-100 rounded-b-[24px] shadow-sm overflow-hidden">

                {{-- State Loading --}}
                <div x-show="loading" class="h-[320px] flex flex-col items-center justify-center gap-3">
                    <svg class="animate-spin w-8 h-8 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
                    </svg>
                    <p class="text-[14px] text-gray-400">Memuat data booking...</p>
                </div>

                <div x-show="!loading" x-cloak>

                    {{-- ========================================================= --}}
                    {{-- FILTER TAB STATUS (tengah atas) --}}
                    {{-- ========================================================= --}}
                    <div class="flex justify-center py-4 border-b border-gray-100">
                        <div class="inline-flex items-center gap-1 bg-gray-50 rounded-xl p-1 border border-gray-100 flex-wrap">
                            <template x-for="tab in statusTabs" :key="tab.key">
                                <button type="button"
                                    @click="filterStatus = tab.key"
                                    :class="filterStatus === tab.key
                                        ? 'bg-blue-50 text-blue-600 border border-blue-200'
                                        : 'text-gray-500 hover:bg-gray-100 border border-transparent'"
                                    class="flex items-center gap-2 px-4 py-2 rounded-lg text-[13px] font-semibold transition">
                                    <span x-text="tab.label"></span>
                                    <span :class="filterStatus === tab.key ? 'bg-blue-100 text-blue-600' : 'bg-gray-200 text-gray-600'"
                                        class="text-[11px] font-bold px-2 py-0.5 rounded-full min-w-[20px] text-center"
                                        x-text="statusCounts[tab.key] ?? 0"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    {{-- ========================================================= --}}
                    {{-- BULK ACTIONS TOOLBAR (muncul saat ada yang dicentang) --}}
                    {{-- ========================================================= --}}
                    <div x-show="selectedIds.length > 0" x-cloak x-transition
                        class="flex items-center justify-between px-8 py-3 bg-blue-50/60 border-b border-blue-100">
                        <div class="relative">
                            <button type="button" @click="bulkMenuOpen = !bulkMenuOpen"
                                class="flex items-center gap-2 px-3 py-2 bg-white border border-gray-200 rounded-lg text-[13px] font-semibold text-gray-700 hover:bg-gray-50 transition">
                                <i data-lucide="more-vertical" class="w-4 h-4"></i>
                                Bulk actions
                                <span class="text-blue-600" x-text="`(${selectedIds.length})`"></span>
                            </button>
                            <div x-show="bulkMenuOpen" @click.outside="bulkMenuOpen = false" x-transition
                                class="absolute left-0 mt-2 w-52 bg-white border border-gray-100 rounded-lg shadow-lg z-20 py-1">
                                <button type="button" @click="bulkMenuOpen = false; bulkDeleteModalOpen = true"
                                    class="w-full flex items-center gap-2 px-4 py-2 text-[13px] text-red-500 hover:bg-red-50 transition font-medium">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i> Delete selected
                                </button>
                            </div>
                        </div>
                        <button type="button" @click="selectedIds = []"
                            class="text-[13px] font-semibold text-blue-600 hover:text-blue-700 transition">
                            Deselect all
                        </button>
                    </div>

                    {{-- Tabel Data --}}
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[950px]">
                            <thead>
                                <tr class="text-left text-gray-500 text-sm border-b border-gray-100 bg-gray-50/50">
                                    <th class="px-6 py-6 w-10">
                                        <input type="checkbox" :checked="isAllSelected" @change="toggleSelectAll()"
                                            class="w-4 h-4 rounded border-gray-300 text-blue-500 focus:ring-blue-400 cursor-pointer">
                                    </th>
                                    <th class="px-4 py-6 whitespace-nowrap font-semibold tracking-wide">KLIEN</th>
                                    <th class="px-4 py-6 whitespace-nowrap font-semibold tracking-wide">LAYANAN</th>
                                    <th class="px-4 py-6 whitespace-nowrap font-semibold tracking-wide">TANGGAL & WAKTU</th>
                                    <th class="px-4 py-6 whitespace-nowrap font-semibold tracking-wide">STATUS JADWAL</th>
                                    <th class="px-4 py-6 whitespace-nowrap font-semibold tracking-wide text-center">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-if="filteredBookings.length > 0">
                                    <template x-for="booking in filteredBookings" :key="booking.id">
                                        <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition"
                                            :class="selectedIds.includes(booking.id) ? 'bg-blue-50/40' : ''">

                                            {{-- Checkbox --}}
                                            <td class="px-6 py-6 align-top">
                                                <input type="checkbox" :checked="selectedIds.includes(booking.id)"
                                                    @change="toggleSelect(booking.id)"
                                                    class="w-4 h-4 rounded border-gray-300 text-blue-500 focus:ring-blue-400 cursor-pointer">
                                            </td>

                                            {{-- Klien --}}
                                            <td class="px-4 py-6 align-top">
                                                <p class="font-bold text-[15px] text-gray-900" x-text="booking.client_name"></p>
                                                <p class="text-[13px] text-gray-500 mt-0.5" x-text="booking.client_contact || '-'"></p>
                                            </td>

                                            {{-- Layanan --}}
                                            <td class="px-4 py-6 align-top">
                                                <p class="font-semibold text-[14px] text-gray-900" x-text="booking.service_type?.name || '-'"></p>
                                            </td>

                                            {{-- Tanggal --}}
                                            <td class="px-4 py-6 align-top">
                                                <p class="text-[14px] font-medium text-gray-800" x-text="formatDate(booking.booking_date || booking.start_date)"></p>
                                                <p class="text-[12px] text-gray-500 mt-0.5" x-text="booking.booking_time"></p>
                                            </td>

                                            {{-- Status --}}
                                            <td class="px-4 py-6 align-top">
                                                <span :class="statusClass(booking.status)" class="inline-flex items-center px-3 py-1 rounded-full text-[12px] font-semibold border">
                                                    <span x-text="booking.status"></span>
                                                </span>
                                            </td>

                                            {{-- Aksi --}}
                                            <td class="px-4 py-6 align-top text-center">
                                                <div class="flex items-center justify-center gap-2">
                                                    <a :href="`/bookings/${booking.id}/edit`" class="w-9 h-9 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-600 flex items-center justify-center transition" title="Edit Jadwal">
                                                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                                                    </a>
                                                    <button type="button" @click="deleteBooking(booking)" class="w-9 h-9 rounded-xl bg-red-50 hover:bg-red-100 text-red-500 flex items-center justify-center transition" title="Hapus">
                                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                </template>
                                <template x-if="!loading && filteredBookings.length === 0">
                                    <tr><td colspan="6"><div class="h-[300px] flex items-center justify-center text-gray-400 font-bold text-xl">Belum ada booking</div></td></tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- MODAL: HAPUS MASSAL (BULK DELETE) --}}
        {{-- ========================================================= --}}
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
                <h3 class="text-white font-bold text-lg mb-2">Hapus Booking Terpilih?</h3>
                <p class="text-gray-400 text-sm mb-6 px-2">
                    <span x-text="selectedIds.length"></span> booking akan dihapus permanen dan tidak bisa dikembalikan.
                </p>
                <div class="flex gap-3 justify-center">
                    <button type="button" @click="bulkDeleteModalOpen = false" class="bg-[#27272a] hover:bg-[#3f3f46] border border-gray-700 text-white px-6 py-2.5 rounded-xl font-medium transition text-sm flex-1">
                        Batal
                    </button>
                    <button type="button" @click="submitBulkDelete()" :disabled="isBulkDeleting" class="bg-red-600 hover:bg-red-500 text-white px-6 py-2.5 rounded-xl font-medium transition text-sm flex-1 disabled:opacity-50">
                        <span x-show="!isBulkDeleting">Hapus Sekarang</span>
                        <span x-show="isBulkDeleting">Menghapus...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        function bookingListApp() {
            return {
                bookings: [],
                loading: true,
                searchQuery: '',
                filterStatus: 'semua',
                statusTabs: [
                    { key: 'semua', label: 'Semua' },
                    { key: 'dijadwalkan', label: 'Terjadwal' },
                    { key: 'proses_editing', label: 'Proses Editing' },
                    { key: 'pembayaran_tertunda', label: 'Pending Bayar' },
                    { key: 'selesai', label: 'Selesai' },
                    { key: 'dibatalkan', label: 'Dibatalkan' },
                ],
                selectedIds: [],
                bulkMenuOpen: false,
                bulkDeleteModalOpen: false,
                isBulkDeleting: false,

                init() {
                    this.$nextTick(() => { if (window.lucide) lucide.createIcons() })
                    this.loadBookings();
                    setInterval(() => {
                        if (document.visibilityState === 'visible') {
                            this.loadBookings(true);
                        }
                    }, 5000);
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

                mapStatusToTabKey(status) {
                    const map = {
                        'Dijadwalkan': 'dijadwalkan',
                        'Proses Edit': 'proses_editing',
                        'Pembayaran Tertunda': 'pembayaran_tertunda',
                        'Selesai': 'selesai',
                        'Dibatalkan': 'dibatalkan',
                    };
                    return map[status] ?? null;
                },

                get statusCounts() {
                    const counts = { semua: this.bookings.length, dijadwalkan: 0, proses_editing: 0, pembayaran_tertunda: 0, selesai: 0, dibatalkan: 0 };
                    this.bookings.forEach(b => {
                        const key = this.mapStatusToTabKey(b.status);
                        if (key && counts.hasOwnProperty(key)) counts[key]++;
                    });
                    return counts;
                },

                get filteredBookings() {
                    let result = [...this.bookings];
                    if (this.filterStatus && this.filterStatus !== 'semua') {
                        result = result.filter(b => this.mapStatusToTabKey(b.status) === this.filterStatus)
                    }
                    if (this.searchQuery.trim()) {
                        const q = this.searchQuery.toLowerCase().trim()
                        result = result.filter(b => (b.client_name ?? '').toLowerCase().includes(q) || (b.service_type?.name ?? '').toLowerCase().includes(q))
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
                    this.isBulkDeleting = true;
                    try {
                        const res = await fetch('/bookings/bulk-delete', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ ids: this.selectedIds })
                        });
                        const data = await res.json();
                        if (!res.ok) throw new Error(data.message);
                        this.bulkDeleteModalOpen = false;
                        this.selectedIds = [];
                        if (window.Swal) {
                            Swal.fire({ icon: 'success', title: 'Terhapus!', text: data.message || 'Booking berhasil dihapus', timer: 2000, showConfirmButton: false, customClass: { popup: 'rounded-[28px]' } });
                        }
                        this.loadBookings(true);
                    } catch (err) {
                        if (window.Swal) {
                            Swal.fire({ icon: 'error', title: 'Gagal', text: err.message, customClass: { popup: 'rounded-[28px]' } });
                        } else {
                            alert(err.message);
                        }
                    } finally {
                        this.isBulkDeleting = false;
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
                        this.selectedIds = this.selectedIds.filter(id => id !== booking.id)
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
                },

                formatDate(d) {
                    return d ? new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }).format(new Date(d)) : '-'
                },

                statusClass(s) {
                    if (s === 'Selesai') return 'bg-emerald-50 text-emerald-700 border-emerald-200';
                    if (s === 'Dibatalkan') return 'bg-rose-50 text-rose-700 border-rose-200';
                    if (s === 'Pembayaran Tertunda') return 'bg-orange-50 text-orange-700 border-orange-200';
                    if (s === 'Proses Edit') return 'bg-purple-50 text-purple-700 border-purple-200';
                    return 'bg-blue-50 text-blue-700 border-blue-200';
                }
            }
        }
        document.addEventListener('DOMContentLoaded', () => { if (window.lucide) lucide.createIcons() })
    </script>
</x-app-layout>