{{-- resources/views/components/dashboard/invoice-modal.blade.php --}}
<div x-data="invoiceModal()" @open-invoice.window="openModal($event.detail)" x-show="show" x-cloak x-transition.opacity
    class="fixed inset-0 z-[60]">
    <div @click="show = false" class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div @click.stop
            class="bg-white w-full max-w-[860px] rounded-[22px] shadow-2xl overflow-hidden max-h-[95vh] flex flex-col">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between shrink-0 bg-white">
                <h2 class="text-[18px] font-bold text-gray-900">Generate Invoice</h2>
                <div class="flex items-center gap-2">
                    <button @click="printInvoice()"
                        class="h-[36px] px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-[13px] font-semibold flex items-center gap-2 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Print / Save PDF
                    </button>
                    <button @click="show = false"
                        class="w-8 h-8 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div x-show="loading" class="flex-1 flex items-center justify-center py-16">
                <svg class="animate-spin w-8 h-8 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
                </svg>
            </div>

            <div x-show="!loading" class="flex-1 overflow-y-auto no-scrollbar">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-[15px] font-bold text-gray-800">Pengaturan Invoice</h3>
                        <button @click="resetSettings()"
                            class="text-[12px] text-gray-500 hover:text-gray-700 flex items-center gap-1 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Reset
                        </button>
                    </div>

                    <div class="bg-white rounded-2xl border border-gray-200 p-4">
                        <p class="text-[13px] font-semibold text-gray-700 mb-3">Jenis Invoice</p>
                        <div class="flex flex-wrap gap-6">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" x-model="invoiceType" value="dp"
                                    class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                <span class="text-[14px] text-gray-700">Invoice DP</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" x-model="invoiceType" value="pelunasan"
                                    class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                <span class="text-[14px] text-gray-700">Invoice Pelunasan</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" x-model="invoiceType" value="penuh"
                                    class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                <span class="text-[14px] text-gray-700">Invoice Penuh</span>
                            </label>
                        </div>

                        <div x-show="invoiceType === 'dp'" class="mt-4 space-y-3">
                            <div class="flex gap-2">
                                <button @click="dpMethod = 'percent'"
                                    :class="dpMethod === 'percent' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700'"
                                    class="px-4 h-[32px] rounded-xl text-[13px] font-semibold transition">
                                    Persentase (%)
                                </button>
                                <button @click="dpMethod = 'nominal'"
                                    :class="dpMethod === 'nominal' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700'"
                                    class="px-4 h-[32px] rounded-xl text-[13px] font-semibold transition">
                                    Nominal (Rp)
                                </button>
                            </div>

                            <template x-if="dpMethod === 'percent'">
                                <div>
                                    <p class="text-[13px] font-medium text-gray-600 mb-2">Persentase DP</p>
                                    <div class="flex items-center gap-3">
                                        <input type="range" x-model="dpPercent" min="1" max="100"
                                            class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-blue-600">
                                        <div class="w-[60px] h-[36px] rounded-xl border border-gray-300 flex items-center justify-center text-[13px] font-bold text-blue-600"
                                            x-text="dpPercent + '%'"></div>
                                    </div>
                                    <p class="text-[12px] text-blue-600 mt-1">
                                        DP: <span x-text="formatRp(dpAmount)"></span>
                                        dari total <span x-text="formatRp(booking?.total ?? 0)"></span>
                                    </p>
                                </div>
                            </template>

                            <template x-if="dpMethod === 'nominal'">
                                <div>
                                    <p class="text-[13px] font-medium text-gray-600 mb-2">Nominal DP (Rp)</p>
                                    <div class="relative">
                                        <input
                                            type="text"
                                            :value="dpNominalDisplay"
                                            @input="handleDpNominalInput($event.target)"
                                            @focus="handleDpNominalFocus($event.target)"
                                            @blur="handleDpNominalBlur($event.target)"
                                            placeholder="0"
                                            class="w-full h-[44px] rounded-xl border border-gray-300 px-4 text-[14px] focus:ring-2 focus:ring-blue-500 focus:border-transparent pr-[72px]">
                                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-[13px] text-gray-400 font-medium pointer-events-none">
                                            Rupiah
                                        </span>
                                    </div>
                                    <p class="text-[12px] text-blue-600 mt-1.5">
                                        DP: <span x-text="formatRp(dpNominal)"></span>
                                        dari total <span x-text="formatRp(booking?.total ?? 0)"></span>
                                        (<span x-text="dpNominalPercent"></span>%)
                                    </p>
                                    <div class="flex items-center gap-2 mt-2 flex-wrap">
                                        <span class="text-[12px] text-gray-500">Preset:</span>
                                        <template x-for="preset in [25, 30, 50]" :key="preset">
                                            <button type="button"
                                                @click="setDpPreset(preset)"
                                                :class="dpNominalPercent === preset ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                                class="px-3 h-[26px] rounded-lg text-[12px] font-semibold transition">
                                                <span x-text="preset + '% (' + formatRp(Math.round((booking?.total ?? 0) * preset / 100)) + ')'"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            <div class="bg-blue-50 rounded-xl p-3 text-[13px] space-y-1 border border-blue-100">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Total Keseluruhan:</span>
                                    <span class="font-semibold" x-text="formatRp(booking?.total ?? 0)"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-blue-600 font-medium" x-text="'DP (' + dpFinalPercent + '%):' "></span>
                                    <span class="font-semibold text-blue-600" x-text="formatRp(dpAmount)"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Sisa setelah DP:</span>
                                    <span class="font-semibold" x-text="formatRp((booking?.total ?? 0) - dpAmount)"></span>
                                </div>
                            </div>
                        </div>

                        <div x-show="invoiceType === 'pelunasan'" class="mt-4">
                            <div class="bg-orange-50 rounded-xl p-3 text-[13px] space-y-1 border border-orange-100">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Total Keseluruhan:</span>
                                    <span class="font-semibold" x-text="formatRp(booking?.total ?? 0)"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Sudah dibayar:</span>
                                    <span class="font-semibold" x-text="formatRp(booking?.paid_amount ?? 0)"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-red-600 font-medium">Sisa pembayaran:</span>
                                    <span class="font-bold text-red-600" x-text="formatRp(booking?.remaining ?? 0)"></span>
                                </div>
                            </div>
                        </div>

                        <div x-show="invoiceType === 'penuh'" class="mt-4">
                            <div class="bg-green-50 rounded-xl p-3 text-[13px] space-y-1 border border-green-100">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Total Keseluruhan:</span>
                                    <span class="font-semibold" x-text="formatRp(booking?.total ?? 0)"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-green-600 font-medium">Invoice ini:</span>
                                    <span class="font-bold text-green-600" x-text="formatRp(booking?.total ?? 0)"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-4">
                        <div>
                            <label class="block text-[12px] font-semibold text-gray-600 mb-1.5">Nomor Invoice</label>
                            <input type="text" x-model="invoiceNumber"
                                class="w-full h-[38px] rounded-xl border border-gray-300 px-3 text-[13px] focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                        </div>
                        <div>
                            <label class="block text-[12px] font-semibold text-gray-600 mb-1.5">Tanggal Invoice</label>
                            <input type="date" x-model="invoiceDate"
                                class="w-full h-[38px] rounded-xl border border-gray-300 px-3 text-[13px] focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                        </div>
                        <div>
                            <label class="block text-[12px] font-semibold text-gray-600 mb-1.5">Jatuh Tempo</label>
                            <input type="date" x-model="invoiceDue"
                                class="w-full h-[38px] rounded-xl border border-gray-300 px-3 text-[13px] focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                        </div>
                    </div>

                    <div class="mt-3 bg-yellow-50 border border-yellow-100 rounded-xl px-4 py-3 text-[12px] text-yellow-800">
                        💡 Pilih jenis invoice di atas:
                        <strong>Invoice DP</strong> untuk pembayaran awal,
                        <strong>Invoice Pelunasan</strong> untuk sisa pembayaran, atau
                        <strong>Invoice Penuh</strong> untuk pembayaran lengkap.
                        Diskon dan PPN diatur dari halaman edit booking.
                    </div>
                </div>

                <div id="invoice-preview" class="px-8 py-8 bg-white mx-auto w-full max-w-[794px] box-border transition-all">
                    <div class="flex items-start justify-between mb-6">
                        <div>
                            <template x-if="company?.company_logo">
                                <img :src="'/storage/' + company.company_logo" alt="Logo" class="h-12 object-contain mb-3">
                            </template>
                            <h2 class="text-[20px] font-bold text-blue-700 leading-none"
                                x-text="company?.company_name || 'Nama Perusahaan'"></h2>
                            <p class="text-[12px] text-gray-600 mt-1" x-text="company?.company_address || ''"></p>
                            <p class="text-[12px] text-gray-600" x-show="company?.company_phone"
                                x-text="'Tel: ' + company?.company_phone"></p>
                            <p class="text-[12px] text-gray-600" x-show="company?.company_email"
                                x-text="'Email: ' + company?.company_email"></p>
                        </div>
                        <div class="text-right">
                            <h1 class="text-[32px] font-black text-gray-900 tracking-tight leading-none">INVOICE</h1>
                            <p class="text-[13px] text-gray-500 mt-1" x-text="invoiceNumber"></p>
                        </div>
                    </div>

                    <hr class="border-gray-200 mb-5">

                    <div class="flex items-start justify-between mb-5">
                        <div>
                            <p class="text-[12px] font-semibold text-gray-500 uppercase tracking-wide mb-1">Bill To:</p>
                            <p class="text-[16px] font-bold text-gray-900" x-text="booking?.client_name || '-'"></p>
                            <p class="text-[13px] text-gray-600 mt-0.5" x-text="booking?.client_contact || '-'"></p>
                            <p class="text-[13px] text-gray-600" x-text="booking?.client_address || '-'"></p>
                        </div>
                        <div class="text-right space-y-1">
                            <div class="flex items-center gap-8 justify-end">
                                <span class="text-[12px] text-gray-500">Tanggal Invoice</span>
                                <span class="text-[13px] font-medium text-gray-800" x-text="formatDateID(invoiceDate)"></span>
                            </div>
                            <div class="flex items-center gap-8 justify-end">
                                <span class="text-[12px] text-gray-500">Jatuh Tempo</span>
                                <span class="text-[13px] font-medium text-gray-800" x-text="formatDateID(invoiceDue)"></span>
                            </div>
                            <div class="flex items-center gap-8 justify-end">
                                <span class="text-[12px] text-gray-500">Tanggal Layanan</span>
                                <span class="text-[13px] font-medium text-gray-800"
                                    x-text="formatDateID(booking?.booking_date ?? booking?.start_date)"></span>
                            </div>
                        </div>
                    </div>

                    <table class="w-full mb-5 text-[13px]">
                        <thead>
                            <tr class="bg-blue-600 text-white">
                                <th class="px-4 py-3 text-left font-semibold rounded-tl-lg">Deskripsi Layanan</th>
                                <th class="px-4 py-3 text-right font-semibold">Harga Paket</th>
                                <th class="px-4 py-3 text-right font-semibold">Tanggal & Waktu</th>
                                <th class="px-4 py-3 text-right font-semibold rounded-tr-lg">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border border-gray-200">
                                <td class="px-4 py-4 align-top">
                                    <p class="font-bold text-gray-900" x-text="booking?.service_type?.name || '-'"></p>
                                    <p class="text-[12px] text-blue-600 mt-0.5 whitespace-pre-line" x-text="booking?.service_type?.description || ''"></p>
                                    <p class="text-[12px] text-gray-500 mt-0.5" x-text="booking?.notes || ''"></p>
                                </td>
                                <td class="px-4 py-4 text-right" x-text="formatRp(booking?.unit_price ?? 0)"></td>
                                <td class="px-4 py-4 text-right" x-text="formatDateShort(booking?.booking_date ?? booking?.start_date)"></td>
                                <td class="px-4 py-4 text-right font-semibold text-gray-900"
                                    x-text="formatRp(booking?.subtotal ?? booking?.unit_price ?? 0)"></td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="flex justify-end mb-5">
                        <div class="w-full max-w-[340px] space-y-2 text-[13px]">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Subtotal:</span>
                                <span class="font-medium text-gray-800" x-text="formatRp(booking?.subtotal ?? booking?.total ?? 0)"></span>
                            </div>
                            <template x-if="(booking?.discount_amount ?? 0) > 0">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Diskon:</span>
                                    <span class="font-medium text-red-500" x-text="'- ' + formatRp(booking?.discount_amount ?? 0)"></span>
                                </div>
                            </template>
                            <div class="flex justify-between border-t border-gray-200 pt-2 mt-2">
                                <span class="text-gray-700 font-medium">Total keseluruhan:</span>
                                <span class="font-bold text-gray-900" x-text="formatRp(booking?.total ?? 0)"></span>
                            </div>
                            <template x-if="invoiceType === 'dp'">
                                <div>
                                    <div class="flex justify-between text-blue-600">
                                        <span class="font-medium" x-text="'DP (' + dpFinalPercent + '%):' "></span>
                                        <span class="font-bold" x-text="formatRp(dpAmount)"></span>
                                    </div>
                                    <div class="flex justify-between text-orange-500">
                                        <span>Sisa setelah DP ini:</span>
                                        <span class="font-bold" x-text="formatRp((booking?.total ?? 0) - dpAmount)"></span>
                                    </div>
                                    <div class="flex justify-between border-t-2 border-gray-800 pt-2 mt-2">
                                        <span class="font-bold text-gray-900 text-[14px]">Total invoice ini:</span>
                                        <span class="font-black text-gray-900 text-[15px]" x-text="formatRp(dpAmount)"></span>
                                    </div>
                                </div>
                            </template>
                            <template x-if="invoiceType === 'pelunasan'">
                                <div>
                                    <div class="flex justify-between text-gray-500">
                                        <span>Sudah dibayar:</span>
                                        <span x-text="formatRp(booking?.paid_amount ?? 0)"></span>
                                    </div>
                                    <div class="flex justify-between border-t-2 border-gray-800 pt-2 mt-2">
                                        <span class="font-bold text-gray-900 text-[14px]">Total invoice ini:</span>
                                        <span class="font-black text-gray-900 text-[15px]" x-text="formatRp(booking?.remaining ?? 0)"></span>
                                    </div>
                                </div>
                            </template>
                            <template x-if="invoiceType === 'penuh'">
                                <div class="flex justify-between border-t-2 border-gray-800 pt-2 mt-2">
                                    <span class="font-bold text-gray-900 text-[14px]">Total invoice ini:</span>
                                    <span class="font-black text-gray-900 text-[15px]" x-text="formatRp(booking?.total ?? 0)"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-xl p-5 mb-4">
                        <div class="flex justify-between items-center mb-2">
                            <p class="text-[14px] font-bold text-gray-800">Informasi Pembayaran</p>
                            <p class="text-[12px] text-gray-500">Status: <span class="font-semibold text-gray-700" x-text="booking?.payment_status || '-'"></span></p>
                        </div>
                        <div class="border-2 border-orange-300 rounded-lg p-3 text-center mb-4 bg-orange-50/50">
                            <p class="text-[12px] font-semibold text-orange-600 mb-1">
                                <span x-text="invoiceType === 'dp' ? 'Jumlah DP yang harus ditransfer' : 'Jumlah yang harus ditransfer'"></span>
                            </p>
                            <p class="text-[24px] font-black text-orange-600 leading-none" x-text="formatRp(invoiceAmount)"></p>
                        </div>
                        <template x-if="company?.bank_name || company?.bank_account">
                            <div>
                                <p class="text-[12px] font-semibold text-gray-700 mb-1.5">Transfer Bank:</p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <template x-if="company?.bank_name">
                                        <div class="bg-gray-50 rounded-lg p-2.5 text-[12px]">
                                            <p><strong>Bank:</strong> <span x-text="company?.bank_name"></span></p>
                                            <p><strong>No. Rek:</strong> <span x-text="company?.bank_account"></span></p>
                                            <p><strong>A.n:</strong> <span x-text="company?.bank_holder"></span></p>
                                        </div>
                                    </template>
                                    <template x-if="company?.bank_name_2">
                                        <div class="bg-gray-50 rounded-lg p-2.5 text-[12px]">
                                            <p><strong>Bank:</strong> <span x-text="company?.bank_name_2"></span></p>
                                            <p><strong>No. Rek:</strong> <span x-text="company?.bank_account_2"></span></p>
                                            <p><strong>A.n:</strong> <span x-text="company?.bank_holder_2"></span></p>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                        <template x-if="company?.payment_instruction">
                            <div class="mt-3 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                <p class="text-[11px] font-semibold text-yellow-800 mb-0.5">Instruksi Pembayaran:</p>
                                <p class="text-[11px] text-yellow-800" x-text="company?.payment_instruction"></p>
                            </div>
                        </template>
                    </div>

                    <p class="text-center text-[10px] text-gray-400 mt-4"
                        x-text="'Invoice ini dibuat secara otomatis oleh sistem ' + (company?.company_name || '')"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function invoiceModal() {
    return {
        show:               false,
        loading:            false,
        booking:            null,
        company:            null,
        invoiceType:        'dp',
        dpMethod:           'percent',
        dpPercent:          30,
        dpNominal:          0,
        dpNominalFocused:   false,
        invoiceNumber:      '',
        invoiceDate:        '',
        invoiceDue:         '',

        async openModal(bookingData) {
            this.show    = true
            this.loading = true
            this.booking = null
            this.company = null
            try {
                const res    = await fetch(`/invoices/${bookingData.id}`, {
                    headers: { 'Accept': 'application/json' }
                })
                const result = await res.json()
                if (result.success) {
                    this.booking     = result.booking
                    this.company     = result.company
                    this.invoiceType = 'dp'
                    this.dpMethod    = 'percent'
                    this.dpPercent   = 30
                    this.dpNominal   = Math.round((result.booking.total ?? 0) * 0.3)
                    const today = new Date()
                    const y = today.getFullYear()
                    const m = String(today.getMonth() + 1).padStart(2, '0')
                    const d = String(today.getDate()).padStart(2, '0')
                    const rand = Math.random().toString(36).substring(2, 7).toUpperCase()
                    this.invoiceNumber = `INV-${y}${m}${d}-${rand}`
                    this.invoiceDate   = `${y}-${m}-${d}`
                    const due = new Date(today)
                    due.setDate(due.getDate() + 7)
                    this.invoiceDue = due.toISOString().substring(0, 10)
                }
            } catch (e) {
                alert('Gagal memuat data invoice.')
                this.show = false
            } finally {
                this.loading = false
            }
        },

        get dpAmount() {
            const total = this.booking?.total ?? 0
            if (this.dpMethod === 'percent') {
                return Math.round(total * (this.dpPercent / 100))
            }
            return Math.min(this.dpNominal, total)
        },

        get dpFinalPercent() {
            const total = this.booking?.total ?? 0
            if (total <= 0) return 0
            return Math.round((this.dpAmount / total) * 100)
        },

        get dpNominalPercent() {
            const total = this.booking?.total ?? 0
            if (total <= 0) return 0
            return Math.round((this.dpNominal / total) * 100)
        },

        get dpNominalDisplay() {
            if (this.dpNominalFocused) {
                return this.dpNominal > 0 ? String(this.dpNominal) : ''
            }
            return this.dpNominal > 0
                ? new Intl.NumberFormat('id-ID').format(this.dpNominal)
                : ''
        },

        get invoiceAmount() {
            switch (this.invoiceType) {
                case 'dp':        return this.dpAmount
                case 'pelunasan': return this.booking?.remaining ?? 0
                case 'penuh':     return this.booking?.total ?? 0
                default:          return 0
            }
        },

        handleDpNominalFocus(el) {
            this.dpNominalFocused = true
            this.$nextTick(() => {
                el.value = this.dpNominal > 0 ? String(this.dpNominal) : ''
                el.select()
            })
        },

        handleDpNominalBlur(el) {
            this.dpNominalFocused = false
            const raw   = el.value.replace(/[^0-9]/g, '')
            const total = this.booking?.total ?? 0
            this.dpNominal = raw === '' ? 0 : Math.min(parseInt(raw), total)
            this.$nextTick(() => {
                el.value = this.dpNominal > 0
                    ? new Intl.NumberFormat('id-ID').format(this.dpNominal)
                    : ''
            })
        },

        handleDpNominalInput(el) {
            const raw   = el.value.replace(/[^0-9]/g, '')
            const total = this.booking?.total ?? 0
            this.dpNominal = raw === '' ? 0 : Math.min(parseInt(raw), total)
        },

        setDpPreset(percent) {
            const total    = this.booking?.total ?? 0
            this.dpNominal = Math.round(total * percent / 100)
            this.dpNominalFocused = false
        },

        resetSettings() {
            this.invoiceType      = 'dp'
            this.dpMethod         = 'percent'
            this.dpPercent        = 30
            this.dpNominal        = Math.round((this.booking?.total ?? 0) * 0.3)
            this.dpNominalFocused = false
        },

        formatRp(value) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(value || 0))
        },

        formatDateID(dateStr) {
            if (!dateStr) return '-'
            return new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }).format(new Date(dateStr))
        },

        formatDateShort(dateStr) {
            if (!dateStr) return '-'
            return new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }).format(new Date(dateStr))
        },

        printInvoice() {
            const content = document.getElementById('invoice-preview').innerHTML
            const win     = window.open('', '_blank')
            win.document.write(`
                <!DOCTYPE html><html><head>
                <meta charset="utf-8">
                <title>${this.invoiceNumber}</title>
                <script src="https://cdn.tailwindcss.com"><\/script>
                <style>
                    @media print { 
                        body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
                        /* Paksa 1 halaman jika memungkinkan */
                        html, body {
                            width: 100%;
                            height: 100%;
                            margin: 0;
                            padding: 0;
                            overflow: hidden;
                        }
                    }
                    body { font-family: sans-serif; padding: 10px; max-width: 800px; margin: auto; }
                </style>
                </head><body>${content}</body></html>
            `)
            win.document.close()
            setTimeout(() => { win.print() }, 500)
        }
    }
}
</script>