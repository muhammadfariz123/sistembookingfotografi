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
            class="bg-white w-full max-w-[780px] rounded-[22px] shadow-2xl overflow-hidden">

            <!-- HEADER -->
            <div class="px-6 py-5 border-b border-gray-200 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                        <i data-lucide="building-2" class="w-5 h-5"></i>
                    </div>
                    <h2 class="text-[20px] font-bold text-gray-900">
                        Pengaturan Perusahaan
                    </h2>
                </div>

                <button
                    type="button"
                    @click="closeCompanySettingsModal()"
                    class="text-gray-500 hover:text-gray-700 transition">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- BODY -->
            <!-- UBAH: no-scrollbar dihapus agar scrollbar browser tampil -->
            <div class="max-h-[75vh] overflow-y-auto px-6 py-6">

                <!-- ===================================== -->
                <!-- INFORMASI PERUSAHAAN -->
                <!-- ===================================== -->
                <div class="flex items-center gap-2 mb-1">
                    <i data-lucide="building-2" class="w-5 h-5 text-gray-700"></i>
                    <h3 class="text-[18px] font-bold text-gray-800">
                        Informasi Perusahaan
                    </h3>
                </div>

                <!-- FORM FRONTEND ONLY -->
                <form
                    @submit.prevent="
                        alert('Frontend only: Pengaturan perusahaan berhasil disimpan.');
                        closeCompanySettingsModal();
                    "
                    enctype="multipart/form-data">

                    @csrf

                    <!-- NAMA PERUSAHAAN -->
                    <div class="mt-4">
                        <label class="block text-[14px] font-semibold text-gray-800 mb-2">
                            Nama Perusahaan <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="company_name"
                            value="CatatKlien"
                            class="w-full h-[44px] rounded-2xl border border-gray-300 px-4 text-[14px] focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- ALAMAT -->
                    <div class="mt-5">
                        <label class="block text-[14px] font-semibold text-gray-800 mb-2">
                            Alamat Perusahaan <span class="text-red-500">*</span>
                        </label>
                        <textarea
                            rows="3"
                            name="company_address"
                            class="w-full rounded-2xl border border-gray-300 px-4 py-3 text-[14px] resize-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">Jl. Contoh No. 123
