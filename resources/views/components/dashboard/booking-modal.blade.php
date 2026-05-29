<div x-show="showBookingModal" x-cloak x-transition.opacity class="fixed inset-0 z-50"
     @force-open-modal.window="typeof openBookingModal === 'function' ? openBookingModal() : showBookingModal = true"
     @force-close-modal.window="typeof closeBookingModal === 'function' ? closeBookingModal() : showBookingModal = false">
    
    <div @click="closeBookingModal()" class="absolute inset-0 bg-black/35 backdrop-blur-sm"></div>
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div @click.stop class="bg-white w-full max-w-[680px] rounded-[22px] shadow-2xl overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-[20px] font-bold text-gray-900" id="booking-modal-title">
                    Tambah Booking Baru
                </h2>
                <button @click="closeBookingModal()" class="text-gray-500 hover:text-gray-700 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form id="booking-form"
                x-data="bookingForm()"
                @open-edit-booking.window="openEditBooking($event.detail)"
                @submit.prevent="submitBooking()"
                class="max-h-[70vh] overflow-y-auto no-scrollbar px-6 py-6">
                
                <div x-show="Object.keys(submitErrors).length > 0" x-cloak
                    class="mb-5 bg-red-50 border border-red-200 rounded-2xl px-4 py-3">
                    <p class="text-[13px] font-semibold text-red-600 mb-1">Mohon periksa kembali:</p>
                    <ul class="space-y-1">
                        <template x-for="(msgs, field) in submitErrors" :key="field">
                            <template x-for="msg in msgs" :key="msg">
                                <li class="text-[12px] text-red-600 flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 shrink-0" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span x-text="msg"></span>
                                </li>
                            </template>
                        </template>
                    </ul>
                </div>
                
                <div>
                    <label class="block text-[14px] font-semibold text-gray-800 mb-2">
                        Nama Klien <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                        x-model="clientName"
                        id="field-client-name"
                        required
                        placeholder="Masukkan nama klien"
                        class="w-full h-[44px] rounded-2xl border border-gray-300 px-4 text-[14px] focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div class="mt-5">
                    <label class="block text-[14px] font-semibold text-gray-800 mb-2">
                        Kontak Klien <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                        x-model="clientContact"
                        id="field-client-contact"
                        required
                        placeholder="Nomor telepon atau email"
                        class="w-full h-[44px] rounded-2xl border border-gray-300 px-4 text-[14px]">
                </div>
                <div class="mt-5">
                    <label class="block text-[14px] font-semibold text-gray-800 mb-2">
                        Alamat Klien <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                        x-model="clientAddress"
                        id="field-client-address"
                        required
                        placeholder="Alamat klien"
                        class="w-full h-[44px] rounded-2xl border border-gray-300 px-4 text-[14px]">
                </div>
                <div class="mt-5 relative">
                    <label class="block text-[14px] font-semibold text-gray-800 mb-2">
                        Paket Layanan <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                        :value="selectedService"
                        required
                        tabindex="-1"
                        style="position:absolute;opacity:0;width:1px;height:1px;pointer-events:none;"
                        aria-hidden="true">
                    <button type="button"
                        @click="showSummary = true; toggleServiceDropdown()"
                        id="field-service-type"
                        class="w-full h-[44px] rounded-2xl border border-gray-300 px-4 text-left text-[14px] flex items-center justify-between bg-white">
                        <span :class="selectedService ? 'text-gray-900' : 'text-gray-400'"
                            x-text="selectedService || 'Pilih atau tambah paket layanan'"></span>
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="w-4 h-4 text-gray-400 transition-transform duration-200"
                            :class="showServiceDropdown ? 'rotate-0' : 'rotate-180'"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                        </svg>
                    </button>
                    <div x-show="showServiceDropdown" x-transition
                        @click.away="showServiceDropdown = false"
                        class="mt-2 bg-white border border-gray-200 rounded-2xl shadow-lg p-3 z-10 relative">
                        <div class="mb-3">
                            <div class="relative">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <input type="text" x-model="serviceSearch"
                                    placeholder="Cari layanan..."
                                    class="w-full h-[38px] rounded-xl border border-gray-300 pl-9 pr-4 text-[14px] focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <button x-show="serviceSearch.trim()" type="button"
                                    @click="serviceSearch = ''"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="space-y-0.5 max-h-[200px] overflow-y-auto no-scrollbar">
                            <template x-if="services.length === 0">
                                <div class="py-8 text-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-gray-300 mx-auto mb-3"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                    </svg>
                                    <p class="text-[13px] font-semibold text-gray-500">Belum ada layanan</p>
                                    <p class="text-[12px] text-gray-400 mt-1">
                                        Klik "Tambah Layanan" di bawah untuk membuat layanan pertama.
                                    </p>
                                </div>
                            </template>
                            <template x-if="services.length > 0 && serviceSearch.trim() && filteredServices.length === 0">
                                <div class="py-8 text-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-gray-300 mx-auto mb-3"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                    <p class="text-[13px] font-semibold text-gray-500">Tidak ditemukan</p>
                                    <p class="text-[12px] text-gray-400 mt-1">
                                        Tidak ada layanan dengan kata kunci
                                        "<span class="font-medium text-gray-600" x-text="serviceSearch"></span>".
                                    </p>
                                </div>
                            </template>
                            <template x-if="filteredServices.length > 0">
                                <template x-for="service in filteredServices" :key="service.id">
                                    <div :class="selectedServiceId === service.id ? 'bg-blue-50 border border-blue-100' : 'hover:bg-gray-50 border border-transparent'"
                                        class="flex items-center justify-between px-2 py-3 rounded-xl transition">
                                        <button type="button" @click="selectService(service)" class="flex-1 text-left min-w-0">
                                            <div class="flex items-center gap-2">
                                                <template x-if="selectedServiceId === service.id">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        class="w-3.5 h-3.5 text-blue-600 shrink-0"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                </template>
                                                <span class="text-[14px] font-semibold text-gray-900 truncate"
                                                    x-text="service.name"></span>
                                            </div>
                                            <p class="text-[12px] text-gray-500 mt-0.5"
                                                x-text="formatCurrency(service.price)"></p>
                                        </button>
                                        <div class="flex items-center gap-2 ml-3 shrink-0">
                                            <button type="button" @click.stop="editService(service)"
                                                title="Edit layanan"
                                                class="w-7 h-7 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 flex items-center justify-center transition">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                            <button type="button" @click.stop="deleteService(service)"
                                                title="Hapus layanan"
                                                class="w-7 h-7 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 flex items-center justify-center transition">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </template>
                        </div>
                        <div class="mt-3 pt-3 border-t border-gray-100">
                            <button type="button" @click="openAddServiceModal()"
                                class="w-full h-[36px] rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-[14px] font-medium flex items-center justify-center gap-2 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"/>
                                </svg>
                                Tambah Layanan
                            </button>
                        </div>
                    </div>
                </div>
                <div class="mt-5">
                    <label class="inline-flex items-center gap-2 text-[14px] text-gray-700">
                        <input type="checkbox" x-model="multiDay"
                            class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                        Booking lebih dari 1 hari
                    </label>
                </div>
                <div class="mt-5">
                    <template x-if="!multiDay">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[14px] font-semibold text-gray-800 mb-2">
                                    Tanggal <span class="text-red-500">*</span>
                                </label>
                                <input type="date"
                                    x-model="bookingDate"
                                    required
                                    class="w-full h-[44px] rounded-2xl border border-gray-300 px-4 text-[14px]">
                            </div>
                            <div>
                                <label class="block text-[14px] font-semibold text-gray-800 mb-2">Waktu</label>
                                <input type="time" x-model="bookingTime"
                                    class="w-full h-[44px] rounded-2xl border border-gray-300 px-4 text-[14px]">
                                <p class="text-[11px] text-gray-500 mt-1">Opsional - kosongkan jika tidak diperlukan</p>
                            </div>
                        </div>
                    </template>
                    <template x-if="multiDay">
                        <div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-[14px] font-semibold text-gray-800 mb-2">
                                        Tanggal Mulai <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date"
                                        x-model="startDate"
                                        required
                                        class="w-full h-[44px] rounded-2xl border border-gray-300 px-4 text-[14px]">
                                </div>
                                <div>
                                    <label class="block text-[14px] font-semibold text-gray-800 mb-2">
                                        Tanggal Selesai <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date"
                                        x-model="endDate"
                                        required
                                        class="w-full h-[44px] rounded-2xl border border-gray-300 px-4 text-[14px]">
                                </div>
                                <div>
                                    <label class="block text-[14px] font-semibold text-gray-800 mb-2">Waktu</label>
                                    <input type="time" x-model="bookingTime"
                                        class="w-full h-[44px] rounded-2xl border border-gray-300 px-4 text-[14px]">
                                    <p class="text-[11px] text-gray-500 mt-1">Opsional</p>
                                </div>
                            </div>
                            <div x-show="totalDurasi > 0" x-transition
                                class="mt-4 bg-blue-50 border border-blue-100 rounded-2xl px-4 py-3">
                                <span class="text-[14px] font-semibold text-blue-600">
                                    Total durasi: <span x-text="totalDurasi + ' hari'"></span>
                                </span>
                            </div>
                        </div>
                    </template>
                </div>
                <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[14px] font-semibold text-gray-800 mb-2">Status</label>
                        <select x-model="status"
                            class="w-full h-[44px] rounded-2xl border border-gray-300 px-4 text-[14px]">
                            <option>Dijadwalkan</option>
                            <option>Selesai</option>
                            <option>Dibatalkan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[14px] font-semibold text-gray-800 mb-2">Status Pembayaran</label>
                        <input type="text" :value="paymentStatus" readonly :class="paymentStatusClass"
                            class="w-full h-[44px] rounded-2xl border px-4 text-[14px] font-medium cursor-default">
                        <p class="text-[11px] text-blue-600 mt-1">
                            Status pembayaran tervalidasi otomatis.
                        </p>
                    </div>
                </div>
                
                <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[14px] font-semibold text-gray-800 mb-2">Harga Paket (Rp)</label>
                        <input type="text"
                            :value="formatCurrency(unitPrice)"
                            readonly
                            class="w-full h-[44px] rounded-2xl border border-gray-200 px-4 text-[14px] bg-gray-50 text-gray-500 cursor-not-allowed outline-none focus:ring-0">
                    </div>
                    <div>
                        <label class="block text-[14px] font-semibold text-gray-800 mb-2">Sudah Dibayar (Rp)</label>
                        <input type="text"
                            :value="formatCurrency(paidAmount)"
                            @input="updatePaidAmount($event.target)"
                            @focus="showSummary = true; $event.target.select()"
                            class="w-full h-[44px] rounded-2xl border border-gray-300 px-4 text-[14px]">
                    </div>
                </div>
                
                <div class="mt-5">
                    <label class="block text-[14px] font-semibold text-gray-800 mb-2">Diskon (%)</label>
                    <div class="relative">
                        <input type="number" min="0" max="100"
                            x-model="discountValue"
                            @input="formatDiscount($event.target)"
                            @focus="showSummary = true; $event.target.select()"
                            class="w-full h-[44px] rounded-2xl border border-gray-300 pl-4 pr-12 text-[14px] focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 flex items-center text-gray-400 font-bold">
                            %
                        </div>
                    </div>
                </div>

                <div x-show="showSummary" x-transition class="mt-5 border border-gray-200 rounded-2xl p-4 text-[14px]">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="text-[11px] font-bold tracking-widest text-gray-400 uppercase">
                            Ringkasan Biaya
                        </span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-500">Subtotal:</span>
                        <span class="font-medium" x-text="formattedSubtotal"></span>
                    </div>
                    <template x-if="discountValue > 0">
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-500">Diskon (<span x-text="discountValue"></span>%):</span>
                            <span class="font-medium text-red-500" x-text="'- ' + formattedDiscountAmount"></span>
                        </div>
                    </template>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="font-semibold text-gray-800">Total Keseluruhan:</span>
                        <span class="font-bold text-blue-600" x-text="formattedGrandTotal"></span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-500">Sudah Dibayar:</span>
                        <span class="font-medium" x-text="formattedPaidAmount"></span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-100">
                        <span class="text-gray-500">Sisa Pembayaran:</span>
                        <span class="font-bold text-red-500" x-text="formattedRemaining"></span>
                    </div>
                    <div class="flex justify-between pt-2 items-center">
                        <span class="text-gray-500">Status Pembayaran:</span>
                        <span :class="paymentStatusClass"
                            class="px-3 py-1 rounded-xl border text-[12px] font-semibold"
                            x-text="paymentStatus"></span>
                    </div>
                </div>
                
                <div class="mt-5">
                    <label class="block text-[14px] font-semibold text-gray-800 mb-2">Catatan</label>
                    <textarea x-model="notes" rows="4" placeholder="Tambahkan catatan..."
                        class="w-full rounded-2xl border border-gray-300 px-4 py-3 text-[14px] resize-none"></textarea>
                </div>
                
                <div x-show="showServiceModal" x-cloak x-transition.opacity class="fixed inset-0 z-[70]">
                    <div @click="showServiceModal = false" class="absolute inset-0 bg-black/20"></div>
                    <div class="relative min-h-screen flex items-center justify-center p-4">
                        <div @click.stop class="bg-white w-full max-w-[560px] rounded-2xl shadow-2xl border border-gray-200">
                            <div class="px-5 pt-5 pb-3">
                                <h3 class="text-[24px] font-semibold text-gray-900"
                                    x-text="serviceModalMode === 'add' ? 'Tambah Layanan Baru' : 'Edit Layanan'"></h3>
                            </div>
                            <div class="px-5 pb-5">
                                <div x-show="Object.keys(serviceErrors).length > 0" x-cloak
                                    class="mb-4 bg-red-50 border border-red-200 rounded-xl px-4 py-3">
                                    <ul class="space-y-1">
                                        <template x-for="(msgs, field) in serviceErrors" :key="field">
                                            <template x-for="msg in msgs" :key="msg">
                                                <li class="text-[12px] text-red-600 flex items-center gap-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    <span x-text="msg"></span>
                                                </li>
                                            </template>
                                        </template>
                                    </ul>
                                </div>
                                <div>
                                    <label class="block text-[14px] font-semibold text-gray-800 mb-2">Nama Layanan <span class="text-red-500">*</span></label>
                                    <input type="text" x-model="serviceForm.name"
                                        :class="serviceErrors.name ? 'border-red-400 ring-1 ring-red-400' : 'border-gray-300'"
                                        placeholder="Masukkan nama layanan..."
                                        class="w-full h-[38px] rounded-xl border px-4 text-[14px] focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                <div class="mt-5">
                                    <label class="block text-[14px] font-semibold text-gray-800 mb-2">Deskripsi (opsional)</label>
                                    <textarea rows="3" x-model="serviceForm.description"
                                        placeholder="Masukkan deskripsi layanan..."
                                        class="w-full rounded-xl border border-gray-300 px-4 py-3 text-[14px] resize-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                                </div>
                                <div class="mt-5">
                                    <label class="block text-[14px] font-semibold text-gray-800 mb-2">Harga Default (opsional)</label>
                                    <input type="text" x-model="serviceForm.price"
                                        @input="formatRupiah($event.target)" @focus="$event.target.select()"
                                        class="w-full h-[38px] rounded-xl border border-gray-300 px-4 text-[14px] focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                                <div class="mt-6 flex items-center justify-end gap-3">
                                    <button type="button" @click="showServiceModal = false" :disabled="serviceSubmitting"
                                        class="h-[36px] px-5 rounded-xl bg-gray-200 hover:bg-gray-300 text-gray-800 text-[14px] font-medium transition disabled:opacity-50">
                                        Batal
                                    </button>
                                    <button type="button" @click="saveService()" :disabled="serviceSubmitting"
                                        class="h-[36px] px-6 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-[14px] font-medium flex items-center gap-2 transition disabled:opacity-60">
                                        <span x-text="serviceModalMode === 'add' ? 'Tambah Layanan' : 'Simpan Perubahan'"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            
            <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-end gap-3">
                <button @click="closeBookingModal()"
                    class="h-[40px] px-5 rounded-2xl bg-gray-100 text-gray-700 font-medium text-[14px] hover:bg-gray-200 transition">
                    Batal
                </button>
                <button type="button"
                    @click="document.getElementById('booking-form').dispatchEvent(new Event('submit', {bubbles: true, cancelable: true}))"
                    class="h-[40px] px-6 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-[14px] transition flex items-center gap-2">
                    <span x-text="document.getElementById('booking-form')?._x_dataStack?.[0]?.editingBookingId ? 'Simpan Perubahan' : 'Simpan Booking'"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function bookingForm() {
    return {
        _editMode: false, editingBookingId: null, showSummary: false,
        clientName: '', clientContact: '', clientAddress: '',
        bookingDate: '', startDate: '', endDate: '', bookingTime: '',
        multiDay: false, status: 'Dijadwalkan', notes: '',
        unitPrice: 0, paidAmount: 0,
        discountValue: 0, // Hanya angka persentase 0-100
        submitting: false, submitErrors: {}, clientErrors: {},
        services: [], selectedService: '', selectedServiceId: null,
        serviceSearch: '', showServiceDropdown: false,
        showServiceModal: false, serviceModalMode: 'add',
        serviceSubmitting: false, serviceErrors: {},
        serviceForm: { id: null, name: '', description: '', price: 'Rp 0' },
        
        init() { this.loadServices() },
        
        async loadServices() {
            try {
                const res = await fetch('/service-types', { headers: { 'Accept': 'application/json' } })
                const result = await res.json()
                this.services = result.data ?? []
                if (this.services.length > 0 && !this.editingBookingId) {
                    this.selectedService   = this.services[0].name
                    this.selectedServiceId = this.services[0].id
                    this.unitPrice         = parseInt(this.services[0].price) || 0
                }
            } catch { this.services = [] }
        },
        
        get subtotal()   { return this.unitPrice },
        get discountAmount() {
            // Karena diskon murni persen
            return Math.round(this.subtotal * (this.discountValue / 100));
        },
        get grandTotal() { return Math.max(this.subtotal - this.discountAmount, 0) },
        get remaining()  { return Math.max(this.grandTotal - this.paidAmount, 0) },
        get paymentStatus() {
            if (this.paidAmount <= 0)              return 'Belum Bayar'
            if (this.paidAmount >= this.grandTotal) return 'Lunas'
            return 'Down Payment'
        },
        get totalDurasi() {
            if (!this.startDate || !this.endDate) return 0
            const diff = new Date(this.endDate) - new Date(this.startDate)
            return diff < 0 ? 0 : Math.floor(diff / 86400000) + 1
        },
        get paymentStatusClass() {
            if (this.paymentStatus === 'Lunas')        return 'border-green-300 bg-green-50 text-green-700'
            if (this.paymentStatus === 'Down Payment') return 'border-blue-300 bg-blue-50 text-blue-700'
            return 'border-yellow-300 bg-yellow-50 text-yellow-700'
        },
        get filteredServices() {
            if (!this.serviceSearch.trim()) return this.services
            return this.services.filter(s => s.name.toLowerCase().includes(this.serviceSearch.toLowerCase()))
        },
        get formattedSubtotal()       { return this.formatCurrency(this.subtotal) },
        get formattedDiscountAmount() { return this.formatCurrency(this.discountAmount) },
        get formattedGrandTotal()     { return this.formatCurrency(this.grandTotal) },
        get formattedPaidAmount()     { return this.formatCurrency(this.paidAmount) },
        get formattedRemaining()      { return this.formatCurrency(this.remaining) },
        
        formatNumber(v)   { return new Intl.NumberFormat('id-ID').format(Math.round(v || 0)) },
        formatCurrency(v) { return 'Rp ' + this.formatNumber(v) },
        parseRupiah(v) {
            if (typeof v === 'number') return isNaN(v) ? 0 : v
            const c = String(v ?? '').replace(/[^0-9]/g, '')
            return c ? parseInt(c) : 0
        },
        formatRupiah(el) {
            const v = el.value.replace(/[^0-9]/g, '')
            el.value = v ? 'Rp ' + new Intl.NumberFormat('id-ID').format(parseInt(v)) : 'Rp 0'
        },
        updatePaidAmount(el) { 
            this.showSummary = true; 
            this.paidAmount = this.parseRupiah(el.value); 
            this.formatRupiah(el) 
        },
        toggleServiceDropdown() { this.showServiceDropdown = !this.showServiceDropdown },
        
        formatDiscount(el) {
            this.showSummary = true;
            let v = parseInt(el.value);
            if (isNaN(v) || v < 0) v = 0;
            if (v > 100) v = 100;
            this.discountValue = v;
        },

        selectService(service) {
            this.showSummary = true;
            this.selectedService = service.name; 
            this.selectedServiceId = service.id;
            this.unitPrice = parseInt(service.price) || 0; 
            this.showServiceDropdown = false;
        },
        openAddServiceModal() {
            this.showServiceDropdown = false; this.serviceModalMode = 'add'
            this.serviceForm = { id: null, name: '', description: '', price: 'Rp 0' }
            this.serviceErrors = {}; this.showServiceModal = true
        },
        editService(service) {
            this.showServiceDropdown = false; this.serviceModalMode = 'edit'; this.serviceErrors = {}
            this.serviceForm = { id: service.id, name: service.name, description: service.description || '',
                price: service.price ? 'Rp ' + new Intl.NumberFormat('id-ID').format(parseInt(service.price)) : 'Rp 0' }
            this.showServiceModal = true
        },
        async saveService() {
            this.serviceSubmitting = true; this.serviceErrors = {}
            const isEdit = this.serviceModalMode === 'edit'
            try {
                const res = await fetch(isEdit ? '/service-types/' + this.serviceForm.id : '/service-types', {
                    method: isEdit ? 'PUT' : 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                    body: JSON.stringify({ name: this.serviceForm.name, description: this.serviceForm.description, price: parseInt(String(this.serviceForm.price).replace(/[^0-9]/g, '') || '0') })
                })
                const result = await res.json()
                if (res.status === 422) { this.serviceErrors = result.errors ?? {}; return }
                if (!res.ok) { alert(result.message ?? 'Gagal'); return }
                if (isEdit) {
                    const idx = this.services.findIndex(s => s.id === this.serviceForm.id)
                    if (idx !== -1) this.services[idx] = result.data
                    if (this.selectedServiceId === result.data.id) { this.selectedService = result.data.name; this.unitPrice = parseInt(result.data.price) || 0 }
                } else {
                    this.services.unshift(result.data)
                    this.selectedService = result.data.name; this.selectedServiceId = result.data.id; this.unitPrice = parseInt(result.data.price) || 0
                }
                this.showServiceModal = false
            } catch { alert('Gagal terhubung ke server.') }
            finally  { this.serviceSubmitting = false }
        },
        async deleteService(service) {
            this.showServiceDropdown = false
            if (!confirm('Hapus layanan "' + service.name + '"?')) return
            try {
                const res = await fetch('/service-types/' + service.id, {
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
                })
                const result = await res.json()
                if (!res.ok) { alert(result.message ?? 'Gagal'); return }
                this.services = this.services.filter(s => s.id !== service.id)
                if (this.selectedServiceId === service.id) {
                    if (this.services.length > 0) { this.selectedService = this.services[0].name; this.selectedServiceId = this.services[0].id; this.unitPrice = parseInt(this.services[0].price) || 0 }
                    else { this.selectedService = ''; this.selectedServiceId = null; this.unitPrice = 0 }
                }
            } catch { alert('Gagal terhubung ke server.') }
        },
        resetToCreate() {
            Object.assign(this, {
                _editMode: false, editingBookingId: null, showSummary: false,
                clientName: '', clientContact: '', clientAddress: '',
                bookingDate: '', startDate: '', endDate: '', bookingTime: '',
                multiDay: false, status: 'Dijadwalkan', notes: '',
                unitPrice: 0, paidAmount: 0, discountValue: 0,
                submitErrors: {}, showServiceDropdown: false, serviceSearch: ''
            })
            if (this.services.length > 0) { this.selectedService = this.services[0].name; this.selectedServiceId = this.services[0].id; this.unitPrice = parseInt(this.services[0].price) || 0 }
            else { this.selectedService = ''; this.selectedServiceId = null; this.unitPrice = 0 }
            this.$nextTick(() => {
                const t = document.getElementById('booking-modal-title'); if (t) t.textContent = 'Tambah Booking Baru'
            })
        },
        
        openEditBooking(booking) {
            Object.assign(this, {
                editingBookingId: booking.id, 
                clientName: booking.client_name ?? '', 
                clientContact: booking.client_contact ?? '',
                clientAddress: booking.client_address ?? '', 
                bookingTime: booking.booking_time ? String(booking.booking_time).substring(0, 5) : '',
                status: booking.status ?? 'Dijadwalkan', 
                unitPrice: parseInt(booking.unit_price) || 0, 
                paidAmount: parseInt(booking.paid_amount) || 0,
                notes: booking.notes ?? '', 
                submitErrors: {}, 
                showServiceDropdown: false, 
                serviceSearch: '',
                showSummary: true 
            })

            if (booking.start_date) {
                this.multiDay = true; 
                this.startDate = String(booking.start_date).substring(0, 10)
                this.endDate = String(booking.end_date ?? '').substring(0, 10); 
                this.bookingDate = ''
            } else {
                this.multiDay = false; 
                this.bookingDate = String(booking.booking_date ?? '').substring(0, 10)
                this.startDate = ''; 
                this.endDate = ''
            }

            // Set Diskon Murni Persentase
            this.discountValue = parseFloat(booking.discount_percent) || 0;
            
            if (booking.service_type) { 
                this.selectedService = booking.service_type.name; 
                this.selectedServiceId = booking.service_type.id 
            } else if (booking.service_type_id) {
                const s = this.services.find(s => s.id == booking.service_type_id);
                if (s) {
                    this.selectedService = s.name;
                    this.selectedServiceId = s.id;
                }
            }
            
            this._editMode = true
            const t = document.getElementById('booking-modal-title'); 
            if (t) t.textContent = 'Edit Booking'
            
            window.dispatchEvent(new CustomEvent('force-open-modal'));
        },
        
        async submitBooking() {
            const form = document.getElementById('booking-form'); if (!form) return
            for (const f of form.querySelectorAll('input[required], select[required], textarea[required]')) {
                if (!f.checkValidity()) { f.focus(); f.reportValidity(); return }
            }
            this.submitting = true; this.submitErrors = {}
            const isEdit = this.editingBookingId !== null
            try {
                const res = await fetch(isEdit ? `/bookings/${this.editingBookingId}` : '/bookings', {
                    method: isEdit ? 'PUT' : 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                    body: JSON.stringify({
                        client_name: this.clientName, client_contact: this.clientContact, client_address: this.clientAddress,
                        service_type_id: this.selectedServiceId,
                        booking_date: !this.multiDay ? (this.bookingDate || null) : null,
                        start_date:    this.multiDay  ? (this.startDate   || null) : null,
                        end_date:      this.multiDay  ? (this.endDate     || null) : null,
                        booking_time: this.bookingTime || null, status: this.status,
                        unit_price: this.unitPrice,
                        discount_percent: this.discountValue,
                        paid_amount: this.paidAmount, notes: this.notes,
                    })
                })
                const result = await res.json()
                if (res.status === 422) { this.submitErrors = result.errors ?? {}; return }
                if (!res.ok) { Swal.fire({ icon:'error', title:'Gagal!', text: result.message ?? 'Terjadi kesalahan.', confirmButtonColor:'#2563eb', customClass:{popup:'rounded-[28px]'} }); return }
                
                window.dispatchEvent(new CustomEvent('force-close-modal'));

                this.resetToCreate()
                Swal.fire({ icon:'success', title: isEdit ? 'Diperbarui!' : 'Tersimpan!', text: result.message, confirmButtonColor:'#2563eb', timer:2000, timerProgressBar:true, showConfirmButton:false, customClass:{popup:'rounded-[28px]'} })
                    .then(() => window.dispatchEvent(new CustomEvent('reload-bookings')))
            } catch {
                Swal.fire({ icon:'error', title:'Gagal!', text:'Gagal terhubung ke server.', confirmButtonColor:'#2563eb', customClass:{popup:'rounded-[28px]'} })
            } finally { this.submitting = false }
        }
    }
}
</script>