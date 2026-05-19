<!-- resources/views/dashboard.blade.php -->
<x-app-layout>
    <div x-data="dashboardFilter()" @open-company-settings.window="openCompanySettingsModal()"
        @input.stop="markFilterInteraction($event)" @change.stop="markFilterInteraction($event)"
        class="px-4 sm:px-6 lg:px-7 py-7 bg-[#f5f7fb] min-h-screen overflow-x-hidden">
        <!-- STATUS BOOKING -->
        <div>
            <h2 class="text-[18px] font-semibold text-gray-800 mb-4">Status Booking</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                <template x-for="item in bookingCards" :key="item.key">
                    <button @click="setStatus(item.key)"
                        :class="status === item.key ? item.active : 'border-transparent'"
                        class="bg-white border-2 rounded-[22px] p-5 flex items-center justify-between shadow-sm">
                        <div class="text-left min-w-0">
                            <p class="text-gray-700 font-semibold text-[15px] truncate">
                                <span x-text="item.title"></span>
                            </p>
                            <h1 class="text-[28px] font-bold mt-2" :class="item.text" x-text="summary[item.key] ?? 0">
                            </h1>
                        </div>
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center shrink-0" :class="item.bg">
                            <i :data-lucide="item.icon" class="w-7 h-7" :class="item.text"></i>
                        </div>
                    </button>
                </template>
            </div>
        </div>
        <!-- STATUS PEMBAYARAN -->
        <div class="mt-8">
            <h2 class="text-[18px] font-semibold text-gray-800 mb-4">Status Pembayaran</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <template x-for="item in paymentCards" :key="item.key">
                    <button @click="setPayment(item.key)"
                        :class="payment === item.key ? item.active : 'border-transparent'"
                        class="bg-white border-2 rounded-[22px] p-5 flex items-center justify-between shadow-sm">
                        <div class="text-left min-w-0">
                            <p class="text-gray-700 font-semibold text-[15px] truncate">
                                <span x-text="item.title"></span>
                            </p>
                            <h1 class="text-[28px] font-bold mt-2" :class="item.text" x-text="summary[item.key] ?? 0">
                            </h1>
                        </div>
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center shrink-0" :class="item.bg">
                            <i :data-lucide="item.icon" class="w-7 h-7" :class="item.text"></i>
                        </div>
                    </button>
                </template>
            </div>
        </div>
        <!-- FILTER -->
        <div @click="markFilterInteractionFromClick($event)">
            <x-dashboard.filter-section />
        </div>
        <!-- TABLE VIEW -->
        <div x-show="viewMode === 'table'" x-transition class="mt-7">
            <x-dashboard.booking-table />
        </div>
        <!-- CALENDAR VIEW -->
        <div x-show="viewMode === 'calendar'" x-transition class="mt-7" x-cloak>
            <x-dashboard.booking-calendar />
        </div>
        <!-- BOOKING MODAL -->
        @include('components.dashboard.booking-modal')
        <!-- INVOICE MODAL -->
        <x-dashboard.invoice-modal />
        <!-- COMPANY SETTINGS MODAL -->
        <x-dashboard.company-settings-modal />
    </div>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        /* ============================================================
         * bookingForm()
         * ============================================================ */
        function bookingForm() {
            return {
                _editMode: false,
                multiDay: false,
                discountType: 'rupiah',
                startDate: '',
                endDate: '',
                clientName: '',
                clientContact: '',
                clientAddress: '',
                bookingDate: '',
                bookingTime: '',
                status: 'Dijadwalkan',
                notes: '',
                quantity: 1,
                unitPrice: 0,
                paidAmount: 0,
                discountValue: 0,
                showServiceDropdown: false,
                selectedService: '',
                selectedServiceId: null,
                serviceSearch: '',
                services: [],
                showServiceModal: false,
                serviceModalMode: 'add',
                serviceForm: { id: null, name: '', description: '', price: 'Rp 0' },
                serviceSubmitting: false,
                serviceErrors: {},
                submitting: false,
                submitErrors: {},
                clientErrors: {},
                editingBookingId: null,
                init() { this.loadServices() },
                async loadServices() {
                    try {
                        const res = await fetch('/service-types', { headers: { 'Accept': 'application/json' } })
                        const result = await res.json()
                        this.services = result.data ?? []
                        if (this.services.length > 0 && !this.editingBookingId) {
                            this.selectedService = this.services[0].name
                            this.selectedServiceId = this.services[0].id
                            this.unitPrice = parseInt(this.services[0].price) || 0
                        }
                    } catch (e) { this.services = [] }
                },
                formatDateForInput(dateStr) {
                    if (!dateStr) return ''
                    return String(dateStr).substring(0, 10)
                },
                openEditBooking(booking) {
                    this.editingBookingId = booking.id
                    this.clientName = booking.client_name ?? ''
                    this.clientContact = booking.client_contact ?? ''
                    this.clientAddress = booking.client_address ?? ''
                    if (booking.start_date) {
                        this.multiDay = true
                        this.startDate = this.formatDateForInput(booking.start_date)
                        this.endDate = this.formatDateForInput(booking.end_date)
                        this.bookingDate = ''
                    } else {
                        this.multiDay = false
                        this.bookingDate = this.formatDateForInput(booking.booking_date)
                        this.startDate = ''
                        this.endDate = ''
                    }
                    this.bookingTime = booking.booking_time
                        ? String(booking.booking_time).substring(0, 5) : ''
                    this.status = booking.status ?? 'Dijadwalkan'
                    this.quantity = booking.quantity ?? 1
                    this.unitPrice = parseInt(booking.unit_price) || 0
                    this.paidAmount = parseInt(booking.paid_amount) || 0
                    const discPct = parseFloat(booking.discount_percent) || 0
                    if (discPct > 0) {
                        this.discountType = 'percent'
                        this.discountValue = discPct
                        this.$nextTick(() => {
                            if (this.$refs.discountInput) this.$refs.discountInput.value = discPct
                        })
                    } else {
                        this.discountType = 'rupiah'
                        this.discountValue = 0
                        this.$nextTick(() => {
                            if (this.$refs.discountInput) this.$refs.discountInput.value = 'Rp 0'
                        })
                    }
                    if (booking.service_type) {
                        this.selectedService = booking.service_type.name
                        this.selectedServiceId = booking.service_type.id
                    }
                    this.notes = booking.notes ?? ''
                    this.submitErrors = {}
                    this.showServiceDropdown = false
                    this.serviceSearch = ''
                    const titleEl = document.getElementById('booking-modal-title')
                    if (titleEl) titleEl.textContent = 'Edit Booking'
                    this._editMode = true
                    const parentEl = document.querySelector('[x-data^="dashboardFilter"]')
                        ?? document.querySelector('[x-data]')
                    if (parentEl) {
                        const parent = Alpine.$data(parentEl)
                        if (parent && typeof parent.openBookingModal === 'function') {
                            parent.openBookingModal()
                        }
                    }
                },
                resetToCreate() {
                    this._editMode = false
                    this.editingBookingId = null
                    this.clientName = ''
                    this.clientContact = ''
                    this.clientAddress = ''
                    this.bookingDate = ''
                    this.startDate = ''
                    this.endDate = ''
                    this.bookingTime = ''
                    this.multiDay = false
                    this.status = 'Dijadwalkan'
                    this.quantity = 1
                    this.paidAmount = 0
                    this.discountValue = 0
                    this.discountType = 'rupiah'
                    this.notes = ''
                    this.submitErrors = {}
                    this.showServiceDropdown = false
                    this.serviceSearch = ''
                    if (this.services.length > 0) {
                        this.selectedService = this.services[0].name
                        this.selectedServiceId = this.services[0].id
                        this.unitPrice = parseInt(this.services[0].price) || 0
                    } else {
                        this.selectedService = ''
                        this.selectedServiceId = null
                        this.unitPrice = 0
                    }
                    this.$nextTick(() => {
                        if (this.$refs.discountInput) {
                            this.$refs.discountInput.value = 'Rp 0'
                        }
                        const titleEl = document.getElementById('booking-modal-title')
                        if (titleEl) titleEl.textContent = 'Tambah Booking Baru'
                    })
                },
                get totalDurasi() {
                    if (!this.startDate || !this.endDate) return 0
                    const start = new Date(this.startDate), end = new Date(this.endDate)
                    if (isNaN(start.getTime()) || isNaN(end.getTime())) return 0
                    const diffMs = end - start
                    if (diffMs < 0) return 0
                    return Math.floor(diffMs / (1000 * 60 * 60 * 24)) + 1
                },
                parseRupiah(value) {
                    if (value === null || value === undefined) return 0
                    if (typeof value === 'number') return isNaN(value) ? 0 : value
                    const cleaned = String(value).replace(/[^0-9]/g, '')
                    return cleaned === '' ? 0 : parseInt(cleaned)
                },
                formatNumber(value) { return new Intl.NumberFormat('id-ID').format(Math.round(value || 0)) },
                formatCurrency(value) { return 'Rp ' + this.formatNumber(value) },
                formatRupiah(el) {
                    const value = el.value.replace(/[^0-9]/g, '')
                    el.value = value === '' ? 'Rp 0' : 'Rp ' + new Intl.NumberFormat('id-ID').format(parseInt(value))
                },
                toggleServiceDropdown() { this.showServiceDropdown = !this.showServiceDropdown },
                setDiscountType(type) {
                    this.discountType = type; this.discountValue = 0
                    this.$refs.discountInput.value = type === 'rupiah' ? 'Rp 0' : '0'
                },
                formatDiscount(el) {
                    if (this.discountType === 'rupiah') {
                        this.formatRupiah(el); this.discountValue = this.parseRupiah(el.value)
                    } else {
                        let value = el.value.replace(/[^0-9]/g, '')
                        if (value === '') { el.value = '0'; this.discountValue = 0; return }
                        value = Math.min(parseInt(value), 100)
                        el.value = String(value); this.discountValue = value
                    }
                },
                updateUnitPrice(el) { this.unitPrice = this.parseRupiah(el.value); this.formatRupiah(el) },
                updatePaidAmount(el) { this.paidAmount = this.parseRupiah(el.value); this.formatRupiah(el) },
                updateQuantity(el) {
                    let value = parseInt(el.value)
                    if (isNaN(value) || value < 1) value = 1
                    this.quantity = value; el.value = value
                },
                get filteredServices() {
                    if (!this.serviceSearch.trim()) return this.services
                    return this.services.filter(s =>
                        s.name.toLowerCase().includes(this.serviceSearch.toLowerCase())
                    )
                },
                selectService(service) {
                    this.selectedService = service.name
                    this.selectedServiceId = service.id
                    this.unitPrice = parseInt(service.price) || 0
                    this.showServiceDropdown = false
                },
                openAddServiceModal() {
                    this.showServiceDropdown = false; this.serviceModalMode = 'add'
                    this.serviceForm = { id: null, name: '', description: '', price: 'Rp 0' }
                    this.serviceErrors = {}; this.showServiceModal = true
                },
                editService(service) {
                    this.showServiceDropdown = false; this.serviceModalMode = 'edit'; this.serviceErrors = {}
                    this.serviceForm = {
                        id: service.id, name: service.name, description: service.description || '',
                        price: service.price
                            ? 'Rp ' + new Intl.NumberFormat('id-ID').format(parseInt(service.price))
                            : 'Rp 0'
                    }
                    this.showServiceModal = true
                },
                async saveService() {
                    this.serviceSubmitting = true; this.serviceErrors = {}
                    const isEdit = this.serviceModalMode === 'edit'
                    const url = isEdit ? '/service-types/' + this.serviceForm.id : '/service-types'
                    const method = isEdit ? 'PUT' : 'POST'
                    try {
                        const res = await fetch(url, {
                            method,
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                            },
                            body: JSON.stringify({
                                name: this.serviceForm.name,
                                description: this.serviceForm.description,
                                price: parseInt(String(this.serviceForm.price).replace(/[^0-9]/g, '') || '0')
                            })
                        })
                        const result = await res.json()
                        if (res.status === 422) { this.serviceErrors = result.errors ?? {}; return }
                        if (!res.ok) { alert(result.message ?? 'Terjadi kesalahan.'); return }
                        if (isEdit) {
                            const idx = this.services.findIndex(s => s.id === this.serviceForm.id)
                            if (idx !== -1) this.services[idx] = result.data
                            if (this.selectedServiceId === result.data.id) {
                                this.selectedService = result.data.name
                                this.unitPrice = parseInt(result.data.price) || 0
                            }
                        } else {
                            this.services.unshift(result.data)
                            this.selectedService = result.data.name
                            this.selectedServiceId = result.data.id
                            this.unitPrice = parseInt(result.data.price) || 0
                        }
                        this.showServiceModal = false
                    } catch (err) { alert('Gagal terhubung ke server.') }
                    finally { this.serviceSubmitting = false }
                },
                async deleteService(service) {
                    this.showServiceDropdown = false
                    if (!confirm('Hapus layanan "' + service.name + '"?')) return
                    try {
                        const res = await fetch('/service-types/' + service.id, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                            }
                        })
                        const result = await res.json()
                        if (!res.ok) { alert(result.message ?? 'Gagal menghapus.'); return }
                        this.services = this.services.filter(s => s.id !== service.id)
                        if (this.selectedServiceId === service.id) {
                            if (this.services.length > 0) {
                                this.selectedService = this.services[0].name
                                this.selectedServiceId = this.services[0].id
                                this.unitPrice = parseInt(this.services[0].price) || 0
                            } else {
                                this.selectedService = ''; this.selectedServiceId = null; this.unitPrice = 0
                            }
                        }
                    } catch (err) { alert('Gagal terhubung ke server.') }
                },
                get subtotal() { return this.unitPrice * this.quantity },
                get discountAmount() {
                    if (this.discountType === 'percent')
                        return Math.round(this.subtotal * (this.discountValue / 100))
                    return Math.min(this.discountValue, this.subtotal)
                },
                get discountPercentForBackend() {
                    if (this.discountType === 'percent') return this.discountValue
                    if (this.subtotal <= 0) return 0
                    return (this.discountValue / this.subtotal) * 100
                },
                get grandTotal() { return Math.max(this.subtotal - this.discountAmount, 0) },
                get remaining() { return Math.max(this.grandTotal - this.paidAmount, 0) },
                get paymentStatus() {
                    if (this.paidAmount <= 0) return 'Belum Bayar'
                    if (this.paidAmount >= this.grandTotal) return 'Lunas'
                    return 'Down Payment'
                },
                get paymentStatusClass() {
                    switch (this.paymentStatus) {
                        case 'Lunas': return 'border-green-300 bg-green-50 text-green-700'
                        case 'Down Payment': return 'border-blue-300 bg-blue-50 text-blue-700'
                        default: return 'border-yellow-300 bg-yellow-50 text-yellow-700'
                    }
                },
                get formattedSubtotal() { return this.formatCurrency(this.subtotal) },
                get formattedDiscountAmount() { return this.formatCurrency(this.discountAmount) },
                get formattedGrandTotal() { return this.formatCurrency(this.grandTotal) },
                get formattedPaidAmount() { return this.formatCurrency(this.paidAmount) },
                get formattedRemaining() { return this.formatCurrency(this.remaining) },
                validateAndScroll() {
                    this.clientErrors = {}
                    const errors = {}
                    let firstFieldId = null
                    if (!this.clientName.trim()) {
                        errors.client_name = 'Nama klien wajib diisi.'
                        if (!firstFieldId) firstFieldId = 'field-client-name'
                    }
                    if (!this.clientContact.trim()) {
                        errors.client_contact = 'Kontak klien wajib diisi.'
                        if (!firstFieldId) firstFieldId = 'field-client-contact'
                    }
                    if (!this.clientAddress.trim()) {
                        errors.client_address = 'Alamat klien wajib diisi.'
                        if (!firstFieldId) firstFieldId = 'field-client-address'
                    }
                    if (!this.selectedServiceId) {
                        errors.service_type_id = 'Jenis layanan wajib dipilih.'
                        if (!firstFieldId) firstFieldId = 'field-service-type'
                    }
                    if (!this.multiDay && !this.bookingDate) {
                        errors.booking_date = 'Tanggal wajib diisi.'
                        if (!firstFieldId) firstFieldId = 'field-booking-date'
                    }
                    if (this.multiDay && !this.startDate) {
                        errors.start_date = 'Tanggal mulai wajib diisi.'
                        if (!firstFieldId) firstFieldId = 'field-start-date'
                    }
                    if (this.multiDay && !this.endDate) {
                        errors.end_date = 'Tanggal selesai wajib diisi.'
                        if (!firstFieldId) firstFieldId = 'field-end-date'
                    }
                    this.clientErrors = errors
                    if (firstFieldId) {
                        this.$nextTick(() => {
                            const el = document.getElementById(firstFieldId)
                            if (el) {
                                el.scrollIntoView({ behavior: 'smooth', block: 'center' })
                                if (el.tagName !== 'BUTTON') el.focus()
                            }
                        })
                        return false
                    }
                    return true
                },

                // ── submitBooking — native browser validation tanpa delay ──
                async submitBooking() {
                    const form = document.getElementById('booking-form')
                    if (!form) return

                    // Cari field required pertama yang invalid
                    const fields = form.querySelectorAll('input[required], select[required], textarea[required]')
                    let firstInvalid = null
                    for (const field of fields) {
                        if (!field.checkValidity()) {
                            firstInvalid = field
                            break
                        }
                    }

                    if (firstInvalid) {
                        // Langsung focus + reportValidity tanpa delay
                        // agar popup browser muncul instan
                        firstInvalid.focus()
                        firstInvalid.reportValidity()
                        return
                    }

                    // ── Lanjut submit ke backend ───────────────────────
                    this.submitting = true
                    this.submitErrors = {}
                    const isEdit = this.editingBookingId !== null
                    const url    = isEdit ? `/bookings/${this.editingBookingId}` : `/bookings`
                    const method = isEdit ? 'PUT' : 'POST'
                    const payload = {
                        client_name:      this.clientName,
                        client_contact:   this.clientContact,
                        client_address:   this.clientAddress,
                        service_type_id:  this.selectedServiceId,
                        booking_date:     !this.multiDay ? (this.bookingDate || null) : null,
                        start_date:       this.multiDay ? (this.startDate || null) : null,
                        end_date:         this.multiDay ? (this.endDate || null) : null,
                        booking_time:     this.bookingTime || null,
                        status:           this.status,
                        quantity:         this.quantity,
                        unit_price:       this.unitPrice,
                        discount_percent: this.discountPercentForBackend,
                        paid_amount:      this.paidAmount,
                        notes:            this.notes,
                    }
                    try {
                        const res = await fetch(url, {
                            method,
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept':       'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                            },
                            body: JSON.stringify(payload)
                        })
                        const result = await res.json()
                        if (res.status === 422) { this.submitErrors = result.errors ?? {}; return }
                        if (!res.ok) {
                            Swal.fire({
                                icon: 'error', title: 'Gagal!',
                                text: result.message ?? 'Terjadi kesalahan.',
                                confirmButtonColor: '#2563eb',
                                customClass: { popup: 'rounded-[28px]' }
                            })
                            return
                        }
                        const parentEl = document.querySelector('[x-data^="dashboardFilter"]')
                            ?? document.querySelector('[x-data]')
                        if (parentEl) {
                            const parent = Alpine.$data(parentEl)
                            if (parent && typeof parent.closeBookingModal === 'function') {
                                parent.closeBookingModal()
                            }
                        }
                        this.resetToCreate()
                        Swal.fire({
                            icon:             'success',
                            title:            isEdit ? 'Diperbarui!' : 'Tersimpan!',
                            text:             result.message,
                            confirmButtonColor: '#2563eb',
                            timer:            2000,
                            timerProgressBar: true,
                            showConfirmButton: false,
                            customClass:      { popup: 'rounded-[28px]' }
                        }).then(() => {
                            window.dispatchEvent(new CustomEvent('reload-bookings'))
                        })
                    } catch (err) {
                        Swal.fire({
                            icon: 'error', title: 'Gagal!',
                            text: 'Gagal terhubung ke server.',
                            confirmButtonColor: '#2563eb',
                            customClass: { popup: 'rounded-[28px]' }
                        })
                    } finally {
                        this.submitting = false
                    }
                }
            }
        }
        /* ============================================================
         * dashboardFilter()
         * ============================================================ */
        function dashboardFilter() {
            return {
                viewMode: 'table',
                status: 'semua',
                payment: 'semua',
                searchQuery: '',
                filterMonth: '',
                sortBy: 'newest',
                filterInteracted: false,
                bookings: [],
                summary: {
                    semua: 0,
                    dijadwalkan: 0,
                    selesai: 0,
                    dibatalkan: 0,
                    belum_bayar: 0,
                    dp: 0,
                    lunas: 0,
                },
                showBookingModal: false,
                openBookingModal() {
                    this.showBookingModal = true
                    document.body.classList.add('overflow-hidden')
                    this.$nextTick(() => {
                        if (window.lucide) lucide.createIcons()
                        const formEl = document.getElementById('booking-form')
                        if (formEl?._x_dataStack?.[0]) {
                            const fd = formEl._x_dataStack[0]
                            if (fd._editMode) {
                                fd._editMode = false
                                return
                            }
                            if (typeof fd.resetToCreate === 'function') {
                                fd.resetToCreate()
                            }
                        }
                    })
                },
                closeBookingModal() {
                    this.showBookingModal = false
                    document.body.classList.remove('overflow-hidden')
                    this.$nextTick(() => {
                        const formEl = document.getElementById('booking-form')
                        if (formEl?._x_dataStack?.[0]) {
                            const fd = formEl._x_dataStack[0]
                            if (typeof fd.resetToCreate === 'function') {
                                fd.resetToCreate()
                            }
                        }
                    })
                },
                showCompanySettingsModal: false,
                openCompanySettingsModal() {
                    this.showCompanySettingsModal = true
                    document.body.classList.add('overflow-hidden')
                    this.$nextTick(() => {
                        if (window.lucide) lucide.createIcons()
                        window.dispatchEvent(new CustomEvent('reload-company-settings'))
                    })
                },
                closeCompanySettingsModal() {
                    this.showCompanySettingsModal = false
                    document.body.classList.remove('overflow-hidden')
                },
                days: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                currentDate: new Date(),
                bookingCards: [
                    { key: 'semua', title: 'Total Booking', icon: 'users', text: 'text-blue-600', bg: 'bg-blue-100', active: 'border-blue-500' },
                    { key: 'dijadwalkan', title: 'Dijadwalkan', icon: 'calendar-days', text: 'text-blue-600', bg: 'bg-blue-100', active: 'border-blue-500' },
                    { key: 'selesai', title: 'Selesai', icon: 'check-circle-2', text: 'text-green-600', bg: 'bg-green-100', active: 'border-green-500' },
                    { key: 'dibatalkan', title: 'Dibatalkan', icon: 'x-circle', text: 'text-red-600', bg: 'bg-red-100', active: 'border-red-500' }
                ],
                paymentCards: [
                    { key: 'belum_bayar', title: 'Belum Bayar', icon: 'alert-circle', text: 'text-yellow-500', bg: 'bg-yellow-100', active: 'border-yellow-400' },
                    { key: 'dp', title: 'DP', icon: 'credit-card', text: 'text-orange-500', bg: 'bg-orange-100', active: 'border-orange-500' },
                    { key: 'lunas', title: 'Lunas', icon: 'badge-dollar-sign', text: 'text-green-600', bg: 'bg-green-100', active: 'border-green-500' }
                ],
                statusButtons: [
                    { key: 'semua', title: 'Semua' },
                    { key: 'dijadwalkan', title: 'Dijadwalkan' },
                    { key: 'selesai', title: 'Selesai' },
                    { key: 'dibatalkan', title: 'Dibatalkan' }
                ],
                paymentButtons: [
                    { key: 'semua', title: 'Semua' },
                    { key: 'belum_bayar', title: 'Belum Bayar' },
                    { key: 'dp', title: 'DP' },
                    { key: 'lunas', title: 'Lunas' }
                ],
                init() {
                    this.$nextTick(() => { if (window.lucide) lucide.createIcons() })
                    this.loadSummary()
                    this.$watch('status', () => this.dispatchFilter())
                    this.$watch('payment', () => this.dispatchFilter())
                    this.$watch('searchQuery', () => this.dispatchFilter())
                    this.$watch('filterMonth', () => this.dispatchFilter())
                    this.$watch('sortBy', () => this.dispatchFilter())
                    window.addEventListener('reload-bookings', () => this.loadSummary())
                },
                async loadSummary() {
                    try {
                        const res = await fetch('/bookings', { headers: { 'Accept': 'application/json' } })
                        const result = await res.json()
                        if (result.summary) {
                            this.summary = {
                                semua: result.summary.total ?? 0,
                                dijadwalkan: result.summary.dijadwalkan ?? 0,
                                selesai: result.summary.selesai ?? 0,
                                dibatalkan: result.summary.dibatalkan ?? 0,
                                belum_bayar: result.summary.belum_bayar ?? 0,
                                dp: result.summary.dp ?? 0,
                                lunas: result.summary.lunas ?? 0,
                            }
                        }
                    } catch (e) { }
                },
                dispatchFilter() {
                    window.dispatchEvent(new CustomEvent('filter-changed', {
                        detail: {
                            status: this.status,
                            payment: this.payment,
                            search: this.searchQuery,
                            month: this.filterMonth,
                            sortBy: this.sortBy,
                        }
                    }))
                },
                get isEmpty() {
                    if (!this.filterInteracted) return false
                    return !this.bookings || this.bookings.length === 0
                },
                get calendarTitle() {
                    return `Kalender ${this.monthNames[this.currentDate.getMonth()]} ${this.currentDate.getFullYear()}`
                },
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
                setStatus(value) { this.status = value; this.filterInteracted = true },
                setPayment(value) { this.payment = value; this.filterInteracted = true },
                markFilterInteraction(e) {
                    if (!e || !e.target) return
                    const tag = (e.target.tagName || '').toUpperCase()
                    if (tag === 'INPUT' || tag === 'SELECT' || tag === 'TEXTAREA') this.filterInteracted = true
                },
                markFilterInteractionFromClick(e) {
                    if (!e || !e.target) return
                    const btn = e.target.closest('button, [role="button"]')
                    if (!btn || btn.hasAttribute('data-no-filter')) return
                    this.filterInteracted = true
                },
                prevMonth() {
                    this.currentDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() - 1, 1)
                    this.$nextTick(() => { if (window.lucide) lucide.createIcons() })
                },
                nextMonth() {
                    this.currentDate = new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() + 1, 1)
                    this.$nextTick(() => { if (window.lucide) lucide.createIcons() })
                }
            }
        }
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) lucide.createIcons()
        })
    </script>
    <style>
        [x-cloak] {
            display: none !important;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
    </style>
</x-app-layout>