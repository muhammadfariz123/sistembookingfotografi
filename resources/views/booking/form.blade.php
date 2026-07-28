{{-- resources/views/booking/form.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Booking — {{ $companySetting?->company_name ?? $owner->name }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .bg-brand { background-color: #f59e0b; }
        .text-brand { color: #f59e0b; }
        .border-brand { border-color: #f59e0b; }
        .ring-brand { --tw-ring-color: #f59e0b; }
    </style>
</head>
<body class="min-h-screen bg-gray-50 text-gray-800 antialiased relative">
    <x-customer-navbar :owner="$owner" :companySetting="$companySetting" :ownerId="$ownerId" showHome="true" />
    
    <div class="max-w-3xl mx-auto px-4 py-10" x-data="bookingWizard('{{ $selectedService->id ?? old('service_type_id', '') }}', {{ $selectedService->price ?? 0 }}, '{{ $selectedService->name ?? '' }}')">
        <div class="text-center mb-10">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Form Booking</h1>
            <p class="text-gray-500 mt-2 text-sm">Lengkapi 4 tahapan di bawah ini untuk menyelesaikan pesanan Anda.</p>
        </div>
        
        <div class="relative mb-12 px-2 sm:px-8">
            <div class="absolute left-0 top-4 w-full h-[2px] bg-gray-200 z-0"></div>
            <div class="absolute left-0 top-4 h-[2px] bg-brand z-0 transition-all duration-500" :style="'width: ' + ((step - 1) / 3) * 100 + '%'"></div>
            <div class="relative z-10 flex justify-between">
                <div class="flex flex-col items-center bg-gray-50 px-2 cursor-pointer" @click="if(step > 1) step = 1">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs transition-colors duration-300" :class="step >= 1 ? 'bg-brand text-white' : 'bg-gray-200 text-gray-400'">1</div>
                    <span class="text-xs mt-2 font-medium" :class="step >= 1 ? 'text-gray-900' : 'text-gray-400'">Paket</span>
                </div>
                <div class="flex flex-col items-center bg-gray-50 px-2 cursor-pointer" @click="if(step > 2) step = 2">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs transition-colors duration-300" :class="step >= 2 ? 'bg-brand text-white' : 'bg-gray-200 text-gray-400'">2</div>
                    <span class="text-xs mt-2 font-medium" :class="step >= 2 ? 'text-gray-900' : 'text-gray-400'">Tanggal & Waktu</span>
                </div>
                <div class="flex flex-col items-center bg-gray-50 px-2 cursor-pointer" @click="if(step > 3) step = 3">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs transition-colors duration-300" :class="step >= 3 ? 'bg-brand text-white' : 'bg-gray-200 text-gray-400'">3</div>
                    <span class="text-xs mt-2 font-medium" :class="step >= 3 ? 'text-gray-900' : 'text-gray-400'">Info Klien</span>
                </div>
                <div class="flex flex-col items-center bg-gray-50 px-2">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs transition-colors duration-300" :class="step >= 4 ? 'bg-gray-300 text-gray-600' : 'bg-gray-200 text-gray-400'">4</div>
                    <span class="text-xs mt-2 font-medium" :class="step >= 4 ? 'text-gray-900' : 'text-gray-400'">Konfirmasi</span>
                </div>
            </div>
        </div>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-xl px-5 py-4 mb-6">
                <ul class="text-sm text-red-600 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div x-show="errorMsg" style="display: none;" class="bg-red-50 border border-red-200 text-red-600 rounded-xl px-5 py-3 mb-6 text-sm font-medium" x-transition>
            <span x-text="errorMsg"></span>
        </div>

        <form method="POST" action="{{ route('booking.public.store', $ownerId) }}" id="booking-form" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 sm:p-8">
            @csrf
            <input type="hidden" name="payment_type" id="hidden_payment_type" :value="paymentOption">
            <input type="hidden" name="booking_time" :value="bookingTime">
            
            {{-- STEP 1: PILIH PAKET --}}
            <div x-show="step === 1" x-transition.opacity>
                <div class="mb-6">
                    <h2 class="text-lg font-bold text-gray-900">Pilih Paket Foto</h2>
                    <p class="text-gray-500 text-sm mt-1">Pilih paket yang paling sesuai kebutuhanmu.</p>
                </div>
                @if($services->isEmpty())
                    <div class="py-10 text-center border border-dashed border-gray-200 rounded-xl text-gray-400 text-sm">Belum ada paket layanan tersedia saat ini.</div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($services as $service)
                            <label class="block cursor-pointer group h-full">
                                <input type="radio" name="service_type_id" value="{{ $service->id }}" x-model="selectedServiceId" @change="selectService('{{ $service->id }}', {{ (int) $service->price }}, '{{ $service->name }}')" class="sr-only">
                                <div class="border-2 rounded-xl p-5 h-full flex flex-col justify-between transition-all duration-200" :class="selectedServiceId === '{{ $service->id }}' ? 'border-brand bg-orange-50/10 shadow-sm' : 'border-gray-200 hover:border-gray-300'">
                                    <div x-show="selectedServiceId === '{{ $service->id }}'" class="absolute top-0 right-0 bg-brand text-white px-3 py-1 rounded-bl-xl font-bold text-xs" style="display: none;">✓ Terpilih</div>
                                    <div>
                                        <h3 class="font-bold text-base text-gray-900 mb-1">{{ $service->name }}</h3>
                                        @if($service->description)
                                            <div class="text-xs text-gray-500 whitespace-pre-line line-clamp-2 mb-4">{{ $service->description }}</div>
                                        @endif
                                    </div>
                                    <div class="flex justify-between items-end mt-auto pt-2">
                                        <p class="text-brand font-bold text-lg leading-none">Rp {{ number_format($service->price, 0, ',', '.') }}</p>
                                        <span class="text-[10px] font-medium bg-gray-100 text-gray-500 px-2 py-1 rounded-md">Estimasi</span>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                @endif
                <div class="mt-8 flex justify-end">
                    <button type="button" @click="nextStep()" class="bg-brand hover:opacity-90 text-white px-6 py-2.5 rounded-lg font-bold transition flex items-center gap-2 text-sm">Lanjut: Pilih Jadwal &rarr;</button>
                </div>
            </div>
            
            {{-- STEP 2: TANGGAL & WAKTU --}}
            <div x-show="step === 2" x-transition.opacity style="display: none;">
                <div class="mb-6">
                    <h2 class="text-lg font-bold text-gray-900">Pilih Tanggal & Waktu</h2>
                    <p class="text-gray-500 text-sm mt-1">Tentukan tanggal dan jam kedatangan (waktu standby fotografer).</p>
                </div>
                <div class="mb-6">
                    <label class="inline-flex items-center gap-3 cursor-pointer bg-gray-50 border border-gray-200 py-2.5 px-4 rounded-lg hover:bg-gray-100 transition">
                        <input type="checkbox" x-model="multiDay" class="w-4 h-4 rounded border-gray-300 text-brand focus:ring-brand">
                        <span class="text-sm font-semibold text-gray-700">Acara berlangsung lebih dari 1 hari</span>
                    </label>
                </div>
                
                {{-- SATU HARI --}}
                <div x-show="!multiDay" class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Acara <span class="text-red-500">*</span></label>
                        <input type="date" name="booking_date" x-model="bookingDate" :disabled="multiDay" :required="!multiDay" :min="todayDate" class="w-full h-11 rounded-lg border-gray-300 focus:border-brand focus:ring-brand shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Waktu Mulai <span class="text-red-500">*</span></label>
                        <div class="flex items-center gap-2">
                            <select x-model="bookingHour" :disabled="multiDay" :required="!multiDay" class="w-full h-11 rounded-lg border-gray-300 focus:border-brand focus:ring-brand shadow-sm text-sm cursor-pointer">
                                <option value="" disabled selected>Jam</option>
                                <template x-for="i in 24">
                                    <option :value="String(i-1).padStart(2, '0')" x-text="String(i-1).padStart(2, '0')"></option>
                                </template>
                            </select>
                            <span class="font-bold text-gray-400">:</span>
                            <select x-model="bookingMinute" :disabled="multiDay" :required="!multiDay" class="w-full h-11 rounded-lg border-gray-300 focus:border-brand focus:ring-brand shadow-sm text-sm cursor-pointer">
                                <option value="" disabled selected>Menit</option>
                                <template x-for="i in 12">
                                    <option :value="String((i-1)*5).padStart(2, '0')" x-text="String((i-1)*5).padStart(2, '0')"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- MULTI HARI --}}
                <div x-show="multiDay" class="grid grid-cols-1 sm:grid-cols-3 gap-5" style="display: none;">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Mulai <span class="text-red-500">*</span></label>
                        <input type="date" name="start_date" x-model="startDate" :disabled="!multiDay" :required="multiDay" :min="todayDate" class="w-full h-11 rounded-lg border-gray-300 focus:border-brand focus:ring-brand shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Selesai <span class="text-red-500">*</span></label>
                        <input type="date" name="end_date" x-model="endDate" :disabled="!multiDay" :required="multiDay" :min="startDate || todayDate" class="w-full h-11 rounded-lg border-gray-300 focus:border-brand focus:ring-brand shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Waktu Mulai <span class="text-red-500">*</span></label>
                        <div class="flex items-center gap-2">
                            <select x-model="bookingHour" :disabled="!multiDay" :required="multiDay" class="w-full h-11 rounded-lg border-gray-300 focus:border-brand focus:ring-brand shadow-sm text-sm cursor-pointer">
                                <option value="" disabled selected>Jam</option>
                                <template x-for="i in 24">
                                    <option :value="String(i-1).padStart(2, '0')" x-text="String(i-1).padStart(2, '0')"></option>
                                </template>
                            </select>
                            <span class="font-bold text-gray-400">:</span>
                            <select x-model="bookingMinute" :disabled="!multiDay" :required="multiDay" class="w-full h-11 rounded-lg border-gray-300 focus:border-brand focus:ring-brand shadow-sm text-sm cursor-pointer">
                                <option value="" disabled selected>Menit</option>
                                <template x-for="i in 12">
                                    <option :value="String((i-1)*5).padStart(2, '0')" x-text="String((i-1)*5).padStart(2, '0')"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-between items-center">
                    <button type="button" @click="prevStep()" class="border border-gray-300 text-gray-600 hover:bg-gray-50 px-5 py-2.5 rounded-lg font-bold transition text-sm">&larr; Kembali</button>
                    <button type="button" @click="nextStep()" class="bg-brand hover:opacity-90 text-white px-6 py-2.5 rounded-lg font-bold transition flex items-center gap-2 text-sm">Lanjut: Info Klien &rarr;</button>
                </div>
            </div>

            {{-- STEP 3: INFO KLIEN --}}
            <div x-show="step === 3" x-transition.opacity style="display: none;">
                <div class="mb-6">
                    <h2 class="text-lg font-bold text-gray-900">Informasi Klien</h2>
                    <p class="text-gray-500 text-sm mt-1">Isi data diri kamu untuk konfirmasi booking.</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="client_name" x-model="clientName" required placeholder="Nama lengkap" class="w-full h-11 rounded-lg border border-gray-300 px-4 text-sm focus:border-brand focus:ring-brand shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">WhatsApp <span class="text-red-500">*</span></label>
                        <input type="text" name="client_contact" x-model="clientContact" required placeholder="08xxxxxxxxxx" class="w-full h-11 rounded-lg border border-gray-300 px-4 text-sm focus:border-brand focus:ring-brand shadow-sm">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email Aktif</label>
                        <input type="email" name="client_email" x-model="clientEmail" placeholder="email@contoh.com" class="w-full h-11 rounded-lg border border-gray-300 px-4 text-sm focus:border-brand focus:ring-brand shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Akun Instagram</label>
                        <input type="text" name="client_instagram" x-model="clientInstagram" placeholder="@username" class="w-full h-11 rounded-lg border border-gray-300 px-4 text-sm focus:border-brand focus:ring-brand shadow-sm">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Lokasi Sesi / Alamat Acara</label>
                    <input type="text" name="client_address" x-model="clientAddress" placeholder="Ketik alamat atau lokasi..." class="w-full h-11 rounded-lg border border-gray-300 px-4 text-sm focus:border-brand focus:ring-brand shadow-sm">
                </div>
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Catatan Khusus</label>
                    <textarea name="notes" x-model="notes" rows="3" placeholder="Permintaan khusus, tema, durasi yang diinginkan dll." class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm resize-none focus:border-brand focus:ring-brand shadow-sm"></textarea>
                </div>
                <div class="mt-8 flex justify-between items-center">
                    <button type="button" @click="prevStep()" class="border border-gray-300 text-gray-600 hover:bg-gray-50 px-5 py-2.5 rounded-lg font-bold transition text-sm">&larr; Kembali</button>
                    <button type="button" @click="nextStep()" class="bg-brand hover:opacity-90 text-white px-6 py-2.5 rounded-lg font-bold transition flex items-center gap-2 text-sm">Lanjut: Konfirmasi &rarr;</button>
                </div>
            </div>

            {{-- STEP 4: KONFIRMASI BOOKING --}}
            <div x-show="step === 4" x-transition.opacity style="display: none;">
                <div class="mb-6">
                    <h2 class="text-lg font-bold text-gray-900">Konfirmasi Booking</h2>
                    <p class="text-gray-500 text-sm mt-1">Cek kembali detail booking sebelum melanjutkan ke pembayaran.</p>
                </div>
                <div class="mb-6">
                    <p class="text-sm font-semibold text-gray-700 mb-3">Pilihan Pembayaran</p>
                    <div class="space-y-3">
                        <label class="block cursor-pointer">
                            <input type="radio" value="LUNAS" x-model="paymentOption" class="sr-only peer">
                            <div class="border rounded-xl p-4 flex justify-between items-center transition" :class="paymentOption === 'LUNAS' ? 'border-brand bg-white shadow-sm' : 'border-gray-200 bg-white hover:border-gray-300'">
                                <div class="flex items-center gap-3">
                                    <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 transition" :class="paymentOption === 'LUNAS' ? 'border-brand' : 'border-gray-300'">
                                        <div x-show="paymentOption === 'LUNAS'" class="w-2.5 h-2.5 rounded-full bg-brand"></div>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900 text-sm">Bayar Lunas</p>
                                        <p class="text-xs text-gray-500">Langsung lunas, tidak ada tagihan lagi.</p>
                                    </div>
                                </div>
                                <p class="font-bold text-brand text-sm" x-text="formatCurrency(unitPrice)"></p>
                            </div>
                        </label>
                        <label class="block cursor-pointer">
                            <input type="radio" value="DP" x-model="paymentOption" class="sr-only peer">
                            <div class="border rounded-xl p-4 flex justify-between items-center transition" :class="paymentOption === 'DP' ? 'border-brand bg-white shadow-sm' : 'border-gray-200 bg-white hover:border-gray-300'">
                                <div class="flex items-center gap-3">
                                    <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 transition" :class="paymentOption === 'DP' ? 'border-brand' : 'border-gray-300'">
                                        <div x-show="paymentOption === 'DP'" class="w-2.5 h-2.5 rounded-full bg-brand"></div>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900 text-sm">DP 30%</p>
                                        <p class="text-xs text-gray-500">Bayar sebagian sekarang, sisanya saat sesi.</p>
                                    </div>
                                </div>
                                <p class="font-bold text-brand text-sm" x-text="formatCurrency(unitPrice * 0.3)"></p>
                            </div>
                        </label>
                    </div>
                </div>
                <div class="bg-gray-50 rounded-xl border border-gray-200 p-5 sm:p-6 mb-6">
                    <div class="space-y-3 text-sm text-gray-900">
                        <div class="flex justify-between items-start gap-4"><span class="text-gray-500 font-medium shrink-0">Nama</span><span class="font-bold text-right" x-text="clientName"></span></div>
                        <div class="flex justify-between items-start gap-4"><span class="text-gray-500 font-medium shrink-0">Email</span><span class="font-bold text-right" x-text="clientEmail || '-'"></span></div>
                        <div class="flex justify-between items-start gap-4"><span class="text-gray-500 font-medium shrink-0">WhatsApp</span><span class="font-bold text-right" x-text="clientContact"></span></div>
                        <div class="border-t border-gray-200 my-3"></div>
                        <div class="flex justify-between items-start gap-4"><span class="text-gray-500 font-medium shrink-0">Jadwal</span><span class="font-bold text-right" x-text="scheduleText"></span></div>
                        <div class="flex justify-between items-start gap-4"><span class="text-gray-500 font-medium shrink-0">Waktu Mulai</span><span class="font-bold text-right" x-text="(bookingTime || '-')"></span></div>
                        <div class="flex justify-between items-start gap-4"><span class="text-gray-500 font-medium shrink-0">Paket</span><span class="font-bold text-right" x-text="selectedServiceName"></span></div>
                        <div class="flex justify-between items-start gap-4"><span class="text-gray-500 font-medium shrink-0">Harga Paket</span><span class="font-bold text-right" x-text="formatCurrency(unitPrice)"></span></div>
                        <div class="border-t border-gray-200 my-3"></div>
                        <div class="flex justify-between items-center pt-1">
                            <span class="font-bold text-gray-900">Total Estimasi</span>
                            <span class="font-bold text-brand text-base" x-text="formatCurrency(unitPrice)"></span>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 rounded-xl border border-gray-200 p-4 mb-8">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" x-model="agreeTnC" class="mt-0.5 w-5 h-5 rounded border-gray-300 text-brand focus:ring-brand">
                        <div class="select-none">
                            <p class="text-sm font-bold text-gray-900">Saya menyetujui syarat dan ketentuan booking</p>
                            <button type="button" @click="showTermsModal = true" class="text-brand text-sm font-semibold mt-0.5 hover:underline">Lihat detail syarat & ketentuan</button>
                        </div>
                    </label>
                </div>
                <div class="flex justify-between">
                    <button type="button" @click="prevStep()" class="border border-gray-300 text-gray-600 hover:bg-gray-50 px-5 py-2.5 rounded-lg font-semibold transition text-sm">&larr; Kembali</button>
                    <button type="button" @click="submitForm()" class="bg-brand hover:opacity-90 text-white px-6 py-2.5 rounded-lg font-bold transition shadow-sm text-sm flex items-center gap-2"
                        x-text="paymentOption === 'LUNAS' ? `Booking & Bayar Lunas ${formatCurrency(unitPrice)}` : `Booking & Bayar DP ${formatCurrency(unitPrice * 0.3)}`">
                    </button>
                </div>
            </div>
        </form>

        <div x-show="showTermsModal" class="fixed inset-0 z-[100] flex items-center justify-center px-4" style="display: none;">
            <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showTermsModal = false"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6 sm:p-8 z-10">
                <button type="button" @click="showTermsModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
                <h3 class="text-lg font-bold text-gray-900 leading-tight">Syarat dan Ketentuan</h3>
                <div class="text-sm text-gray-600 mt-4 mb-6 leading-relaxed space-y-2">
                    <p>1. Pemesanan (booking) dianggap sah hanya jika pembayaran DP/Lunas telah dikonfirmasi oleh Admin.</p>
                    <p>2. Diharapkan hadir tepat waktu sesuai waktu mulai yang dipilih. Keterlambatan dapat memotong durasi sesi foto Anda.</p>
                    <p>3. Reschedule hanya dapat dilakukan maksimal H-3 sebelum tanggal pemotretan.</p>
                    <p>4. Pembatalan sepihak oleh klien akan mengakibatkan uang muka (DP) hangus.</p>
                    <p>5. Waktu pada sistem kami menggunakan Format 24 Jam.</p>
                </div>
                <div class="flex justify-end pt-2"><button type="button" @click="showTermsModal = false" class="border px-5 py-2 rounded-lg text-sm font-semibold hover:bg-gray-50">Tutup</button></div>
            </div>
        </div>
    </div>
    
    <x-customer-footer :owner="$owner" :companySetting="$companySetting" />
    
    <script>
        function bookingWizard(initialId = '', initialPrice = 0, initialName = '') {
            return {
                step: 1, errorMsg: '',
                selectedServiceId: String(initialId), unitPrice: Number(initialPrice), selectedServiceName: initialName,
                multiDay: false, bookingDate: '', startDate: '', endDate: '',

                // LOKASI VARIABEL
                get todayDate() {
                    const d = new Date();
                    const year = d.getFullYear();
                    const month = String(d.getMonth() + 1).padStart(2, '0');
                    const day = String(d.getDate()).padStart(2, '0');
                    return `${year}-${month}-${day}`;
                },

                // Ganti dari text input ke state Jam dan Menit
                bookingHour: '',
                bookingMinute: '',
                get bookingTime() {
                    if (this.bookingHour && this.bookingMinute) {
                        return `${this.bookingHour}:${this.bookingMinute}`;
                    }
                    return '';
                },

                clientName: '{{ old('client_name') }}', clientContact: '{{ old('client_contact') }}',
                clientEmail: '{{ old('client_email') }}', clientInstagram: '{{ old('client_instagram') }}',
                clientAddress: '{{ old('client_address') }}', notes: '{{ old('notes') }}',
                
                paymentOption: 'DP', 
                agreeTnC: false, showTermsModal: false,
                
                selectService(id, price, name) {
                    this.selectedServiceId = String(id); this.unitPrice = price; this.selectedServiceName = name; this.errorMsg = '';
                },
                
                nextStep() {
                    if (this.step === 1 && !this.selectedServiceId) { this.showError('Pilih paket dahulu.'); return; }
                    
                    if (this.step === 2) {
                        if (!this.multiDay && !this.bookingDate) { this.showError('Pilih tanggal acara.'); return; }
                        if (this.multiDay && (!this.startDate || !this.endDate)) { this.showError('Pilih tanggal mulai dan selesai.'); return; }
                        if (!this.bookingTime) { this.showError('Pilih jam dan menit waktu mulai.'); return; }
                        
                        // --- LOGIKA TIME BLOCKING JIKA BOOKING HARI INI ---
                        let checkDate = this.multiDay ? this.startDate : this.bookingDate;
                        if (checkDate === this.todayDate && this.bookingTime) {
                            const now = new Date();
                            
                            // Hapus detik dan milidetik agar perhitungan dimulai persis di awal menit
                            now.setSeconds(0, 0);
                            
                            const selectedTime = new Date(`${checkDate}T${this.bookingTime}:00`);
                            
                            // Set jeda kelonggaran menjadi 55 menit (agar booking 17:40 saat jam 16:41 bisa lolos)
                            const bufferMinutes = 55;
                            const minTime = new Date(now.getTime() + (bufferMinutes * 60 * 1000)); 
                            
                            if (selectedTime < minTime) {
                                this.showError('Untuk booking hari ini, waktu mulai sesi harus minimal 1 jam dari waktu sekarang.');
                                return;
                            }
                        }

                        // Jika multiDay false, pastikan endDate kosong agar tidak bentrok
                        if (!this.multiDay) {
                            this.startDate = '';
                            this.endDate = '';
                        } else {
                            this.bookingDate = '';
                        }
                    }
                    
                    if (this.step === 3 && (!this.clientName || !this.clientContact)) { this.showError('Isi kontak yang wajib (*).'); return; }
                    
                    this.errorMsg = ''; this.step++; window.scrollTo({ top: 0, behavior: 'smooth' });
                },
                
                prevStep() { if (this.step > 1) { this.step--; this.errorMsg = ''; window.scrollTo({ top: 0, behavior: 'smooth' }); } },
                showError(msg) { this.errorMsg = msg; window.scrollTo({ top: 0, behavior: 'smooth' }); },
                formatCurrency(value) { return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value || 0); },
                
                get scheduleText() { 
                    if (this.multiDay) {
                        return (this.startDate || '-') + ' s/d ' + (this.endDate || '-');
                    }
                    return (this.bookingDate || '-'); 
                },
                
                submitForm() {
                    if (!this.agreeTnC) { this.showError('Centang persetujuan dahulu.'); return; }
                    document.getElementById('hidden_payment_type').value = this.paymentOption;
                    
                    // Form akan otomatis menyertakan hidden input "booking_time" yang mengambil nilai get bookingTime()
                    document.getElementById('booking-form').submit();
                }
            }
        }
    </script>
</body>
</html>