{{-- resources/views/booking/pembayaran.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran — {{ $bookingCode }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .text-brand { color: #f59e0b; }
        .bg-brand { background-color: #fbbf59; }
        .hover-bg-brand:hover { background-color: #f7a934; }
    </style>
</head>
<body class="min-h-screen bg-gray-50 font-sans antialiased pb-12">
    @php
        $tglLayanan = $booking->booking_date ?? $booking->start_date;
        $isLunas = strtoupper($booking->payment_type) === 'LUNAS' || strtoupper($booking->payment_type) === 'PELUNASAN';
        
        // Logika Dinamis Label Total Pembayaran
        if ($booking->payment_status === 'Down Payment') {
            $paymentLabelText = 'Total Harus Dibayar (PELUNASAN)';
        } else {
            $paymentLabelText = $isLunas ? 'Total Harus Dibayar (LUNAS)' : 'Total Harus Dibayar (DP 30%)';
        }
    @endphp

    <div class="bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between shadow-sm sticky top-0 z-50">
        <a href="{{ route('booking.public.show', $ownerId) }}" class="text-sm font-medium text-gray-500 hover:text-gray-900 flex items-center gap-2 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Beranda
        </a>
        <h1 class="text-[15px] font-extrabold text-gray-900">{{ $companySetting?->company_name ?? $owner->name }}</h1>
    </div>

    <div class="max-w-xl mx-auto px-4 pt-12" x-data="{ showPelunasanSummary: {{ $booking->payment_status === 'Down Payment' ? 'true' : 'false' }} }">

        {{-- ========================================================== --}}
        {{-- VIEW KHUSUS: PELUNASAN BOOKING (Tampil Jika Status DP) --}}
        {{-- ========================================================== --}}
        <div x-show="showPelunasanSummary" x-cloak>
            <div class="text-center mb-8">
                <h1 class="text-[26px] font-bold text-gray-900 tracking-tight">Pelunasan Booking</h1>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 sm:p-8 mb-6">
                <h2 class="text-[18px] font-bold text-gray-900 mb-2">Selesaikan Sisa Pembayaran</h2>
                <p class="text-[14px] text-gray-600 leading-relaxed mb-6">
                    Halaman ini belum menandai pembayaran sebagai berhasil. Pembayaran baru diproses setelah kamu menekan tombol pembayaran dan menyelesaikan instruksi dari payment gateway atau upload bukti transfer.
                </p>

                <div class="bg-gray-50 rounded-xl p-4 flex justify-between items-center border border-gray-100 mb-8" x-data="{ copied: false, copyKode() { navigator.clipboard.writeText('{{ $bookingCode }}'); this.copied = true; setTimeout(() => this.copied = false, 1500); } }">
                    <div>
                        <p class="text-[12px] font-medium text-gray-500 uppercase tracking-wider mb-1">Kode Booking</p>
                        <p class="font-mono font-bold text-gray-900 text-[16px]">{{ $bookingCode }}</p>
                    </div>
                    <button type="button" @click="copyKode()" class="text-brand font-semibold text-[14px] hover:text-orange-600 transition flex items-center gap-1">
                        <svg x-show="!copied" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        <span x-show="!copied">Salin</span>
                        <span x-show="copied" x-cloak class="text-green-600 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Tersalin
                        </span>
                    </button>
                </div>

                <h3 class="font-bold text-gray-900 mb-4 text-[16px]">Rincian Pelunasan</h3>
                <div class="space-y-4 text-[14px]">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center border-b border-gray-100 pb-3">
                        <span class="text-gray-500 mb-1 sm:mb-0">Nama</span>
                        <span class="font-semibold text-gray-900">{{ $booking->client_name }}</span>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center border-b border-gray-100 pb-3">
                        <span class="text-gray-500 mb-1 sm:mb-0">Tanggal sesi</span>
                        <span class="font-semibold text-gray-900">{{ $tglLayanan ? \Carbon\Carbon::parse($tglLayanan)->locale('id')->isoFormat('D MMM YYYY') : '-' }}</span>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center border-b border-gray-100 pb-3">
                        <span class="text-gray-500 mb-1 sm:mb-0">Paket</span>
                        <span class="font-semibold text-gray-900">{{ $booking->serviceType->name ?? '-' }}</span>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center border-b border-gray-100 pb-3">
                        <span class="text-gray-500 mb-1 sm:mb-0">Harga paket</span>
                        <span class="font-semibold text-gray-900">Rp {{ number_format($booking->unit_price, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center border-b border-gray-100 pb-3">
                        <span class="text-gray-500 mb-1 sm:mb-0">Total tagihan</span>
                        <span class="font-semibold text-gray-900">Rp {{ number_format($booking->total, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center border-b border-gray-100 pb-3">
                        <span class="text-gray-500 mb-1 sm:mb-0">Sudah dibayar</span>
                        <span class="font-semibold text-gray-900">Rp {{ number_format($booking->paid_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center pt-2">
                        <span class="font-bold text-gray-900 mb-1 sm:mb-0">Sisa pelunasan</span>
                        <span class="font-extrabold text-brand text-[18px]">Rp {{ number_format($booking->remaining, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="mt-10">
                    <h3 class="font-bold text-gray-900 mb-2 text-[16px]">Mulai pembayaran pelunasan</h3>
                    <p class="text-[14px] text-gray-600 leading-relaxed mb-6">
                        Bayar sisa <strong>Rp {{ number_format($booking->remaining, 0, ',', '.') }}</strong>. Setelah pembayaran berhasil atau dikonfirmasi admin, status booking akan berubah menjadi lunas.
                    </p>
                    
                    <button @click="showPelunasanSummary = false" class="w-full h-[52px] rounded-xl bg-gray-900 hover:bg-black text-white font-bold text-[15px] flex items-center justify-center gap-2 transition-colors shadow-md">
                        Bayar Pelunasan Sekarang &rarr;
                    </button>
                    
                    <p class="text-[13px] text-gray-500 text-center mt-4">
                        Mau bayar nanti? Kamu bisa buka ulang link pelunasan ini kapan saja sebelum sesi.
                    </p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-4 mb-10">
                <a href="{{ route('booking.check.page') }}" class="flex-1 h-12 rounded-xl bg-white border border-gray-200 text-gray-700 font-bold text-[14px] flex items-center justify-center hover:bg-gray-50 transition shadow-sm">
                    Track Status Booking
                </a>
                <a href="{{ route('booking.public.show', $ownerId) }}" class="flex-1 h-12 rounded-xl bg-white border border-gray-200 text-gray-700 font-bold text-[14px] flex items-center justify-center hover:bg-gray-50 transition shadow-sm">
                    Kembali ke Beranda
                </a>
            </div>
        </div>

        {{-- ========================================================== --}}
        {{-- VIEW REGULER: FORM TRANSFER BANK & UPLOAD BUKTI --}}
        {{-- ========================================================== --}}
        <div x-show="!showPelunasanSummary" x-cloak>
            
            {{-- Tombol Kembali Jika Berasal dari View Pelunasan --}}
            <template x-if="{{ $booking->payment_status === 'Down Payment' ? 'true' : 'false' }}">
                <button @click="showPelunasanSummary = true" class="text-sm font-semibold text-gray-500 hover:text-gray-900 flex items-center gap-1.5 mb-6 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Kembali ke Rincian Pelunasan
                </button>
            </template>

            <div class="text-center mb-8">
                <div class="w-14 h-14 bg-orange-50 text-brand rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">
                    {{ $booking->payment_status === 'Lunas' ? 'Pembayaran Berhasil' : 'Selesaikan Pembayaran' }}
                </h1>
                <p class="text-gray-500 text-sm mt-1">Booking <span class="font-bold text-brand">{{ $bookingCode }}</span></p>
            </div>

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 rounded-2xl px-5 py-4 mb-6 text-sm font-medium text-center">
                    {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-600 rounded-2xl px-5 py-3 mb-6 text-sm font-medium">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">
                    {{ $paymentLabelText }}
                </p>
                <p class="text-4xl font-extrabold text-brand mb-1">
                    @if($booking->payment_status === 'Lunas')
                        LUNAS
                    @else
                        Rp {{ number_format($amountToPay, 0, ',', '.') }}
                    @endif
                </p>
                <p class="text-xs text-gray-400 mb-6">Kode Booking: {{ $bookingCode }}</p>
                
                <div class="flex justify-between border-t border-gray-100 pt-5 text-sm">
                    <div>
                        <p class="text-gray-400 text-[11px] mb-0.5">Nama Klien</p>
                        <p class="font-semibold text-gray-900">{{ $booking->client_name }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-gray-400 text-[11px] mb-0.5">Tanggal Sesi</p>
                        <p class="font-semibold text-gray-900">
                            {{ $tglLayanan ? \Carbon\Carbon::parse($tglLayanan)->locale('id')->isoFormat('D MMM YYYY') : '-' }}
                        </p>
                    </div>
                </div>
            </div>

            @if($booking->payment_status !== 'Lunas' && $booking->payment_status !== 'Tunggu Konfirmasi')
                
                {{-- LOGIKA DINAMIS QRIS ATAU TRANSFER BANK --}}
                @if($companySetting?->payment_method === 'qris' && $companySetting?->qris_image)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6 text-center">
                        <h3 class="font-bold text-gray-900 mb-4 text-[15px]">Scan QRIS untuk Membayar</h3>
                        <div class="bg-white p-2 rounded-xl inline-block border border-gray-200 shadow-sm mb-3">
                            <img src="{{ asset('storage/' . $companySetting->qris_image) }}" alt="QRIS" class="w-64 h-64 object-contain">
                        </div>
                        <p class="text-sm text-gray-500">Gunakan aplikasi M-Banking atau e-Wallet Anda untuk melakukan pembayaran.</p>
                    </div>
                @else
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                        <h3 class="font-bold text-gray-900 mb-4 text-[15px]">Transfer Bank</h3>
                        <div class="space-y-4">
                            @if($companySetting?->bank_name && $companySetting?->bank_account)
                                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 text-sm">
                                    <p class="text-gray-500 mb-1">Bank <span class="font-bold text-gray-900">{{ $companySetting->bank_name }}</span></p>
                                    <p class="text-lg font-bold text-gray-900 tracking-wider mb-1">{{ $companySetting->bank_account }}</p>
                                    @if($companySetting->bank_holder)
                                        <p class="text-[13px] text-gray-600">a.n <span class="font-semibold text-gray-800">{{ $companySetting->bank_holder }}</span></p>
                                    @endif
                                </div>
                            @endif
                            @if($companySetting?->bank_name_2 && $companySetting?->bank_account_2)
                                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 text-sm">
                                    <p class="text-gray-500 mb-1">Bank <span class="font-bold text-gray-900">{{ $companySetting->bank_name_2 }}</span></p>
                                    <p class="text-lg font-bold text-gray-900 tracking-wider mb-1">{{ $companySetting->bank_account_2 }}</p>
                                    @if($companySetting->bank_holder_2)
                                        <p class="text-[13px] text-gray-600">a.n <span class="font-semibold text-gray-800">{{ $companySetting->bank_holder_2 }}</span></p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if($companySetting?->payment_instruction)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
                        <h3 class="font-bold text-gray-900 mb-2 text-[14px]">Informasi Pembayaran</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $companySetting->payment_instruction }}</p>
                    </div>
                @endif

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                    <h3 class="font-bold text-gray-900 mb-1 text-[16px]">Upload Bukti Pembayaran</h3>
                    <p class="text-[13px] text-gray-500 mb-5">Foto / screenshot struk transfer. Maks. 5 MB (JPG, PNG)</p>
                    <form action="{{ route('booking.public.upload-proof', ['ownerId' => $ownerId, 'bookingId' => $booking->id]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="client_instagram" value="{{ $booking->client_instagram }}">
                        <div class="mb-4">
                            <label class="block text-[13px] font-semibold text-gray-800 mb-1.5">Email Booking <span class="text-red-500">*</span></label>
                            <input type="email" name="client_email" required placeholder="email yang kamu pakai saat booking" value="{{ $booking->client_email }}"
                                class="w-full h-12 rounded-xl border border-gray-300 px-4 text-sm focus:border-brand focus:ring-brand shadow-sm">
                            <p class="text-[12px] text-gray-400 mt-1.5">Masukkan email yang kamu gunakan saat booking untuk verifikasi.</p>
                        </div>
                        <div x-data="{ fileName: '' }" class="mb-6">
                            <label class="block border-2 border-dashed border-gray-300 rounded-xl p-8 text-center cursor-pointer hover:border-brand hover:bg-orange-50/10 transition">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M4 6h16a1 1 0 011 1v10a1 1 0 01-1 1H4a1 1 0 01-1-1V7a1 1 0 011-1z" />
                                        <circle cx="9" cy="9" r="1.5" fill="currentColor" stroke="none" />
                                    </svg>
                                    <span class="text-[14px] font-semibold text-gray-700" x-text="fileName ? fileName : 'Klik atau drag & drop foto bukti transfer'"></span>
                                </div>
                                <input type="file" name="payment_proof" required accept="image/*" @change="fileName = $event.target.files[0].name" class="hidden">
                            </label>
                        </div>
                        <button type="submit" class="w-full h-12 rounded-xl bg-brand hover-bg-brand text-white font-bold text-[14px] flex items-center justify-center gap-2 transition-colors shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                            Kirim Bukti Pembayaran
                        </button>
                    </form>
                </div>
                
            @elseif($booking->payment_status === 'Tunggu Konfirmasi')
                <div class="bg-white border border-gray-200 rounded-2xl p-8 text-center mb-6 shadow-sm">
                    <div class="w-16 h-16 bg-green-100 text-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <h3 class="text-xl font-extrabold text-gray-900 mb-2">Bukti Pembayaran Terkirim!</h3>
                    <p class="text-sm text-gray-500 mb-6 leading-relaxed px-4">
                        Terima kasih, bukti transfer dan data Anda telah kami terima. Admin akan memverifikasi pembayaran Anda dalam waktu maksimal 1x24 jam.
                    </p>
                    <div class="bg-gray-50 rounded-xl p-5 text-left border border-gray-100 max-w-sm mx-auto">
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-3 border-b border-gray-200 pb-2">Data Konfirmasi Anda</p>
                        <div class="space-y-2.5 text-sm">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500">Email:</span>
                                <span class="font-semibold text-gray-900">{{ $booking->client_email }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500">Pembayaran:</span>
                                <span class="font-semibold text-gray-900">{{ $isLunas ? 'Lunas Penuh' : 'Down Payment (DP)' }}</span>
                            </div>
                            <div class="flex justify-between items-center pt-3 mt-1 border-t border-gray-200">
                                <span class="text-gray-500 font-medium">Status:</span>
                                <span class="font-bold text-orange-500 bg-orange-50 px-2 py-1 rounded-md text-[12px] uppercase tracking-wide animate-pulse">Tunggu Konfirmasi</span>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('booking.public.show', $ownerId) }}" class="inline-block mt-8 px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-sm transition">
                        Kembali ke Beranda
                    </a>
                </div>
            @endif

            <p class="text-center text-[11px] text-gray-400 mt-8">
                Pembayaran diverifikasi dalam 1x24 jam.<br>Powered by <b>BookPhoto</b>
            </p>
        </div>
    </div>
</body>
</html>