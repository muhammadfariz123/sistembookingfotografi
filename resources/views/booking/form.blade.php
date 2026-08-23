{{-- resources/views/booking/form.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Booking — {{ $companySetting?->company_name ?? $owner->name }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    
    {{-- Flatpickr CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .bg-brand { background-color: #f59e0b; }
        .text-brand { color: #f59e0b; }
        .border-brand { border-color: #f59e0b; }
        .ring-brand { --tw-ring-color: #f59e0b; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-screen bg-gray-50 text-gray-800 antialiased relative">
    <x-customer-navbar :owner="$owner" :companySetting="$companySetting" :ownerId="$ownerId" showHome="true" />
    
    @php
        $initialServiceId = old('service_type_id', $selectedService->id ?? '');
        $initialServicePrice = 0;
        $initialServiceName = '';
        $initialCategory = '';
        $initialServiceDuration = 0;

        if ($initialServiceId) {
            $found = $services->firstWhere('id', $initialServiceId);
            if ($found) {
                $initialServicePrice = $found->price;
                $initialServiceName = $found->name;
                $initialCategory = $found->category ? $found->category->name : 'Lain-lain';
                $initialServiceDuration = $found->duration ?? 0;
            }
        }
    @endphp

    {{-- Pass bookedTimeSlots ke Alpine JS --}}
    <div class="max-w-3xl mx-auto px-4 py-10" x-data="bookingWizard('{{ $initialServiceId }}', {{ (int)$initialServicePrice }}, '{{ addslashes($initialServiceName) }}', '{{ addslashes($initialCategory) }}', {{ (int)$initialServiceDuration }}, @js($bookedTimeSlots ?? new \stdClass()))">
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
            
            {{-- STEP 1: PILIH KATEGORI LALU PAKET --}}
            <div x-show="step === 1" x-transition.opacity>
                <div class="mb-6 border-b border-gray-100 pb-4">
                    <h2 class="text-lg font-bold text-gray-900">Pilih Paket Foto</h2>
                    <p class="text-gray-500 text-sm mt-1">Pilih kategori acara dan paket yang paling sesuai kebutuhanmu.</p>
                </div>

                @if($services->isEmpty())
                    <div class="py-10 text-center border border-dashed border-gray-200 rounded-xl text-gray-400 text-sm">Belum ada paket layanan tersedia saat ini.</div>
                @else
                    @php
                        $groupedServices = $services->groupBy(function($item) {
                            return $item->category ? $item->category->name : 'Lain-lain';
                        });
                    @endphp

                    <div class="mb-8">
                        <label class="block text-sm font-bold text-gray-700 mb-2">1. Jenis Acara <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <select x-model="selectedCategory" @change="selectedServiceId = ''; unitPrice = 0; selectedServiceName = ''; selectedServiceDuration = 0" class="w-full h-12 rounded-xl border border-gray-300 px-4 text-sm font-semibold focus:border-brand focus:ring-brand shadow-sm appearance-none bg-white cursor-pointer text-gray-900">
                                <option value="" disabled selected>-- Klik untuk memilih jenis acara --</option>
                                @foreach($groupedServices->keys() as $categoryName)
                                    <option value="{{ $categoryName }}">{{ $categoryName }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                    </div>

                    <div x-show="selectedCategory !== ''" x-cloak class="mb-4" x-transition>
                        <label class="block text-sm font-bold text-gray-700 mb-3">2. Pilihan Paket Tersedia <span class="text-red-500">*</span></label>
                        
                        @foreach($groupedServices as $categoryName => $catServices)
                            <div x-show="selectedCategory === '{{ $categoryName }}'" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach($catServices as $service)
                                    <label class="block cursor-pointer group h-full">
                                        <input type="radio" name="service_type_id" value="{{ $service->id }}" x-model="selectedServiceId" @change="selectService('{{ $service->id }}', {{ (int) $service->price }}, '{{ addslashes($service->name) }}', {{ (int)($service->duration ?? 0) }})" class="sr-only">
                                        <div class="border-2 rounded-xl p-5 h-full flex flex-col justify-between transition-all duration-200 relative overflow-hidden" :class="selectedServiceId === '{{ $service->id }}' ? 'border-brand bg-orange-50/10 shadow-sm' : 'border-gray-200 hover:border-gray-300'">
                                            
                                            <div x-show="selectedServiceId === '{{ $service->id }}'" class="absolute top-0 right-0 bg-brand text-white px-3 py-1 rounded-bl-xl font-bold text-xs" style="display: none;">✓ Terpilih</div>
                                            
                                            <div>
                                                <h3 class="font-bold text-base text-gray-900 mb-1 pr-6">{{ $service->name }}</h3>
                                                @if($service->description)
                                                    <div class="text-xs text-gray-500 whitespace-pre-line line-clamp-3 mb-4">{{ $service->description }}</div>
                                                @endif
                                            </div>
                                            <div class="flex justify-between items-end mt-auto pt-3 border-t border-dashed border-gray-200">
                                                <p class="text-brand font-bold text-[17px] leading-none">Rp {{ number_format($service->price, 0, ',', '.') }}</p>
                                                @if(($service->duration ?? 0) > 0)
                                                    <span class="text-[10px] font-bold text-gray-500 bg-gray-100 px-2 py-1 rounded-md">{{ $service->duration }} Jam</span>
                                                @endif
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @endforeach
                    </div>

                    <div x-show="selectedCategory === ''" class="py-12 text-center border border-dashed border-gray-200 rounded-xl bg-gray-50 mb-4">
                        <div class="w-14 h-14 bg-white rounded-full flex items-center justify-center mx-auto mb-3 shadow-sm border border-gray-100">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"></path></svg>
                        </div>
                        <p class="text-gray-500 text-sm font-medium">Pilih <span class="font-bold text-gray-700">Jenis Acara</span> terlebih dahulu <br>untuk melihat daftar paket.</p>
                    </div>
                @endif
                <div class="mt-8 flex justify-end pt-4 border-t border-gray-100">
                    <button type="button" @click="nextStep()" class="bg-brand hover:opacity-90 text-white px-6 py-2.5 rounded-lg font-bold transition flex items-center gap-2 text-sm">Lanjut: Pilih Jadwal →</button>
                </div>
            </div>
            
            {{-- STEP 2: TANGGAL & WAKTU --}}
            <div x-show="step === 2" x-transition.opacity style="display: none;">
                <div class="mb-6 border-b border-gray-100 pb-4">
                    <h2 class="text-lg font-bold text-gray-900">Pilih Tanggal & Waktu</h2>
                    <p class="text-gray-500 text-sm mt-1">Tentukan tanggal dan jam berapa sesi foto atau acara Anda akan dimulai.</p>
                </div>

                {{-- KOTAK INFORMASI JAM BENTROK (Otomatis Muncul) --}}
                <div x-show="todayBookedSlots.length > 0" x-cloak class="mb-6 bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                    <div class="flex gap-3">
                        <svg class="w-5 h-5 text-yellow-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <div>
                            <p class="font-bold text-yellow-800 text-sm mb-1">Informasi Ketersediaan</p>
                            <p class="text-yellow-700 text-sm">Pada rentang hari acara Anda, tim kami sudah memiliki jadwal berikut:</p>
                            <ul class="mt-2 space-y-1">
                                <template x-for="(slot, index) in todayBookedSlots" :key="index">
                                    <li class="inline-block bg-white text-yellow-800 border border-yellow-200 text-xs font-bold px-2 py-1 rounded mr-2 mb-1 shadow-sm" x-text="slot.text"></li>
                                </template>
                            </ul>
                            <p class="text-yellow-700 text-xs mt-2 font-medium italic">Mohon pilih rentang jam yang berbeda agar tidak terjadi bentrok jadwal.</p>
                        </div>
                    </div>
                </div>

                <div class="mb-6 bg-amber-50 border border-amber-200 rounded-xl p-4 flex gap-3 text-sm text-amber-800">
                    <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <div>
                        <p class="font-bold mb-1">Penting untuk Pemesanan Hari Ini:</p>
                        <p>Jika Anda memilih jadwal untuk hari ini, mohon atur <b>Jam Mulai minimal 30 menit dari waktu sekarang</b> untuk memberikan waktu persiapan bagi tim kami.</p>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="inline-flex items-center gap-3 cursor-pointer bg-gray-50 border border-gray-200 py-2.5 px-4 rounded-lg hover:bg-gray-100 transition">
                        <input type="checkbox" x-model="multiDay" class="w-4 h-4 rounded border-gray-300 text-brand focus:ring-brand">
                        <span class="text-sm font-semibold text-gray-700">Acara berlangsung lebih dari 1 hari</span>
                    </label>
                </div>
                
                {{-- JADWAL DINAMIS (GABUNGAN SATU HARI & MULTI HARI) --}}
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-5">
                    
                    {{-- Form Tanggal Acara (Satu Hari) --}}
                    <div x-show="!multiDay" class="sm:col-span-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Acara <span class="text-red-500">*</span></label>
                        <input type="date" name="booking_date" x-model="bookingDate" :disabled="multiDay" :required="!multiDay" :min="todayDate" class="w-full h-11 rounded-lg border-gray-300 focus:border-brand focus:ring-brand shadow-sm text-sm">
                    </div>

                    {{-- Form Mulai & Selesai (Multi Hari) --}}
                    <div x-show="multiDay" class="sm:col-span-4" style="display: none;">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Mulai <span class="text-red-500">*</span></label>
                        <input type="date" name="start_date" x-model="startDate" :disabled="!multiDay" :required="multiDay" :min="todayDate" class="w-full h-11 rounded-lg border-gray-300 focus:border-brand focus:ring-brand shadow-sm text-sm">
                    </div>
                    <div x-show="multiDay" class="sm:col-span-4" style="display: none;">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Selesai <span class="text-red-500">*</span></label>
                        <input type="date" name="end_date" x-model="endDate" :disabled="!multiDay" :required="multiDay" :min="startDate || todayDate" class="w-full h-11 rounded-lg border-gray-300 focus:border-brand focus:ring-brand shadow-sm text-sm">
                    </div>

                    {{-- JAM MULAI (Digunakan Bersama, Tanpa Duplikasi DOM) --}}
                    <div :class="multiDay ? 'sm:col-span-4' : 'sm:col-span-6'">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            <span x-text="multiDay ? 'Jam Mulai Hari Pertama' : 'Jam Mulai Sesi Foto'"></span> <span class="text-red-500">*</span>
                        </label>
                        <div class="relative flex flex-col">
                            <div class="relative flex items-center">
                                {{-- DI SINI VALIDASI JAM OPERASIONAL FLATPIKR DIHILANGKAN, BEBAS 24 JAM --}}
                                <input type="text" 
                                    x-model="bookingTime" 
                                    x-init="flatpickr($el, { enableTime: true, noCalendar: true, dateFormat: 'H:i', time_24hr: true, disableMobile: true, minuteIncrement: 30, onChange: function(sd, ds) { bookingTime = ds } })" 
                                    required 
                                    placeholder="-- : --"
                                    class="w-full h-11 rounded-lg border-gray-300 focus:border-brand focus:ring-brand shadow-sm text-sm pl-4 pr-[75px] cursor-pointer bg-white">
                                
                                <div class="absolute right-3 flex items-center gap-1.5 text-gray-400 pointer-events-none">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span class="text-xs font-bold">WIB</span>
                                </div>
                            </div>
                            
                            {{-- TEKS BANTUAN PINTAR (MICROCOPY) --}}
                            <p x-show="(multiDay ? startDate : bookingDate) === todayDate && minTimeToday !== 'Tutup'" x-cloak class="text-[11.5px] text-orange-600 mt-2 font-medium leading-tight">
                                * Khusus hari ini, jam awal yang bisa dipilih adalah <span x-text="minTimeToday" class="font-bold"></span> WIB.
                            </p>
                            <p x-show="(multiDay ? startDate : bookingDate) === todayDate && minTimeToday === 'Tutup'" x-cloak class="text-[11.5px] text-red-600 mt-2 font-bold leading-tight">
                                * Maaf, jadwal hari ini tidak memungkinkan lagi (ganti hari). Silakan pilih besok.
                            </p>
                        </div>
                    </div>

                </div>

                <div class="mt-8 flex justify-between items-center">
                    <button type="button" @click="prevStep()" class="border border-gray-300 text-gray-600 hover:bg-gray-50 px-5 py-2.5 rounded-lg font-bold transition text-sm">← Kembali</button>
                    <button type="button" @click="nextStep()" class="bg-brand hover:opacity-90 text-white px-6 py-2.5 rounded-lg font-bold transition flex items-center gap-2 text-sm">Lanjut: Info Klien →</button>
                </div>
            </div>

            {{-- STEP 3: INFO KLIEN --}}
            <div x-show="step === 3" x-transition.opacity style="display: none;">
                <div class="mb-6 border-b border-gray-100 pb-4">
                    <h2 class="text-lg font-bold text-gray-900">Informasi Klien</h2>
                    <p class="text-gray-500 text-sm mt-1">Isi data diri dan detail lokasi acara untuk konfirmasi booking.</p>
                </div>

                {{-- Section 1: Data Diri Klien --}}
                <div class="mb-8">
                    <h3 class="text-[13px] font-bold text-gray-400 uppercase tracking-wider mb-4 border-b border-gray-100 pb-2">Data Diri Klien</h3>
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
                </div>

                {{-- Section 2: Waktu & Tempat Pelaksanaan --}}
                <div class="mb-6">
                    <h3 class="text-[13px] font-bold text-gray-400 uppercase tracking-wider mb-4 border-b border-gray-100 pb-2">Tempat & Pelaksanaan</h3>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Alamat Acara / Nama Gedung</label>
                        <input type="text" name="client_address" x-model="clientAddress" placeholder="Ketik nama gedung atau alamat lengkap..." class="w-full h-11 rounded-lg border border-gray-300 px-4 text-sm focus:border-brand focus:ring-brand shadow-sm">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Link Google Maps <span class="text-gray-400 font-normal">(Opsional)</span></label>
                        <input type="url" name="link_gmaps" x-model="linkGmaps" placeholder="https://maps.app.goo.gl/..." class="w-full h-11 rounded-lg border border-gray-300 px-4 text-sm focus:border-brand focus:ring-brand shadow-sm text-blue-600">
                        <p class="text-[11px] text-gray-400 mt-1.5">Membantu tim kami tiba di lokasi lebih akurat.</p>
                    </div>
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Catatan Khusus</label>
                        <textarea name="notes" x-model="notes" rows="3" placeholder="Permintaan khusus, tema dresscode, rundown singkat, dll." class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm resize-none focus:border-brand focus:ring-brand shadow-sm"></textarea>
                    </div>
                </div>

                <div class="mt-8 flex justify-between items-center">
                    <button type="button" @click="prevStep()" class="border border-gray-300 text-gray-600 hover:bg-gray-50 px-5 py-2.5 rounded-lg font-bold transition text-sm">← Kembali</button>
                    <button type="button" @click="nextStep()" class="bg-brand hover:opacity-90 text-white px-6 py-2.5 rounded-lg font-bold transition flex items-center gap-2 text-sm">Lanjut: Konfirmasi →</button>
                </div>
            </div>

            {{-- STEP 4: KONFIRMASI BOOKING --}}
            <div x-show="step === 4" x-transition.opacity style="display: none;">
                <div class="mb-6 border-b border-gray-100 pb-4">
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
                                        <p class="text-xs text-gray-500">Bayar sebagian sekarang, sisanya sebelum terima hasil.</p>
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
                        <div class="flex justify-between items-start gap-4"><span class="text-gray-500 font-medium shrink-0">Waktu Sesi</span><span class="font-bold text-right text-brand" x-text="sessionTimeText"></span></div>
                        <div x-show="selectedServiceDuration > 0" class="flex justify-between items-start gap-4"><span class="text-gray-500 font-medium shrink-0">Durasi</span><span class="font-bold text-right" x-text="selectedServiceDuration + ' Jam'"></span></div>
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
                    <button type="button" @click="prevStep()" class="border border-gray-300 text-gray-600 hover:bg-gray-50 px-5 py-2.5 rounded-lg font-semibold transition text-sm">← Kembali</button>
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
                    <p>1. Pemesanan (booking) dianggap sah hanya jika pembayaran DP/Lunas telah dikonfirmasi oleh Admin. <b>Jadwal Anda hanya akan kami amankan sementara maksimal 2 jam. Jika tidak ada konfirmasi pembayaran, sistem berhak membatalkan pesanan secara sepihak.</b></p>
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
    
    {{-- Script Flatpickr --}}
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    
    <script>
        function bookingWizard(initialId = '', initialPrice = 0, initialName = '', initialCategory = '', initialDuration = 0, bookedSlots = {}) {
            return {
                step: 1, errorMsg: '',
                selectedCategory: initialCategory,
                selectedServiceId: String(initialId), 
                unitPrice: Number(initialPrice), 
                selectedServiceName: initialName,
                selectedServiceDuration: Number(initialDuration),
                multiDay: false, bookingDate: '', startDate: '', endDate: '',

                // Data Jam Booking Yang Sudah Terisi
                bookedTimeSlots: bookedSlots,

                get todayDate() {
                    const d = new Date();
                    const year = d.getFullYear();
                    const month = String(d.getMonth() + 1).padStart(2, '0');
                    const day = String(d.getDate()).padStart(2, '0');
                    return `${year}-${month}-${day}`;
                },

                // Menghitung batas waktu paling awal (30 menit dari sekarang) khusus untuk hari ini
                get minTimeToday() {
                    const now = new Date();
                    const originalDate = now.getDate();
                    now.setMinutes(now.getMinutes() + 30);
                    
                    // Jika setelah ditambah 30 menit ternyata melewati tengah malam (ganti hari)
                    if (now.getDate() !== originalDate) {
                        return 'Tutup';
                    }

                    let h = now.getHours();
                    let m = now.getMinutes();

                    return String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0');
                },

                // Mendapatkan daftar jam ter-booking untuk SELURUH hari yang dipilih klien
                get todayBookedSlots() {
                    let slots = [];
                    let datesToCheck = [];
                    
                    if (this.multiDay && this.startDate && this.endDate) {
                        let currentD = new Date(this.startDate);
                        let endD = new Date(this.endDate);
                        while(currentD <= endD) {
                            let y = currentD.getFullYear();
                            let m = String(currentD.getMonth() + 1).padStart(2, '0');
                            let d = String(currentD.getDate()).padStart(2, '0');
                            datesToCheck.push(`${y}-${m}-${d}`);
                            currentD.setDate(currentD.getDate() + 1);
                        }
                    } else if (!this.multiDay && this.bookingDate) {
                        datesToCheck.push(this.bookingDate);
                    }

                    // Kumpulkan semua slot sibuk di tanggal-tanggal tersebut
                    for (let d of datesToCheck) {
                        if (this.bookedTimeSlots[d]) {
                            for (let slot of this.bookedTimeSlots[d]) {
                                slots.push({
                                    text: `Tgl ${this.formatDateLocal(d)}: ${slot.text}`
                                });
                            }
                        }
                    }
                    return slots;
                },

                bookingTime: '',

                clientName: '{{ old('client_name') }}', clientContact: '{{ old('client_contact') }}',
                clientEmail: '{{ old('client_email') }}', clientInstagram: '{{ old('client_instagram') }}',
                clientAddress: '{{ old('client_address') }}', notes: '{{ old('notes') }}',
                linkGmaps: '',
                
                paymentOption: 'DP', 
                agreeTnC: false, showTermsModal: false,
                
                selectService(id, price, name, duration) {
                    this.selectedServiceId = String(id); 
                    this.unitPrice = price; 
                    this.selectedServiceName = name; 
                    this.selectedServiceDuration = Number(duration);
                    this.errorMsg = '';
                },

                timeToMinutes(timeStr) {
                    if (!timeStr) return 0;
                    if (timeStr === 'Tutup') return 9999;
                    let parts = timeStr.split(':');
                    return parseInt(parts[0]) * 60 + parseInt(parts[1]);
                },
                
                nextStep() {
                    if (this.step === 1 && !this.selectedCategory) { this.showError('Pilih jenis acara terlebih dahulu.'); return; }
                    if (this.step === 1 && !this.selectedServiceId) { this.showError('Pilih paket dahulu.'); return; }
                    
                    if (this.step === 2) {
                        if (!this.multiDay && !this.bookingDate) { this.showError('Pilih tanggal acara.'); return; }
                        if (this.multiDay && (!this.startDate || !this.endDate)) { this.showError('Pilih tanggal mulai dan selesai.'); return; }
                        if (!this.bookingTime) { this.showError('Pilih jam mulai pemotretan / acara.'); return; }
                        
                        let checkDate = this.multiDay ? this.startDate : this.bookingDate;
                        let startMin = this.timeToMinutes(this.bookingTime);
                        
                        // LOGIKA 2: TIME BLOCKING KHUSUS HARI INI BERSAMA MICROCOPY
                        if (checkDate === this.todayDate) {
                            let minAllowedMin = this.timeToMinutes(this.minTimeToday);
                            
                            if (this.minTimeToday === 'Tutup') {
                                this.showError('Maaf, jadwal hari ini tidak memungkinkan lagi (ganti hari). Silakan pilih besok atau seterusnya.');
                                return;
                            }
                            
                            if (startMin < minAllowedMin) {
                                this.showError(`Untuk pemesanan hari ini, jam paling awal yang bisa Anda pilih adalah pukul ${this.minTimeToday} WIB untuk waktu persiapan tim.`);
                                return;
                            }
                        }

                        // LOGIKA 3: CEK TABRAKAN JADWAL MULTI-HARI (Mengecek ke seluruh hari yang dipilih)
                        let datesToCheck = [];
                        if (!this.multiDay) {
                            datesToCheck.push(this.bookingDate);
                        } else {
                            let currentD = new Date(this.startDate);
                            let endD = new Date(this.endDate);
                            while(currentD <= endD) {
                                let y = currentD.getFullYear();
                                let m = String(currentD.getMonth() + 1).padStart(2, '0');
                                let d = String(currentD.getDate()).padStart(2, '0');
                                datesToCheck.push(`${y}-${m}-${d}`);
                                currentD.setDate(currentD.getDate() + 1);
                            }
                        }

                        let newEndMin = startMin + (this.selectedServiceDuration * 60);

                        for (let d of datesToCheck) {
                            if (this.bookedTimeSlots[d]) {
                                for (let slot of this.bookedTimeSlots[d]) {
                                    let existStartMin = this.timeToMinutes(slot.start);
                                    let existEndMin = this.timeToMinutes(slot.end);

                                    // Rumus Tabrakan: (Start Baru < End Lama) DAN (End Baru > Start Lama)
                                    if (startMin < existEndMin && newEndMin > existStartMin) {
                                        this.showError(`Waktu bertabrakan! Tim kami sudah memiliki jadwal pukul ${slot.text} pada tanggal ${this.formatDateLocal(d)}. Mohon pilih jam atau tanggal lain.`);
                                        return;
                                    }
                                }
                            }
                        }

                        // Bersihkan field agar tidak rancu
                        if (!this.multiDay) {
                            this.startDate = '';
                            this.endDate = '';
                        } else {
                            this.bookingDate = '';
                        }
                    }
                    
                    if (this.step === 3 && (!this.clientName || !this.clientContact || !this.clientAddress)) { this.showError('Isi kontak yang wajib (*).'); return; }
                    
                    this.errorMsg = ''; this.step++; window.scrollTo({ top: 0, behavior: 'smooth' });
                },
                
                prevStep() { if (this.step > 1) { this.step--; this.errorMsg = ''; window.scrollTo({ top: 0, behavior: 'smooth' }); } },
                showError(msg) { this.errorMsg = msg; window.scrollTo({ top: 0, behavior: 'smooth' }); },
                formatCurrency(value) { return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value || 0); },
                
                formatDateLocal(dateStr) {
                    if (!dateStr || dateStr === '-') return '-';
                    const parts = dateStr.split('-');
                    if (parts.length !== 3) return dateStr;
                    const year = parts[0];
                    const monthIndex = parseInt(parts[1], 10) - 1;
                    const day = parseInt(parts[2], 10);
                    const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    return `${day} ${months[monthIndex]} ${year}`;
                },

                get scheduleText() { 
                    if (this.multiDay) {
                        return (this.startDate ? this.formatDateLocal(this.startDate) : '-') + ' s/d ' + (this.endDate ? this.formatDateLocal(this.endDate) : '-');
                    }
                    return this.bookingDate ? this.formatDateLocal(this.bookingDate) : '-'; 
                },

                get sessionTimeText() {
                    if (!this.bookingTime) return '-';
                    if (!this.selectedServiceDuration || this.selectedServiceDuration <= 0) return this.bookingTime + ' WIB';

                    let parts = this.bookingTime.split(':');
                    let date = new Date();
                    date.setHours(parseInt(parts[0]), parseInt(parts[1]), 0);
                    date.setHours(date.getHours() + parseInt(this.selectedServiceDuration));

                    let endHours = String(date.getHours()).padStart(2, '0');
                    let endMins = String(date.getMinutes()).padStart(2, '0');

                    return this.bookingTime + ' - ' + endHours + ':' + endMins + ' WIB';
                },
                
                submitForm() {
                    if (!this.agreeTnC) { this.showError('Centang persetujuan dahulu.'); return; }
                    document.getElementById('hidden_payment_type').value = this.paymentOption;
                    
                    const hiddenTimeInput = document.querySelector('input[type="hidden"][name="booking_time"]');
                    if (hiddenTimeInput) {
                        hiddenTimeInput.value = this.bookingTime;
                    }

                    document.getElementById('booking-form').submit();
                }
            }
        }
    </script>
</body>
</html>