Jakarta, Indonesia</textarea>
                    </div>

                    <!-- TELEPON & EMAIL -->
                    <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[14px] font-semibold text-gray-800 mb-2">
                                Nomor Telepon <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="company_phone"
                                value="+62 21 1234 5678"
                                class="w-full h-[44px] rounded-2xl border border-gray-300 px-4 text-[14px] focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-[14px] font-semibold text-gray-800 mb-2">
                                Email Perusahaan <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="email"
                                name="company_email"
                                value="info@catatklien.com"
                                class="w-full h-[44px] rounded-2xl border border-gray-300 px-4 text-[14px] focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>

                    <!-- LOGO -->
                    <div class="mt-5">
                        <label class="block text-[14px] font-semibold text-gray-800 mb-2">
                            Logo Perusahaan
                        </label>

                        <label class="block border-2 border-dashed border-gray-300 rounded-2xl p-8 text-center text-gray-500 cursor-pointer hover:border-blue-400 hover:bg-blue-50/30 transition">
                            <div class="flex items-center justify-center gap-3">
                                <i data-lucide="upload" class="w-5 h-5"></i>
                                <span class="text-[14px]">Klik untuk upload logo</span>
                            </div>
                            <input
                                type="file"
                                name="company_logo"
                                accept="image/*"
                                class="hidden">
                        </label>

                        <p class="text-[12px] text-gray-500 mt-2">
                            Format: JPG, PNG, GIF, WebP. Maksimal 5MB. Disarankan 200x80px
                        </p>
                    </div>

                    <!-- ===================================== -->
                    <!-- INFORMASI REKENING BANK -->
                    <!-- ===================================== -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="flex items-center gap-2 mb-4">
                            <i data-lucide="landmark" class="w-5 h-5 text-gray-700"></i>
                            <h3 class="text-[18px] font-bold text-gray-800">
                                Informasi Rekening Bank
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[14px] font-semibold text-gray-800 mb-2">
                                    Nama Bank
                                </label>
                                <input
                                    type="text"
                                    name="bank_name"
                                    placeholder="Bank BCA, Bank Mandiri, dll"
                                    class="w-full h-[44px] rounded-2xl border border-gray-300 px-4 text-[14px]">
                            </div>

                            <div>
                                <label class="block text-[14px] font-semibold text-gray-800 mb-2">
                                    Nomor Rekening
                                </label>
                                <input
                                    type="text"
                                    name="bank_account"
                                    placeholder="1234567890"
                                    class="w-full h-[44px] rounded-2xl border border-gray-300 px-4 text-[14px]">
                            </div>
                        </div>

                        <div class="mt-5">
                            <label class="block text-[14px] font-semibold text-gray-800 mb-2">
                                Nama Pemegang Rekening
                            </label>
                            <input
                                type="text"
                                name="bank_holder"
                                placeholder="Nama sesuai rekening bank"
                                class="w-full h-[44px] rounded-2xl border border-gray-300 px-4 text-[14px]">
                        </div>

                        <div class="mt-5">
                            <label class="block text-[14px] font-semibold text-gray-800 mb-2">
                                Instruksi Pembayaran
                            </label>
                            <textarea
                                rows="3"
                                name="payment_instruction"
                                class="w-full rounded-2xl border border-gray-300 px-4 py-3 text-[14px] resize-none">Silakan transfer ke rekening di atas dan kirimkan bukti transfer untuk konfirmasi pembayaran.</textarea>
                        </div>
                    </div>

                    <!-- ===================================== -->
                    <!-- REKENING BANK ALTERNATIF -->
                    <!-- ===================================== -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="flex items-center gap-2 mb-4">
                            <i data-lucide="landmark" class="w-5 h-5 text-gray-700"></i>
                            <h3 class="text-[18px] font-bold text-gray-800">
                                Rekening Bank Alternatif (Opsional)
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[14px] font-semibold text-gray-800 mb-2">
                                    Nama Bank Kedua
                                </label>
                                <input
                                    type="text"
                                    name="bank_name_2"
                                    placeholder="Bank Mandiri, BNI, dll"
                                    class="w-full h-[44px] rounded-2xl border border-gray-300 px-4 text-[14px]">
                            </div>

                            <div>
                                <label class="block text-[14px] font-semibold text-gray-800 mb-2">
                                    Nomor Rekening Kedua
                                </label>
                                <input
                                    type="text"
                                    name="bank_account_2"
                                    placeholder="0987654321"
                                    class="w-full h-[44px] rounded-2xl border border-gray-300 px-4 text-[14px]">
                            </div>
                        </div>

                        <div class="mt-5">
                            <label class="block text-[14px] font-semibold text-gray-800 mb-2">
                                Nama Pemegang Rekening Kedua
                            </label>
                            <input
                                type="text"
                                name="bank_holder_2"
                                placeholder="Nama sesuai rekening bank kedua"
                                class="w-full h-[44px] rounded-2xl border border-gray-300 px-4 text-[14px]">
                        </div>
                    </div>

                    <!-- ACTION BUTTONS -->
                    <div class="mt-6 grid grid-cols-2 gap-3">
                        <button
                            type="button"
                            @click="closeCompanySettingsModal()"
                            class="h-[48px] rounded-2xl bg-white border border-gray-200 text-gray-800 font-semibold text-[15px] hover:bg-gray-50 transition">
                            Batal
                        </button>

                        <button
                            type="submit"
                            class="h-[48px] rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-[15px] flex items-center justify-center gap-2 transition">
                            <i data-lucide="save" class="w-4 h-4"></i>
                            Simpan Pengaturan
                        </button>
                    </div>
                </form>

                <!-- TIPS PENGATURAN -->
                <div class="mt-6 rounded-2xl bg-blue-50 border border-blue-100 p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <i data-lucide="info" class="w-5 h-5 text-blue-600"></i>
                        <h4 class="font-bold text-blue-700 text-[15px]">
                            Tips Pengaturan
                        </h4>
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