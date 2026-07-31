<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-6 sm:py-8 bg-[#f5f7fb] min-h-screen relative" x-data="bookingForm(@js($booking ?? null), @js($serviceTypes ?? []))">
        
        <div class="w-full max-w-3xl mx-auto">
            
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-gray-500 hover:text-blue-600 text-[14px] font-semibold transition mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke Dashboard
            </a>

            <div class="bg-white border border-gray-200 rounded-[24px] shadow-sm p-6 sm:p-8 relative z-10">
                
                <div class="flex items-center justify-between border-b border-gray-100 pb-5 mb-6">
                    <h2 class="text-[24px] font-bold text-gray-900" x-text="_editMode ? 'Edit Booking'"></h2>
                </div>

                <form id="booking-form" @submit.prevent="submitBooking()" class="space-y-6">
                    
                    <div x-show="Object.keys(submitErrors).length > 0" x-cloak class="bg-red-50 border border-red-200 rounded-2xl px-4 py-3">
                        <p class="text-[13px] font-semibold text-red-600 mb-1">Mohon periksa kembali:</p>
                        <ul class="space-y-1">
                            <template x-for="(msgs, field) in submitErrors" :key="field">
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
                        <label class="block text-[14px] font-medium text-gray-700 mb-2">Nama Klien <span class="text-red-500">*</span></label>
                        <input type="text" x-model="clientName" required placeholder="Masukkan nama klien"
                            class="w-full h-[48px] rounded-xl border border-gray-300 px-4 text-[14px] focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none shadow-sm">
                    </div>
                    <div>
                        <label class="block text-[14px] font-medium text-gray-700 mb-2">Kontak Klien <span class="text-red-500">*</span></label>
                        <input type="text" x-model="clientContact" required placeholder="Nomor telepon atau email"
                            class="w-full h-[48px] rounded-xl border border-gray-300 px-4 text-[14px] focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none shadow-sm">
                    </div>
                    <div>
                        <label class="block text-[14px] font-medium text-gray-700 mb-2">Alamat Klien <span class="text-red-500">*</span></label>
                        <input type="text" x-model="clientAddress" required placeholder="Alamat klien"
                            class="w-full h-[48px] rounded-xl border border-gray-300 px-4 text-[14px] focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 outline-none shadow-sm">
                    </div>

                    <div class="relative z-50">
                        <label class="block text-[14px] font-medium text-gray-700 mb-2">Paket Layanan <span class="text-red-500">*</span></label>
                        <input type="text" :value="selectedService" required tabindex="-1" class="absolute opacity-0 w-0 h-0 pointer-events-none">
                        
                        <button type="button" @click="showServiceDropdown = true; showSummary = true;"
                            class="w-full h-[48px] rounded-xl border border-gray-300 px-4 text-left text-[14px] flex items-center justify-between bg-white focus:ring-2 focus:ring-blue-500/20 shadow-sm outline-none">
                            <span :class="selectedService ? 'text-gray-900 font-medium' : 'text-gray-400'" x-text="selectedService || 'Pilih paket layanan'"></span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="showServiceDropdown ? 'rotate-180' : 'rotate-0'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        
                        <div x-show="showServiceDropdown" x-transition @click.away="showServiceDropdown = false" class="absolute left-0 right-0 mt-2 bg-white border border-gray-200 rounded-2xl shadow-xl p-3 z-50">
                            <div class="space-y-1 max-h-[250px] overflow-y-auto no-scrollbar">
                                <template x-if="services.length === 0">
                                    <div class="py-6 text-center text-[13px] text-gray-500">Belum ada layanan tersedia. Silakan kelola di menu kelola layanan.</div>
                                </template>
                                
                                <template x-if="services.length > 0">
                                    <template x-for="service in services" :key="service.id">
                                        <div @click="selectService(service)" :class="selectedServiceId === service.id ? 'bg-blue-50 border-blue-100' : 'hover:bg-gray-50 border-transparent'" class="flex items-start px-2 py-3 rounded-xl border transition cursor-pointer">
                                            <div class="pt-0.5 w-6 shrink-0 flex justify-center">
                                                <template x-if="selectedServiceId === service.id">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                </template>
                                            </div>
                                            <div class="flex-1 min-w-0 pr-2">
                                                <p class="text-[14px] font-semibold text-gray-900 truncate" x-text="service.name"></p>
                                                <p class="text-[12px] text-gray-500 mt-1 whitespace-pre-line leading-snug" x-text="service.description"></p>
                                                <p class="text-[13px] font-bold text-blue-600 mt-2" x-text="formatCurrency(service.price)"></p>
                                            </div>
                                        </div>
                                    </template>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="relative z-0">
                        <label class="inline-flex items-center gap-2 text-[14px] text-gray-700 mb-4 cursor-pointer">
                            <input type="checkbox" x-model="multiDay" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-4 h-4 transition">
                            Booking lebih dari 1 hari
                        </label>
                        
                        <div x-show="!multiDay" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[14px] font-medium text-gray-700 mb-2">Tanggal <span class="text-red-500">*</span></label>
                                <input type="date" x-model="bookingDate" :disabled="multiDay" :required="!multiDay" class="w-full h-[48px] rounded-xl border border-gray-300 px-4 text-[14px] outline-none shadow-sm focus:ring-2 focus:ring-blue-500/20">
                            </div>
                            <div>
                                <label class="block text-[14px] font-medium text-gray-700 mb-2">Waktu <span class="text-gray-400 font-normal">(Opsional)</span></label>
                                <input type="time" x-model="bookingTime" :disabled="multiDay" class="w-full h-[48px] rounded-xl border border-gray-300 px-4 text-[14px] outline-none shadow-sm focus:ring-2 focus:ring-blue-500/20">
                            </div>
                        </div>
                        
                        <div x-show="multiDay" style="display: none;" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-[14px] font-medium text-gray-700 mb-2">Mulai <span class="text-red-500">*</span></label>
                                <input type="date" x-model="startDate" :disabled="!multiDay" :required="multiDay" class="w-full h-[48px] rounded-xl border border-gray-300 px-4 text-[14px] outline-none shadow-sm focus:ring-2 focus:ring-blue-500/20">
                            </div>
                            <div>
                                <label class="block text-[14px] font-medium text-gray-700 mb-2">Selesai <span class="text-red-500">*</span></label>
                                <input type="date" x-model="endDate" :disabled="!multiDay" :required="multiDay" class="w-full h-[48px] rounded-xl border border-gray-300 px-4 text-[14px] outline-none shadow-sm focus:ring-2 focus:ring-blue-500/20">
                            </div>
                            <div>
                                <label class="block text-[14px] font-medium text-gray-700 mb-2">Waktu <span class="text-gray-400 font-normal">(Opsional)</span></label>
                                <input type="time" x-model="bookingTime" :disabled="!multiDay" class="w-full h-[48px] rounded-xl border border-gray-300 px-4 text-[14px] outline-none shadow-sm focus:ring-2 focus:ring-blue-500/20">
                            </div>
                        </div>

                        <div x-show="totalDurasi > 0" x-transition class="mt-4 bg-blue-50 border border-blue-100 rounded-xl px-4 py-3 shadow-sm">
                            <span class="text-[13px] font-semibold text-blue-600">Total durasi: <span x-text="totalDurasi + ' hari'"></span></span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border-t border-gray-100 pt-6">
                        <div>
                            <label class="block text-[14px] font-medium text-gray-700 mb-2">Status Jadwal</label>
                            <select x-model="status" class="w-full h-[48px] rounded-xl border border-gray-300 px-4 text-[14px] outline-none shadow-sm focus:ring-2 focus:ring-blue-500/20">
                                <option value="Dijadwalkan">Dijadwalkan</option>
                                <option value="Pembayaran Tertunda">Pembayaran Tertunda</option>
                                <option value="Proses Edit">Proses Edit</option>
                                <option value="Selesai">Selesai</option>
                                <option value="Dibatalkan">Dibatalkan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[14px] font-medium text-gray-700 mb-2">Status Pembayaran</label>
                            <input type="text" :value="paymentStatus" readonly :class="paymentStatusClass"
                                class="w-full h-[48px] rounded-xl border px-4 text-[14px] font-medium cursor-default shadow-sm outline-none">
                            
                            {{-- TAMPILKAN TOMBOL LIHAT BUKTI BAYAR JIKA ADA --}}
                            <template x-if="paymentProofUrl">
                                <div class="mt-2 bg-blue-50 border border-blue-200 rounded-xl p-3 flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                        <p class="text-xs font-bold text-gray-900">Bukti Transfer Ada</p>
                                    </div>
                                    <a :href="paymentProofUrl" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-bold px-3 py-1.5 rounded-lg shadow-sm transition">
                                        Lihat Foto
                                    </a>
                                </div>
                            </template>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[14px] font-medium text-gray-700 mb-2">Harga Paket (Rp)</label>
                            <input type="text" :value="formatCurrency(unitPrice)" readonly
                                class="w-full h-[48px] rounded-xl border border-gray-200 px-4 text-[14px] bg-gray-50 text-gray-500 cursor-not-allowed outline-none">
                        </div>
                        <div>
                            <label class="block text-[14px] font-medium text-gray-700 mb-2">Sudah Dibayar (Rp)</label>
                            <input type="text" :value="formatCurrency(paidAmount)" @input="updatePaidAmount($event.target)" @focus="showSummary = true; $event.target.select()"
                                class="w-full h-[48px] rounded-xl border border-gray-300 px-4 text-[14px] outline-none shadow-sm focus:ring-2 focus:ring-blue-500/20">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-[14px] font-medium text-gray-700 mb-2">Diskon (%)</label>
                        <div class="relative">
                            <input type="number" min="0" max="100" x-model="discountValue" @input="formatDiscount($event.target)" @focus="showSummary = true; $event.target.select()"
                                class="w-full h-[48px] rounded-xl border border-gray-300 pl-4 pr-12 text-[14px] outline-none shadow-sm focus:ring-2 focus:ring-blue-500/20">
                            <div class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold">%</div>
                        </div>
                    </div>

                    <div x-show="showSummary" x-transition class="bg-gray-50 border border-gray-200 rounded-2xl p-5 text-[14px]">
                        <p class="text-[12px] font-bold text-gray-400 uppercase mb-4 tracking-wider">Ringkasan Biaya</p>
                        
                        <div class="flex justify-between py-2 border-b border-gray-200">
                            <span class="text-gray-500 font-medium">Subtotal:</span>
                            <span class="font-bold text-gray-800" x-text="formattedSubtotal"></span>
                        </div>
                        
                        <template x-if="discountValue > 0">
                            <div class="flex justify-between py-2 border-b border-gray-200">
                                <span class="text-gray-500 font-medium">Diskon (<span x-text="discountValue"></span>%):</span>
                                <span class="font-bold text-red-500" x-text="'- ' + formattedDiscountAmount"></span>
                            </div>
                        </template>
                        
                        <div class="flex justify-between py-2 border-b border-gray-200">
                            <span class="text-gray-700 font-semibold">Total Keseluruhan:</span>
                            <span class="font-bold text-gray-900" x-text="formattedGrandTotal"></span>
                        </div>
                        
                        <div class="flex justify-between py-2 border-b border-gray-200">
                            <span class="text-gray-500 font-medium">Sudah Dibayar:</span>
                            <span class="font-bold text-emerald-600" x-text="formattedPaidAmount"></span>
                        </div>
                        <div class="flex justify-between py-2">
                            <span class="text-gray-500 font-medium">Sisa Pembayaran:</span>
                            <span class="font-bold text-red-500" x-text="formattedRemaining"></span>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-[14px] font-medium text-gray-700 mb-2">Catatan</label>
                        <textarea x-model="notes" rows="3" placeholder="Tambahkan catatan..."
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 text-[14px] resize-none outline-none shadow-sm focus:ring-2 focus:ring-blue-500/20"></textarea>
                    </div>

                    <div class="pt-6 border-t border-gray-100 flex flex-col-reverse sm:flex-row justify-end gap-3">
                        <a href="{{ route('dashboard') }}" class="h-[48px] px-6 rounded-xl border border-gray-300 text-gray-600 font-semibold text-[15px] flex items-center justify-center hover:bg-gray-50 transition-colors">
                            Batal
                        </a>
                        <button type="submit" :disabled="submitting" class="h-[48px] px-8 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-[15px] shadow-lg shadow-blue-600/20 flex items-center justify-center gap-2 transition-all disabled:opacity-60">
                            <span x-text="submitting ? 'Menyimpan...' : (_editMode ? 'Simpan Perubahan' : 'Simpan Booking')"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    function bookingForm(initialBooking, rawServices) {
        return {
            _editMode: false, editingBookingId: null, showSummary: false,
            clientName: '', clientContact: '', clientAddress: '',
            bookingDate: '', startDate: '', endDate: '', bookingTime: '',
            multiDay: false, status: 'Dijadwalkan', notes: '',
            unitPrice: 0, paidAmount: 0, discountValue: 0,
            submitting: false, submitErrors: {}, 
            
            services: rawServices || [], 
            selectedService: '', selectedServiceId: null,
            showServiceDropdown: false,

            paymentProofUrl: null, // Tambahan untuk link foto bukti
            
            init() { 
                if (!initialBooking && this.services.length > 0) {
                    this.selectedService   = this.services[0].name
                    this.selectedServiceId = this.services[0].id
                    this.unitPrice         = parseInt(this.services[0].price) || 0
                }

                if(initialBooking) {
                    this.openEditBooking(initialBooking);
                }
            },
            
            get subtotal()   { return this.unitPrice },
            get discountAmount() { return Math.round(this.subtotal * (this.discountValue / 100)); },
            get grandTotal() { return Math.max(this.subtotal - this.discountAmount, 0) },
            get remaining()  { return Math.max(this.grandTotal - this.paidAmount, 0) },
            get paymentStatus() {
                if (this.paidAmount <= 0) return 'Pending'
                if (this.paidAmount >= this.grandTotal) return 'Lunas'
                return 'Down Payment'
            },
            get paymentStatusClass() {
                if (this.paymentStatus === 'Lunas') return 'border-green-300 bg-green-50 text-green-700'
                if (this.paymentStatus === 'Down Payment') return 'border-blue-300 bg-blue-50 text-blue-700'
                return 'border-yellow-300 bg-yellow-50 text-yellow-700'
            },
            get totalDurasi() {
                if (!this.startDate || !this.endDate) return 0
                const diff = new Date(this.endDate) - new Date(this.startDate)
                return diff < 0 ? 0 : Math.floor(diff / 86400000) + 1
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
                    showSummary: true,
                    paymentProofUrl: booking.payment_proof ? '/storage/' + booking.payment_proof : null
                })

                if (booking.start_date) {
                    const sDate = String(booking.start_date).substring(0, 10);
                    const eDate = String(booking.end_date ?? '').substring(0, 10);
                    
                    if (sDate === eDate) {
                        this.multiDay = false;
                        this.bookingDate = sDate;
                    } else {
                        this.multiDay = true; 
                        this.startDate = sDate;
                        this.endDate = eDate; 
                    }
                } else {
                    this.multiDay = false; 
                    this.bookingDate = String(booking.booking_date ?? '').substring(0, 10);
                }

                this.discountValue = parseFloat(booking.discount_percent) || 0;
                
                if (booking.service_type_id) {
                    const s = this.services.find(s => s.id == booking.service_type_id);
                    if (s) {
                        this.selectedService = s.name;
                        this.selectedServiceId = s.id;
                    }
                }
                this._editMode = true
            },
            
            async submitBooking() {
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
                            unit_price: this.unitPrice, discount_percent: this.discountValue,
                            paid_amount: this.paidAmount, notes: this.notes,
                        })
                    })
                    const result = await res.json()
                    if (res.status === 422) { this.submitErrors = result.errors ?? {}; return }
                    if (!res.ok) throw new Error(result.message)
                    
                    window.location.href = "{{ route('dashboard') }}"
                } catch {
                    Swal.fire({ icon:'error', title:'Gagal!', text:'Gagal terhubung ke server.', confirmButtonColor:'#2563eb' })
                } finally { this.submitting = false }
            }
        }
    }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</x-app-layout>