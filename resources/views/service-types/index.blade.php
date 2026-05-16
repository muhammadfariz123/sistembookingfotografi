<x-app-layout>
    <div
        x-data="serviceTypePage(@js($services))"
        class="px-3 sm:px-6 lg:px-8 py-6 sm:py-8 bg-[#f5f7fb] min-h-screen overflow-x-hidden">
        <!-- CONTAINER -->
        <div class="bg-white border border-gray-200 rounded-[20px] sm:rounded-[30px] shadow-sm p-4 sm:p-6 lg:p-8">
            <!-- BACK -->
            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center gap-2 text-gray-600 hover:text-blue-600 text-[13px] sm:text-[14px] font-medium transition">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Kembali ke Dashboard
            </a>
            <!-- HEADER -->
            <div class="mt-5 sm:mt-6 flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4 sm:gap-6">
                <div>
                    <h1 class="text-[22px] sm:text-[26px] lg:text-[30px] font-bold text-[#0f172a]">
                        Kelola Jenis Layanan
                    </h1>
                    <p class="text-[13px] sm:text-[15px] text-gray-500 mt-2">
                        Tambahkan dan kelola jenis layanan yang Anda tawarkan
                    </p>
                </div>
                <button
                    @click="openCreateModal()"
                    class="h-[44px] sm:h-[48px] px-5 sm:px-6 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-[14px] sm:text-[15px] flex items-center justify-center gap-2 shadow-lg shadow-blue-200 transition whitespace-nowrap w-full lg:w-auto">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Tambah Layanan
                </button>
            </div>
            <!-- TABLE DESKTOP -->
            <div class="mt-6 sm:mt-8 hidden md:block border border-gray-200 rounded-[22px] overflow-hidden">
                <div class="overflow-x-auto no-scrollbar">
                    <table class="w-full">
                        <thead class="bg-[#f8fafc]">
                            <tr class="text-left">
                                @foreach (['Nama Layanan', 'Deskripsi', 'Harga Default'] as $head)
                                    <th class="px-4 lg:px-6 py-4 text-[12px] font-semibold tracking-wide text-gray-500 uppercase">
                                        {{ $head }}
                                    </th>
                                @endforeach
                                <th class="px-4 lg:px-6 py-4 text-[12px] font-semibold tracking-wide text-gray-500 uppercase text-right">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <template x-if="services.length > 0">
                                <template x-for="item in services" :key="item.id">
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 lg:px-6 py-5">
                                            <p class="font-semibold text-[14px] lg:text-[15px] text-[#0f172a]"
                                                x-text="item.name"></p>
                                        </td>
                                        <td class="px-4 lg:px-6 py-5">
                                            <p class="text-[13px] lg:text-[14px] text-gray-500"
                                                x-text="item.description || '-'"></p>
                                        </td>
                                        <td class="px-4 lg:px-6 py-5 whitespace-nowrap">
                                            <p class="text-[13px] lg:text-[14px] font-medium text-[#0f172a]"
                                                x-text="formatCurrency(item.price)"></p>
                                        </td>
                                        <td class="px-4 lg:px-6 py-5">
                                            <div class="flex justify-end gap-3">
                                                <button type="button" @click="openEditModal(item)"
                                                    class="w-9 h-9 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-600 flex items-center justify-center transition">
                                                    <i data-lucide="pencil" class="w-4 h-4"></i>
                                                </button>
                                                <button type="button" @click="confirmDelete(item)"
                                                    class="w-9 h-9 rounded-xl bg-red-50 hover:bg-red-100 text-red-500 flex items-center justify-center transition">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </template>
                            <template x-if="services.length === 0">
                                <tr>
                                    <td colspan="4" class="py-16 text-center text-[14px] text-gray-500">
                                        Belum ada layanan. Klik
                                        <span class="font-semibold text-blue-600">"Tambah Layanan"</span>
                                        untuk membuat layanan baru.
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- MOBILE -->
            <div class="mt-6 md:hidden space-y-3">
                <template x-if="services.length === 0">
                    <div class="py-16 text-center text-[14px] text-gray-500">
                        Belum ada layanan. Klik
                        <span class="font-semibold text-blue-600">"Tambah Layanan"</span>
                        untuk membuat layanan baru.
                    </div>
                </template>
                <template x-for="item in services" :key="'mobile-' + item.id">
                    <div class="border border-gray-200 rounded-2xl p-4 bg-white">
                        <div class="flex justify-between items-start gap-3">
                            <div class="flex-1">
                                <p class="text-[11px] font-semibold tracking-wide text-gray-400 uppercase">Nama Layanan</p>
                                <p class="mt-1 font-semibold text-[15px] text-[#0f172a]" x-text="item.name"></p>
                            </div>
                            <div class="flex gap-2">
                                <button type="button" @click="openEditModal(item)"
                                    class="w-9 h-9 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-600 flex items-center justify-center">
                                    <i data-lucide="pencil" class="w-4 h-4"></i>
                                </button>
                                <button type="button" @click="confirmDelete(item)"
                                    class="w-9 h-9 rounded-xl bg-red-50 hover:bg-red-100 text-red-500 flex items-center justify-center">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mt-4">
                            <p class="text-[11px] font-semibold tracking-wide text-gray-400 uppercase">Deskripsi</p>
                            <p class="mt-1 text-[13px] text-gray-600" x-text="item.description || '-'"></p>
                        </div>
                        <div class="mt-4">
                            <p class="text-[11px] font-semibold tracking-wide text-gray-400 uppercase">Harga Default</p>
                            <p class="mt-1 text-[14px] font-semibold text-[#0f172a]"
                                x-text="formatCurrency(item.price)"></p>
                        </div>
                    </div>
                </template>
            </div>
        </div>
        <!-- MODAL -->
        <div x-show="showModal" x-cloak x-transition.opacity class="fixed inset-0 z-50 overflow-y-auto">
            <div @click="closeModal()" class="fixed inset-0 bg-black/35 backdrop-blur-sm"></div>
            <div class="relative min-h-screen flex items-center justify-center p-3 sm:p-4">
                <div @click.stop
                    class="bg-white w-full max-w-[560px] rounded-[22px] sm:rounded-[28px] shadow-2xl overflow-hidden">
                    <!-- HEADER MODAL -->
                    <div class="px-5 sm:px-6 py-4 sm:py-5 border-b border-gray-200 flex items-center justify-between">
                        <h2 class="text-[20px] sm:text-[24px] font-bold text-[#1e293b]"
                            x-text="editing ? 'Edit Layanan' : 'Tambah Layanan Baru'"></h2>
                        <button @click="closeModal()" :disabled="submitLoading"
                            class="text-gray-400 hover:text-gray-600 transition disabled:opacity-40">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <!-- BODY MODAL -->
                    <div class="px-5 sm:px-6 py-5 sm:py-6 space-y-5">
                        <!-- ERROR VALIDASI -->
                        <div x-show="Object.keys(errors).length > 0" x-cloak
                            class="bg-red-50 border border-red-200 rounded-2xl px-4 py-3">
                            <ul class="space-y-1">
                                <template x-for="(msgs, field) in errors" :key="field">
                                    <template x-for="msg in msgs" :key="msg">
                                        <li class="text-[12px] text-red-600 flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 shrink-0"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span x-text="msg"></span>
                                        </li>
                                    </template>
                                </template>
                            </ul>
                        </div>
                        <!-- NAMA -->
                        <div>
                            <label class="block text-[14px] font-semibold text-gray-700 mb-2">
                                Nama Layanan <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                x-model="form.name"
                                :class="errors.name ? 'border-red-400 ring-1 ring-red-400' : 'border-gray-300'"
                                placeholder="Contoh: Wedding, Prewedding, Wisuda"
                                class="w-full h-[48px] rounded-2xl border text-[14px] px-4 focus:outline-none focus:ring-2 focus:ring-blue-300 transition">
                        </div>
                        <!-- DESKRIPSI -->
                        <div>
                            <label class="block text-[14px] font-semibold text-gray-700 mb-2">
                                Deskripsi (Opsional)
                            </label>
                            <textarea
                                x-model="form.description"
                                rows="4"
                                placeholder="Deskripsi layanan..."
                                class="w-full rounded-2xl border border-gray-300 text-[14px] px-4 py-3 resize-none focus:outline-none focus:ring-2 focus:ring-blue-300 transition"></textarea>
                        </div>
                        <!-- HARGA -->
                        <div>
                            <label class="block text-[14px] font-semibold text-gray-700 mb-2">
                                Harga Default (Opsional)
                            </label>
                            <input
                                type="text"
                                x-model="form.price_display"
                                @input="formatPrice($event)"
                                placeholder="0"
                                class="w-full h-[48px] rounded-2xl border border-gray-300 text-[14px] px-4 focus:outline-none focus:ring-2 focus:ring-blue-300 transition">
                            <p class="text-[12px] text-gray-500 mt-1">
                                Harga ini akan diisi otomatis saat membuat booking (bisa diubah nanti)
                            </p>
                        </div>
                    </div>
                    <!-- FOOTER MODAL -->
                    <div class="px-5 sm:px-6 pb-5 sm:pb-6 flex flex-col-reverse sm:flex-row justify-end gap-2">
                        <button
                            type="button"
                            @click="closeModal()"
                            :disabled="submitLoading"
                            class="h-[46px] px-6 rounded-2xl border border-gray-300 text-gray-600 font-medium text-[14px] disabled:opacity-60 transition">
                            Batal
                        </button>
                        <button
                            type="button"
                            @click="submitForm()"
                            :disabled="submitLoading"
                            class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-semibold h-[46px] px-6 rounded-2xl transition flex items-center justify-center gap-2 min-w-[170px]">
                            <svg x-show="submitLoading"
                                class="animate-spin h-4 w-4 shrink-0"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
                            </svg>
                            <svg x-show="!submitLoading"
                                xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M17 21H7a2 2 0 01-2-2V5a2 2 0 012-2h7l5 5v11a2 2 0 01-2 2z"/>
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M17 21v-8H7v8M7 3v5h8"/>
                            </svg>
                            <span x-text="submitLoading
                                ? 'Menyimpan...'
                                : (editing ? 'Simpan Perubahan' : 'Simpan')">
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) lucide.createIcons()
        })

        // ✅ Helper: re-render semua lucide icon di DOM
        function refreshIcons() {
            if (window.lucide) {
                // Delay kecil agar Alpine selesai update DOM lebih dulu
                setTimeout(() => lucide.createIcons(), 50)
            }
        }

        function serviceTypePage(services) {
            return {
                showModal:     false,
                editing:       false,
                submitLoading: false,
                services,
                errors: {},
                form: {
                    id:            null,
                    name:          '',
                    description:   '',
                    price:         0,
                    price_display: ''
                },
                // ✅ Watch perubahan services → re-render icon
                init() {
                    this.$watch('services', () => refreshIcons())
                },
                formatCurrency(value) {
                    return new Intl.NumberFormat('id-ID', {
                        style:                 'currency',
                        currency:              'IDR',
                        minimumFractionDigits: 0
                    }).format(value || 0)
                },
                toRupiah(value) {
                    return new Intl.NumberFormat('id-ID').format(value || 0)
                },
                formatPrice(event) {
                    const raw = event.target.value.replace(/\D/g, '')
                    this.form.price         = raw ? parseInt(raw) : 0
                    this.form.price_display = raw
                        ? new Intl.NumberFormat('id-ID').format(parseInt(raw))
                        : ''
                },
                resetForm() {
                    this.form   = { id: null, name: '', description: '', price: 0, price_display: '' }
                    this.errors = {}
                },
                openCreateModal() {
                    this.editing       = false
                    this.submitLoading = false
                    this.resetForm()
                    this.showModal     = true
                },
                openEditModal(item) {
                    this.editing       = true
                    this.submitLoading = false
                    this.errors        = {}
                    const price        = item.price ? parseInt(item.price) : 0
                    this.form = {
                        id:            item.id,
                        name:          item.name,
                        description:   item.description || '',
                        price:         price,
                        price_display: this.toRupiah(price)
                    }
                    this.showModal = true
                },
                closeModal() {
                    if (this.submitLoading) return
                    this.showModal = false
                    this.errors    = {}
                },
                async submitForm() {
                    if (this.submitLoading) return
                    this.submitLoading = true
                    this.errors        = {}
                    const url    = this.editing
                        ? `/service-types/${this.form.id}`
                        : `/service-types`
                    const method = this.editing ? 'PUT' : 'POST'
                    try {
                        const res = await fetch(url, {
                            method,
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept':       'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                name:        this.form.name,
                                description: this.form.description,
                                price:       this.form.price
                            })
                        })
                        const result = await res.json()
                        if (res.status === 422) {
                            this.errors = result.errors ?? {}
                            return
                        }
                        if (!res.ok) {
                            Swal.fire({
                                icon:               'error',
                                title:              'Gagal!',
                                text:               result.message ?? 'Terjadi kesalahan.',
                                confirmButtonColor: '#2563eb',
                                customClass:        { popup: 'rounded-[28px]' }
                            })
                            return
                        }
                        if (this.editing) {
                            const idx = this.services.findIndex(s => s.id === this.form.id)
                            if (idx !== -1) this.services.splice(idx, 1, result.data)
                        } else {
                            this.services.unshift(result.data)
                        }
                        // ✅ Re-render icon setelah data ditambah/diedit
                        refreshIcons()
                        this.showModal = false
                        Swal.fire({
                            icon:               'success',
                            title:              'Berhasil!',
                            text:               result.message,
                            confirmButtonColor: '#2563eb',
                            timer:              2000,
                            timerProgressBar:   true,
                            showConfirmButton:  false,
                            customClass:        { popup: 'rounded-[28px]' }
                        })
                    } catch (err) {
                        Swal.fire({
                            icon:               'error',
                            title:              'Gagal!',
                            text:               'Gagal terhubung ke server.',
                            confirmButtonColor: '#2563eb',
                            customClass:        { popup: 'rounded-[28px]' }
                        })
                    } finally {
                        this.submitLoading = false
                    }
                },
                async confirmDelete(item) {
                    const confirm = await Swal.fire({
                        title:              `Hapus "${item.name}"?`,
                        text:               'Layanan yang sedang digunakan booking tidak bisa dihapus.',
                        icon:               'warning',
                        showCancelButton:   true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor:  '#6b7280',
                        confirmButtonText:  'Ya, Hapus',
                        cancelButtonText:   'Batal',
                        reverseButtons:     true,
                        customClass:        { popup: 'rounded-[28px]' }
                    })
                    if (!confirm.isConfirmed) return
                    try {
                        const res  = await fetch(`/service-types/${item.id}`, {
                            method: 'DELETE',
                            headers: {
                                'Accept':       'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        })
                        const data = await res.json()
                        if (!res.ok) {
                            const msg = res.status === 500
                                ? `Layanan "${item.name}" tidak bisa dihapus karena masih digunakan oleh data booking.`
                                : (data.message ?? 'Gagal menghapus.')
                            Swal.fire({
                                icon:               'warning',
                                title:              'Tidak Bisa Dihapus',
                                text:               msg,
                                confirmButtonColor: '#2563eb',
                                customClass:        { popup: 'rounded-[28px]' }
                            })
                            return
                        }
                        this.services = this.services.filter(s => s.id !== item.id)
                        // ✅ Re-render icon setelah hapus (sisa item perlu icon-nya tetap tampil)
                        refreshIcons()
                        Swal.fire({
                            icon:               'success',
                            title:              'Dihapus!',
                            text:               data.message,
                            confirmButtonColor: '#2563eb',
                            timer:              2000,
                            timerProgressBar:   true,
                            showConfirmButton:  false,
                            customClass:        { popup: 'rounded-[28px]' }
                        })
                    } catch (err) {
                        Swal.fire({
                            icon:               'error',
                            title:              'Gagal!',
                            text:               'Gagal terhubung ke server.',
                            confirmButtonColor: '#2563eb',
                            customClass:        { popup: 'rounded-[28px]' }
                        })
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        html, body { overflow-x: hidden; max-width: 100%; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
    </style>
</x-app-layout>