{{-- resources/views/components/dashboard/company-settings-modal.blade.php --}}
<div
    x-show="showCompanySettingsModal"
    x-cloak
    x-transition.opacity
    class="fixed inset-0 z-50">

    <!-- BACKDROP -->
    <div
        @click="closeCompanySettingsModal()"
        class="absolute inset-0 bg-black/35 backdrop-blur-sm"></div>

    <!-- WRAPPER -->
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <!-- MODAL -->
        <div
            @click.stop
            x-data="companySettingsForm()"
            x-init="loadSettings()"
            @reload-company-settings.window="loadSettings()"
            class="bg-white w-full max-w-[780px] rounded-[22px] shadow-2xl overflow-hidden">

            <!-- HEADER -->
            <div class="px-6 py-5 border-b border-gray-200 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <h2 class="text-[20px] font-bold text-gray-900">Pengaturan Perusahaan</h2>
                </div>
                <button type="button" @click="closeCompanySettingsModal()"
                    class="text-gray-500 hover:text-gray-700 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- LOADING STATE -->
            <div x-show="loading" class="px-6 py-16 flex items-center justify-center">
                <svg class="animate-spin w-8 h-8 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
                </svg>
            </div>

            <!-- BODY -->
            <div x-show="!loading" class="max-h-[75vh] overflow-y-auto px-6 py-6">

                <!-- ERROR GLOBAL -->
                <div x-show="Object.keys(errors).length > 0" x-cloak
                    class="mb-5 bg-red-50 border border-red-200 rounded-2xl px-4 py-3">
                    <p class="text-[13px] font-semibold text-red-600 mb-1">Mohon periksa kembali:</p>
                    <ul class="space-y-1">
                        <template x-for="(msgs, field) in errors" :key="field">
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

                <!-- FORM -->
                <form @submit.prevent="saveSettings()" enctype="multipart/form-data" id="company-settings-form">

                    <!-- ══ INFORMASI PERUSAHAAN ══ -->
                    <div class="flex items-center gap-2 mb-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <h3 class="text-[18px] font-bold text-gray-800">Informasi Perusahaan</h3>
                    </div>

                    <!-- NAMA PERUSAHAAN -->
                    <div class="mt-4">
                        <label class="block text-[14px] font-semibold text-gray-800 mb-2">
                            Nama Perusahaan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" x-model="form.company_name"
                            :class="errors.company_name ? 'border-red-400 ring-1 ring-red-400' : 'border-gray-300'"
                            placeholder="Nama perusahaan Anda"
                            class="w-full h-[44px] rounded-2xl border px-4 text-[14px] focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <p x-show="errors.company_name" class="text-[11px] text-red-500 mt-1"
                            x-text="errors.company_name?.[0]"></p>
                    </div>

                    <!-- ALAMAT -->
                    <div class="mt-5">
                        <label class="block text-[14px] font-semibold text-gray-800 mb-2">
                            Alamat Perusahaan <span class="text-red-500">*</span>
                        </label>
                        <textarea rows="3" x-model="form.company_address"
                            :class="errors.company_address ? 'border-red-400 ring-1 ring-red-400' : 'border-gray-300'"
                            placeholder="Alamat lengkap perusahaan"
                            class="w-full rounded-2xl border px-4 py-3 text-[14px] resize-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                        <p x-show="errors.company_address" class="text-[11px] text-red-500 mt-1"
                            x-text="errors.company_address?.[0]"></p>
                    </div>

                    <!-- TELEPON & EMAIL -->
                    <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[14px] font-semibold text-gray-800 mb-2">
                                Nomor Telepon <span class="text-red-500">*</span>
                            </label>
                            <input type="text" x-model="form.company_phone"
                                :class="errors.company_phone ? 'border-red-400 ring-1 ring-red-400' : 'border-gray-300'"
                                placeholder="+62 21 1234 5678"
                                class="w-full h-[44px] rounded-2xl border px-4 text-[14px] focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <p x-show="errors.company_phone" class="text-[11px] text-red-500 mt-1"
                                x-text="errors.company_phone?.[0]"></p>
                        </div>
                        <div>
                            <label class="block text-[14px] font-semibold text-gray-800 mb-2">
                                Email Perusahaan <span class="text-red-500">*</span>
                            </label>
                            <input type="email" x-model="form.company_email"
                                :class="errors.company_email ? 'border-red-400 ring-1 ring-red-400' : 'border-gray-300'"
                                placeholder="info@perusahaan.com"
                                class="w-full h-[44px] rounded-2xl border px-4 text-[14px] focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <p x-show="errors.company_email" class="text-[11px] text-red-500 mt-1"
                                x-text="errors.company_email?.[0]"></p>
                        </div>
                    </div>

                    <!-- LOGO -->
                    <div class="mt-5">
                        <label class="block text-[14px] font-semibold text-gray-800 mb-2">Logo Perusahaan</label>
                        <!-- Preview logo yang sudah ada -->
                        <div x-show="form.existing_logo" class="mb-3 flex items-center gap-3">
                            <img :src="form.existing_logo" alt="Logo"
                                class="h-12 object-contain rounded-lg border border-gray-200 px-2">
                            <span class="text-[12px] text-gray-500">Logo saat ini</span>
                        </div>
                        <label class="block border-2 border-dashed border-gray-300 rounded-2xl p-6 text-center text-gray-500 cursor-pointer hover:border-blue-400 hover:bg-blue-50/30 transition">
                            <div class="flex items-center justify-center gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                <span class="text-[14px]" x-text="logoFileName || 'Klik untuk upload logo'"></span>
                            </div>
                            <input type="file" name="company_logo" accept="image/*"
                                @change="handleLogoChange($event)"
                                class="hidden">
                        </label>
                        <p class="text-[12px] text-gray-500 mt-2">
                            Format: JPG, PNG, GIF, WebP. Maksimal 5MB. Disarankan 200x80px
                        </p>
                    </div>

                    <!-- ══ INFORMASI REKENING BANK ══ -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="flex items-center gap-2 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                            </svg>
                            <h3 class="text-[18px] font-bold text-gray-800">Informasi Rekening Bank</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[14px] font-semibold text-gray-800 mb-2">Nama Bank</label>
                                <input type="text" x-model="form.bank_name"
                                    placeholder="Bank BCA, Bank Mandiri, dll"
                                    class="w-full h-[44px] rounded-2xl border border-gray-300 px-4 text-[14px]">
                            </div>
                            <div>
                                <label class="block text-[14px] font-semibold text-gray-800 mb-2">Nomor Rekening</label>
                                <input type="text" x-model="form.bank_account"
                                    placeholder="1234567890"
                                    class="w-full h-[44px] rounded-2xl border border-gray-300 px-4 text-[14px]">
                            </div>
                        </div>
                        <div class="mt-5">
                            <label class="block text-[14px] font-semibold text-gray-800 mb-2">Nama Pemegang Rekening</label>
                            <input type="text" x-model="form.bank_holder"
                                placeholder="Nama sesuai rekening bank"
                                class="w-full h-[44px] rounded-2xl border border-gray-300 px-4 text-[14px]">
                        </div>
                        <div class="mt-5">
                            <label class="block text-[14px] font-semibold text-gray-800 mb-2">Instruksi Pembayaran</label>
                            <textarea rows="3" x-model="form.payment_instruction"
                                class="w-full rounded-2xl border border-gray-300 px-4 py-3 text-[14px] resize-none"
                                placeholder="Instruksi transfer untuk klien, contoh : Silakan transfer ke rekening di atas dan kirimkan bukti transfer untuk konfirmasi pembayaran."></textarea>
                        </div>
                    </div>

                    <!-- ══ REKENING BANK ALTERNATIF ══ -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="flex items-center gap-2 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                            </svg>
                            <h3 class="text-[18px] font-bold text-gray-800">Rekening Bank Alternatif (Opsional)</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[14px] font-semibold text-gray-800 mb-2">Nama Bank Kedua</label>
                                <input type="text" x-model="form.bank_name_2"
                                    placeholder="Bank Mandiri, BNI, dll"
                                    class="w-full h-[44px] rounded-2xl border border-gray-300 px-4 text-[14px]">
                            </div>
                            <div>
                                <label class="block text-[14px] font-semibold text-gray-800 mb-2">Nomor Rekening Kedua</label>
                                <input type="text" x-model="form.bank_account_2"
                                    placeholder="0987654321"
                                    class="w-full h-[44px] rounded-2xl border border-gray-300 px-4 text-[14px]">
                            </div>
                        </div>
                        <div class="mt-5">
                            <label class="block text-[14px] font-semibold text-gray-800 mb-2">Nama Pemegang Rekening Kedua</label>
                            <input type="text" x-model="form.bank_holder_2"
                                placeholder="Nama sesuai rekening bank kedua"
                                class="w-full h-[44px] rounded-2xl border border-gray-300 px-4 text-[14px]">
                        </div>
                    </div>

                    <!-- ACTION BUTTONS -->
                    <div class="mt-6 grid grid-cols-2 gap-3">
                        <button type="button" @click="closeCompanySettingsModal()"
                            class="h-[48px] rounded-2xl bg-white border border-gray-200 text-gray-800 font-semibold text-[15px] hover:bg-gray-50 transition">
                            Batal
                        </button>
                        <button type="submit" :disabled="submitting"
                            class="h-[48px] rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-[15px] flex items-center justify-center gap-2 transition disabled:opacity-60">
                            <template x-if="submitting">
                                <svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                            </template>
                            <template x-if="!submitting">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                                </svg>
                            </template>
                            <span x-text="submitting ? 'Menyimpan...' : 'Simpan Pengaturan'"></span>
                        </button>
                    </div>
                </form>

                <!-- TIPS PENGATURAN -->
                <div class="mt-6 rounded-2xl bg-blue-50 border border-blue-100 p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h4 class="font-bold text-blue-700 text-[15px]">Tips Pengaturan</h4>
                    </div>
                    <ul class="space-y-1.5 text-[14px] text-blue-700 list-disc pl-5">
                        <li>Pastikan informasi perusahaan akurat untuk tampil profesional di invoice</li>
                        <li>Logo perusahaan akan ditampilkan di bagian atas invoice jika diisi</li>
                        <li>Informasi rekening bank opsional, akan muncul di invoice jika diisi</li>
                        <li>Pengaturan ini akan tersimpan dan digunakan untuk semua invoice yang dibuat</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function companySettingsForm() {
    return {
        loading:      true,
        submitting:   false,
        errors:       {},
        logoFile:     null,
        logoFileName: '',
        form: {
            company_name:        '',
            company_address:     '',
            company_phone:       '',
            company_email:       '',
            existing_logo:       '',
            bank_name:           '',
            bank_account:        '',
            bank_holder:         '',
            payment_instruction: '',
            bank_name_2:         '',
            bank_account_2:      '',
            bank_holder_2:       '',
        },

        // ── Load/reload data dari backend ─────────────────────
        async loadSettings() {
            this.loading    = true
            this.errors     = {}
            this.logoFile   = null
            this.logoFileName = ''

            try {
                const res    = await fetch('/company-setting', {
                    headers: { 'Accept': 'application/json' }
                })
                const result = await res.json()

                if (result.success && result.data) {
                    const d = result.data
                    this.form.company_name        = d.company_name        ?? ''
                    this.form.company_address     = d.company_address     ?? ''
                    this.form.company_phone       = d.company_phone       ?? ''
                    this.form.company_email       = d.company_email       ?? ''
                    this.form.bank_name           = d.bank_name           ?? ''
                    this.form.bank_account        = d.bank_account        ?? ''
                    this.form.bank_holder         = d.bank_holder         ?? ''
                    this.form.payment_instruction = d.payment_instruction ?? ''
                    this.form.bank_name_2         = d.bank_name_2         ?? ''
                    this.form.bank_account_2      = d.bank_account_2      ?? ''
                    this.form.bank_holder_2       = d.bank_holder_2       ?? ''
                    this.form.existing_logo       = d.company_logo
                        ? '/storage/' + d.company_logo
                        : ''
                } else {
                    // Belum ada data — kosongkan semua field
                    this.resetForm()
                }
            } catch (e) {
                this.resetForm()
            } finally {
                this.loading = false
            }
        },

        // ── Reset form ke kosong ───────────────────────────────
        resetForm() {
            this.form = {
                company_name:        '',
                company_address:     '',
                company_phone:       '',
                company_email:       '',
                existing_logo:       '',
                bank_name:           '',
                bank_account:        '',
                bank_holder:         '',
                payment_instruction: '',
                bank_name_2:         '',
                bank_account_2:      '',
                bank_holder_2:       '',
            }
        },

        // ── Handle file logo dipilih ──────────────────────────
        handleLogoChange(event) {
            const file = event.target.files[0]
            if (!file) return
            this.logoFile     = file
            this.logoFileName = file.name
        },

        // ── Simpan ke backend ──────────────────────────────────
        async saveSettings() {
            this.submitting = true
            this.errors     = {}

            const formData = new FormData()
            formData.append('company_name',        this.form.company_name        ?? '')
            formData.append('company_address',     this.form.company_address     ?? '')
            formData.append('company_phone',       this.form.company_phone       ?? '')
            formData.append('company_email',       this.form.company_email       ?? '')
            formData.append('bank_name',           this.form.bank_name           ?? '')
            formData.append('bank_account',        this.form.bank_account        ?? '')
            formData.append('bank_holder',         this.form.bank_holder         ?? '')
            formData.append('payment_instruction', this.form.payment_instruction ?? '')
            formData.append('bank_name_2',         this.form.bank_name_2         ?? '')
            formData.append('bank_account_2',      this.form.bank_account_2      ?? '')
            formData.append('bank_holder_2',       this.form.bank_holder_2       ?? '')
            formData.append('_token', document.querySelector('meta[name=csrf-token]').content)

            if (this.logoFile) {
                formData.append('company_logo', this.logoFile)
            }

            try {
                const res    = await fetch('/company-setting', {
                    method:  'POST',
                    headers: { 'Accept': 'application/json' },
                    body:    formData
                })
                const result = await res.json()

                if (res.status === 422) {
                    this.errors = result.errors ?? {}
                    return
                }

                if (!res.ok) {
                    alert(result.message ?? 'Terjadi kesalahan.')
                    return
                }

                // ── Update data di form dari response backend ──
                if (result.data) {
                    const d = result.data
                    this.form.company_name        = d.company_name        ?? ''
                    this.form.company_address     = d.company_address     ?? ''
                    this.form.company_phone       = d.company_phone       ?? ''
                    this.form.company_email       = d.company_email       ?? ''
                    this.form.bank_name           = d.bank_name           ?? ''
                    this.form.bank_account        = d.bank_account        ?? ''
                    this.form.bank_holder         = d.bank_holder         ?? ''
                    this.form.payment_instruction = d.payment_instruction ?? ''
                    this.form.bank_name_2         = d.bank_name_2         ?? ''
                    this.form.bank_account_2      = d.bank_account_2      ?? ''
                    this.form.bank_holder_2       = d.bank_holder_2       ?? ''
                    this.form.existing_logo       = d.company_logo
                        ? '/storage/' + d.company_logo
                        : this.form.existing_logo
                }

                // Reset file input setelah upload
                this.logoFile     = null
                this.logoFileName = ''

                // Tutup modal
                const parentEl = document.querySelector('[x-data^="dashboardFilter"]')
                    ?? document.querySelector('[x-data]')
                if (parentEl) {
                    const parent = Alpine.$data(parentEl)
                    if (parent && typeof parent.closeCompanySettingsModal === 'function') {
                        parent.closeCompanySettingsModal()
                    }
                }

                // Notifikasi sukses
                if (window.Swal) {
                    Swal.fire({
                        icon:               'success',
                        title:              'Tersimpan!',
                        text:               result.message,
                        confirmButtonColor: '#2563eb',
                        timer:              2000,
                        timerProgressBar:   true,
                        showConfirmButton:  false,
                        customClass:        { popup: 'rounded-[28px]' }
                    })
                } else {
                    alert(result.message)
                }

            } catch (err) {
                alert('Gagal terhubung ke server.')
            } finally {
                this.submitting = false
            }
        }
    }
}
</script>