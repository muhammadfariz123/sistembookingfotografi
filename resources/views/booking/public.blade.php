<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Booking — {{ $companySetting?->company_name ?? $owner->name }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#eef2ff] font-sans antialiased">
    {{-- HEADER --}}
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white py-8 px-4 text-center">
        @if($companySetting?->logo_path)
            <img src="{{ Storage::url($companySetting->logo_path) }}"
                 class="w-16 h-16 rounded-full object-cover mx-auto mb-3 border-2 border-white/40" alt="Logo">
        @endif
        <h1 class="text-2xl font-bold">Form Booking Layanan</h1>
        <p class="text-white/70 text-sm mt-1">Isi data di bawah ini untuk mengirim booking ke admin.</p>
    </div>

    <div class="max-w-2xl mx-auto px-4 py-8" x-data="publicBookingForm()">
        {{-- ERROR --}}
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-2xl px-5 py-4 mb-6">
                <ul class="text-sm text-red-600 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('booking.public.store', $ownerId) }}" id="booking-form">
            @csrf
            
            {{-- STEP 1: DATA KLIEN --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-5">
                <div class="flex items-start gap-3 mb-5">
                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-sm shrink-0">1</div>
                    <div>
                        <h2 class="font-semibold text-gray-900 text-[16px]">Data Klien</h2>
                        <p class="text-gray-500 text-sm">Isi informasi dasar klien terlebih dahulu</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Nama Klien <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="client_name" required
                            value="{{ old('client_name') }}"
                            placeholder="Masukkan nama klien"
                            class="w-full h-[44px] rounded-xl border border-gray-300 px-4 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('client_name') border-red-400 @enderror">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Kontak Klien <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="client_contact" required
                            value="{{ old('client_contact') }}"
                            placeholder="Nomor telepon atau email"
                            class="w-full h-[44px] rounded-xl border border-gray-300 px-4 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('client_contact') border-red-400 @enderror">
                        <p class="text-xs text-gray-400 mt-1">Disarankan isi nomor WhatsApp agar mudah dihubungi.</p>
                    </div>
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Alamat / Lokasi Acara</label>
                    <input type="text" name="client_address"
                        value="{{ old('client_address') }}"
                        placeholder="Alamat klien / lokasi acara"
                        class="w-full h-[44px] rounded-xl border border-gray-300 px-4 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>

            {{-- STEP 2: PILIH LAYANAN --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-5">
                <div class="flex items-start gap-3 mb-5">
                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-sm shrink-0">2</div>
                    <div>
                        <h2 class="font-semibold text-gray-900 text-[16px]">Pilih Paket Layanan</h2>
                        <p class="text-gray-500 text-sm">Pilih salah satu layanan yang tersedia</p>
                    </div>
                </div>
                @if($services->isEmpty())
                    <div class="py-8 text-center text-gray-400 text-sm">Belum ada layanan tersedia saat ini.</div>
                @else
                    <div class="space-y-3">
                        @foreach($services as $service)
                            <label class="block cursor-pointer">
                                <input type="radio" name="service_type_id" value="{{ $service->id }}"
                                    x-model="selectedServiceId"
                                    @change="selectService({{ $service->id }}, {{ (int)$service->price }})"
                                    {{ old('service_type_id') == $service->id ? 'checked' : '' }}
                                    class="sr-only peer">
                                <div class="border-2 rounded-xl p-4 transition peer-checked:border-blue-500 peer-checked:bg-blue-50 border-gray-200 hover:border-blue-300">
                                    <div class="flex items-center gap-3">
                                        <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 transition"
                                            :class="selectedServiceId == '{{ $service->id }}' ? 'border-blue-500 bg-blue-500' : 'border-gray-300'">
                                            <div x-show="selectedServiceId == '{{ $service->id }}'" class="w-2 h-2 rounded-full bg-white"></div>
                                        </div>
                                        <p class="font-semibold text-[15px]" :class="selectedServiceId == '{{ $service->id }}' ? 'text-blue-700' : 'text-gray-900'">
                                            {{ $service->name }}
                                        </p>
                                    </div>
                                    @if($service->description)
                                        <div class="mt-3 ml-8">
                                            <p class="text-[10px] font-semibold tracking-wider text-gray-400 uppercase mb-1">Deskripsi</p>
                                            <div class="bg-gray-50 rounded-lg px-3 py-2 text-sm text-gray-600 whitespace-pre-line">{{ $service->description }}</div>
                                        </div>
                                    @endif
                                    <div class="mt-3 ml-8">
                                        <p class="text-[10px] font-semibold tracking-wider text-gray-400 uppercase mb-1">Harga</p>
                                        <span class="inline-block px-3 py-1 rounded-lg text-sm font-semibold"
                                            :class="selectedServiceId == '{{ $service->id }}' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700'">
                                            Rp {{ number_format($service->price, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    <div x-show="serviceError" class="mt-2 text-sm text-red-500 font-medium" style="display: none;">Jenis layanan wajib dipilih.</div>
                @endif
            </div>
            
            {{-- STEP 3: DETAIL BOOKING --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-5">
                <div class="flex items-start gap-3 mb-5">
                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-sm shrink-0">3</div>
                    <div>
                        <h2 class="font-semibold text-gray-900 text-[16px]">Detail Booking</h2>
                        <p class="text-gray-500 text-sm">Isi tanggal dan waktu acara</p>
                    </div>
                </div>
                
                <div class="mt-4">
                    <div class="flex flex-col justify-center">
                        {{-- Total biaya — tampil jika layanan sudah dipilih --}}
                        <template x-if="selectedServiceId && unitPrice > 0">
                            <div class="bg-blue-600 rounded-xl px-5 py-4 text-white shadow-md">
                                <p class="text-[11px] font-semibold uppercase tracking-wider opacity-80 mb-1">Total Biaya Paket</p>
                                <p class="text-[28px] font-bold leading-none mb-3" x-text="formatCurrency(estimatedTotal)"></p>
                                
                                <div class="space-y-2 border-t border-white/20 pt-3 text-[12px] opacity-90">
                                    <p class="flex items-start gap-2">
                                        <span class="font-bold mt-0.5">•</span>
                                        <span>DP Minimal 30%: <b class="font-bold text-yellow-300" x-text="formatCurrency(estimatedTotal * 0.3)"></b> (Jatuh tempo 1-2 hari setelah Invoice dikirim)</span>
                                    </p>
                                    <p class="flex items-start gap-2">
                                        <span class="font-bold mt-0.5">•</span>
                                        <span>Pelunasan Sisa Tagihan wajib diselesaikan <b class="font-bold text-yellow-300">maksimal H-7</b> sebelum hari acara.</span>
                                    </p>
                                </div>
                            </div>
                        </template>
                        {{-- Placeholder sebelum pilih layanan --}}
                        <template x-if="!selectedServiceId || unitPrice === 0">
                            <div class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-3">
                                <p class="text-[13px] text-gray-400">Pilih layanan terlebih dahulu untuk melihat total biaya dan ketentuan pembayaran.</p>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Multi day toggle --}}
                <div class="mt-5">
                    <label class="inline-flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                        <input type="checkbox" x-model="multiDay" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        Acara lebih dari 1 hari
                    </label>
                </div>
                
                <div x-show="!multiDay" class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Acara <span class="text-red-500">*</span></label>
                        <input type="date" name="booking_date" :required="!multiDay" value="{{ now()->format('Y-m-d') }}" min="{{ now()->format('Y-m-d') }}" class="w-full h-[44px] rounded-xl border border-gray-300 px-4 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Waktu Mulai</label>
                        <input type="time" name="booking_time" class="w-full h-[44px] rounded-xl border border-gray-300 px-4 text-sm">
                    </div>
                </div>
                
                <div x-show="multiDay" class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-4" style="display: none;">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Mulai <span class="text-red-500">*</span></label>
                        <input type="date" name="start_date" :required="multiDay" value="{{ now()->format('Y-m-d') }}" min="{{ now()->format('Y-m-d') }}" class="w-full h-[44px] rounded-xl border border-gray-300 px-4 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Selesai <span class="text-red-500">*</span></label>
                        <input type="date" name="end_date" :required="multiDay" value="{{ now()->format('Y-m-d') }}" min="{{ now()->format('Y-m-d') }}" class="w-full h-[44px] rounded-xl border border-gray-300 px-4 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Waktu Mulai</label>
                        <input type="time" name="booking_time" class="w-full h-[44px] rounded-xl border border-gray-300 px-4 text-sm">
                    </div>
                </div>
            </div>
            
            {{-- STEP 4: CATATAN --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                <div class="flex items-start gap-3 mb-5">
                    <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center font-bold text-sm shrink-0">4</div>
                    <div>
                        <h2 class="font-semibold text-gray-900 text-[16px]">Catatan Tambahan</h2>
                        <p class="text-gray-500 text-sm">Isi bila ada permintaan khusus</p>
                    </div>
                </div>
                <textarea name="notes" rows="3" placeholder="Catatan tambahan..." class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm resize-none focus:ring-2 focus:ring-blue-500">{{ old('notes') }}</textarea>
            </div>
            
            <button type="button" @click="handleSubmit()" class="w-full h-[52px] rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold text-[16px] shadow-lg transition hover:scale-[1.02]">
                Kirim Booking ke Admin
            </button>
        </form>
    </div>
    
    <script>
        function publicBookingForm() {
            return {
                selectedServiceId: '{{ old('service_type_id', '') }}',
                unitPrice: 0,
                multiDay: false,
                serviceError: false,
                get estimatedTotal() { return this.unitPrice },
                selectService(id, price) {
                    this.selectedServiceId = String(id);
                    this.unitPrice = price;
                    this.serviceError = false;
                },
                formatCurrency(value) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(value || 0) },
                handleSubmit() {
                    if (!this.selectedServiceId) {
                        this.serviceError = true;
                        window.scrollTo({ top: 400, behavior: 'smooth' });
                        return;
                    }
                    document.getElementById('booking-form').submit();
                }
            }
        }
    </script>
</body>
</html>