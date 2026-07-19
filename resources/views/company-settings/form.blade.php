<x-app-layout>
    <div class="w-full min-h-screen px-4 sm:px-6 lg:px-8 py-8 sm:py-10 bg-[#f5f7fb]">
        
        <div class="w-full max-w-3xl mx-auto"> 
            
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-gray-500 hover:text-blue-600 text-[13px] font-semibold transition mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke Dashboard
            </a>

            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 rounded-2xl px-5 py-4 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <p class="text-[14px] font-semibold text-green-800">{{ session('success') }}</p>
                </div>
            @endif

            <div class="bg-white border border-gray-200 rounded-[24px] shadow-sm p-6 sm:p-8">
                
                <div class="flex items-center gap-3 mb-6 border-b border-gray-100 pb-5">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <h2 class="text-[22px] font-bold text-gray-900">Pengaturan Perusahaan</h2>
                </div>

                <div class="mb-8 rounded-2xl bg-blue-50 border border-blue-100 p-5 flex items-start gap-3">
                    <div class="mt-0.5 shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-blue-800 text-[14px]">Informasi Penting</h4>
                        <p class="text-[13px] text-blue-700 mt-1 leading-relaxed">
                            Seluruh data pengaturan perusahaan (termasuk Logo, Alamat, dan Rekening Bank/QRIS) akan ditampilkan secara langsung pada dokumen <b>Invoice</b> yang Anda cetak atau kirim ke Klien. Pastikan data yang dimasukkan akurat.
                        </p>
                    </div>
                </div>

                <form action="{{ route('company-setting.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" 
                      x-data="{ paymentMethod: '{{ old('payment_method', $setting->payment_method ?? 'bank_transfer') }}', fileName: '', qrisFileName: '' }">
                    @csrf

                    <div>
                        <h3 class="text-[16px] font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Informasi Dasar
                        </h3>
                        
                        <div class="space-y-5">
                            <div>
                                <label class="block text-[13px] font-medium text-gray-700 mb-1.5">
                                    Nama Perusahaan <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="company_name" required value="{{ old('company_name', $setting->company_name) }}"
                                    placeholder="Nama perusahaan Anda"
                                    class="w-full h-[48px] rounded-xl border {{ $errors->has('company_name') ? 'border-red-400 ring-1 ring-red-400' : 'border-gray-300' }} px-4 text-[14px] focus:outline-none focus:ring-2 focus:ring-blue-500/20 shadow-sm">
                                @error('company_name')<p class="text-[12px] text-red-500 mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-[13px] font-medium text-gray-700 mb-1.5">
                                        Nomor Telepon <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="company_phone" required value="{{ old('company_phone', $setting->company_phone) }}"
                                        placeholder="+62 812 3456 7890"
                                        class="w-full h-[48px] rounded-xl border {{ $errors->has('company_phone') ? 'border-red-400 ring-1 ring-red-400' : 'border-gray-300' }} px-4 text-[14px] focus:outline-none focus:ring-2 focus:ring-blue-500/20 shadow-sm">
                                    @error('company_phone')<p class="text-[12px] text-red-500 mt-1">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="block text-[13px] font-medium text-gray-700 mb-1.5">
                                        Email Perusahaan <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" name="company_email" required value="{{ old('company_email', $setting->company_email) }}"
                                        placeholder="info@perusahaan.com"
                                        class="w-full h-[48px] rounded-xl border {{ $errors->has('company_email') ? 'border-red-400 ring-1 ring-red-400' : 'border-gray-300' }} px-4 text-[14px] focus:outline-none focus:ring-2 focus:ring-blue-500/20 shadow-sm">
                                    @error('company_email')<p class="text-[12px] text-red-500 mt-1">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-[13px] font-medium text-gray-700 mb-1.5">
                                    Alamat Perusahaan <span class="text-red-500">*</span>
                                </label>
                                <textarea name="company_address" rows="3" required placeholder="Alamat lengkap perusahaan"
                                    class="w-full rounded-xl border {{ $errors->has('company_address') ? 'border-red-400 ring-1 ring-red-400' : 'border-gray-300' }} px-4 py-3 text-[14px] resize-none focus:outline-none focus:ring-2 focus:ring-blue-500/20 shadow-sm">{{ old('company_address', $setting->company_address) }}</textarea>
                                @error('company_address')<p class="text-[12px] text-red-500 mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="block text-[13px] font-medium text-gray-700 mb-1.5">Logo Perusahaan</label>
                                
                                @if($setting->company_logo)
                                    <div class="mb-3 flex items-center gap-3">
                                        <img src="{{ asset('storage/' . $setting->company_logo) }}" alt="Logo" class="h-14 object-contain rounded-lg border border-gray-200 px-2 bg-gray-50">
                                        <span class="text-[12px] text-gray-500">Logo saat ini</span>
                                    </div>
                                @endif
                                
                                <label class="block border-2 border-dashed border-gray-300 rounded-xl p-6 text-center text-gray-500 cursor-pointer hover:border-blue-400 hover:bg-blue-50/50 transition">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                        </svg>
                                        <span class="text-[14px] font-medium" x-text="fileName ? fileName : 'Pilih atau letakkan file logo di sini'"></span>
                                        <span class="text-[12px] text-gray-400">Format: JPG, PNG. Maksimal 5MB.</span>
                                    </div>
                                    <input type="file" name="company_logo" accept="image/*" @change="fileName = $event.target.files[0].name" class="hidden">
                                </label>
                                @error('company_logo')<p class="text-[12px] text-red-500 mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="pt-8 border-t border-gray-100">
                        <h3 class="text-[16px] font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            Metode Pembayaran
                        </h3>

                        {{-- PILIHAN METODE PEMBAYARAN (Radio Button) --}}
                        <div class="flex items-center gap-6 mb-6">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="payment_method" value="bank_transfer" x-model="paymentMethod" class="text-blue-600 focus:ring-blue-500 border-gray-300 w-4 h-4">
                                <span class="text-[14px] font-medium text-gray-800">Transfer Bank Manual</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="payment_method" value="qris" x-model="paymentMethod" class="text-blue-600 focus:ring-blue-500 border-gray-300 w-4 h-4">
                                <span class="text-[14px] font-medium text-gray-800">QRIS</span>
                            </label>
                        </div>

                        {{-- SECTION TRANSFER BANK --}}
                        <div x-show="paymentMethod === 'bank_transfer'" x-transition class="space-y-5 bg-gray-50 rounded-2xl p-5 border border-gray-200">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-[13px] font-medium text-gray-700 mb-1.5">Nama Bank</label>
                                    <input type="text" name="bank_name" value="{{ old('bank_name', $setting->bank_name) }}" placeholder="BCA / Mandiri" class="w-full h-[44px] rounded-xl border border-gray-300 px-4 text-[14px] shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-[13px] font-medium text-gray-700 mb-1.5">Nomor Rekening</label>
                                    <input type="text" name="bank_account" value="{{ old('bank_account', $setting->bank_account) }}" placeholder="123456789" class="w-full h-[44px] rounded-xl border border-gray-300 px-4 text-[14px] shadow-sm">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-[13px] font-medium text-gray-700 mb-1.5">Atas Nama</label>
                                    <input type="text" name="bank_holder" value="{{ old('bank_holder', $setting->bank_holder) }}" placeholder="Nama Pemilik Rekening" class="w-full h-[44px] rounded-xl border border-gray-300 px-4 text-[14px] shadow-sm">
                                </div>
                            </div>
                            
                            <div class="border-t border-gray-200 my-4"></div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-[13px] font-medium text-gray-700 mb-1.5">Nama Bank Opsional (Ke-2)</label>
                                    <input type="text" name="bank_name_2" value="{{ old('bank_name_2', $setting->bank_name_2) }}" placeholder="BCA / Mandiri" class="w-full h-[44px] rounded-xl border border-gray-300 px-4 text-[14px] shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-[13px] font-medium text-gray-700 mb-1.5">Nomor Rekening Ke-2</label>
                                    <input type="text" name="bank_account_2" value="{{ old('bank_account_2', $setting->bank_account_2) }}" placeholder="123456789" class="w-full h-[44px] rounded-xl border border-gray-300 px-4 text-[14px] shadow-sm">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-[13px] font-medium text-gray-700 mb-1.5">Atas Nama Ke-2</label>
                                    <input type="text" name="bank_holder_2" value="{{ old('bank_holder_2', $setting->bank_holder_2) }}" placeholder="Nama Pemilik Rekening" class="w-full h-[44px] rounded-xl border border-gray-300 px-4 text-[14px] shadow-sm">
                                </div>
                            </div>
                        </div>

                        {{-- SECTION QRIS --}}
                        <div x-show="paymentMethod === 'qris'" x-transition style="display: none;" class="space-y-5 bg-gray-50 rounded-2xl p-5 border border-gray-200">
                            <div>
                                <label class="block text-[13px] font-medium text-gray-700 mb-1.5">Gambar QRIS</label>
                                
                                @if($setting->qris_image)
                                    <div class="mb-3 flex items-center gap-3">
                                        <img src="{{ asset('storage/' . $setting->qris_image) }}" alt="QRIS" class="h-32 object-contain rounded-lg border border-gray-200 px-2 bg-white">
                                        <span class="text-[12px] text-gray-500">QRIS saat ini</span>
                                    </div>
                                @endif
                                
                                <label class="block border-2 border-dashed border-gray-300 rounded-xl p-6 text-center text-gray-500 cursor-pointer hover:border-blue-400 hover:bg-blue-50/50 transition">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        <span class="text-[14px] font-medium" x-text="qrisFileName ? qrisFileName : 'Upload gambar QRIS untuk ditampilkan ke pelanggan.'"></span>
                                        <span class="text-[12px] text-gray-400">Format: JPG, PNG. Maksimal 5MB.</span>
                                    </div>
                                    <input type="file" name="qris_image" accept="image/*" @change="qrisFileName = $event.target.files[0].name" class="hidden">
                                </label>
                                @error('qris_image')<p class="text-[12px] text-red-500 mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        {{-- INSTRUKSI PEMBAYARAN KEDUANYA (UMUM) --}}
                        <div class="mt-5">
                            <label class="block text-[13px] font-medium text-gray-700 mb-1.5">Instruksi Pembayaran</label>
                            <textarea name="payment_instruction" rows="3" placeholder="Contoh: Pembayaran sah jika ditransfer ke rekening/QRIS di atas."
                                class="w-full rounded-xl border border-gray-300 px-4 py-3 text-[14px] resize-none shadow-sm">{{ old('payment_instruction', $setting->payment_instruction ?? 'Silakan selesaikan pembayaran dan kirimkan bukti transfer.') }}</textarea>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-100 flex flex-col-reverse sm:flex-row justify-end gap-3">
                        <a href="{{ route('dashboard') }}" class="h-[48px] px-6 rounded-xl border border-gray-300 text-gray-600 font-semibold text-[15px] hover:bg-gray-50 flex items-center justify-center transition-colors">
                            Batal
                        </a>
                        <button type="submit" class="h-[48px] px-8 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-[15px] rounded-xl shadow-lg shadow-blue-600/20 transition-all flex items-center justify-center gap-2">
                            Simpan Pengaturan
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </div>
</x-app-layout>