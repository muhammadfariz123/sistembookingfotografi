{{-- resources/views/booking/payment-success.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Booking — {{ $bookingCode }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .text-brand {
            color: #f59e0b; /* Warna Oranye */
        }
        .bg-brand {
            background-color: #f59e0b;
        }
        .hover-bg-brand:hover {
            background-color: #d97706;
        }
        
        /* Menyembunyikan elemen tidak penting saat tombol 'Unduh/Print' ditekan */
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background-color: white;
            }
            .print-shadow-none {
                box-shadow: none !important;
                border: 1px solid #e5e7eb !important;
            }
        }
    </style>
    <!-- Alpine.js untuk fitur Copy -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen bg-gray-50 font-sans antialiased pb-12">
    @php
        $tglLayanan = $booking->booking_date ?? $booking->start_date;
        
        // Cek apakah ada riwayat transaksi yang baru di-upload dan sedang menunggu konfirmasi
        $hasPendingTx = \App\Models\PaymentTransaction::where('booking_id', $booking->id)
                            ->where('payment_status', 'Tunggu Konfirmasi')
                            ->exists();
        
        // Flag utama: Aktif jika booking masih 'Tunggu Konfirmasi' ATAU ada pelunasan baru yang sedang diverifikasi
        $isWaitingVerification = $hasPendingTx || $booking->payment_status === 'Tunggu Konfirmasi';
    @endphp
    
    {{-- TOPBAR --}}
    <div class="bg-white border-b border-gray-100 px-4 py-4 flex items-center justify-between shadow-sm sticky top-0 z-50 no-print">
        <a href="{{ route('booking.public.show', $ownerId) }}"
            class="text-[14px] font-medium text-gray-500 hover:text-gray-900 flex items-center gap-1.5 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali ke Beranda
        </a>
        <h1 class="text-[14px] font-extrabold text-gray-900">{{ $companySetting?->company_name ?? $owner->name }}</h1>
    </div>

    <div class="max-w-[500px] mx-auto px-4 pt-8"
        x-data="{ copied: false, copyKode() { navigator.clipboard.writeText('{{ $bookingCode }}'); this.copied = true; setTimeout(() => this.copied = false, 1500); } }">
        
        {{-- KARTU 1: ICON + PESAN --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center mb-6 print-shadow-none relative overflow-hidden">
            
            @if($isWaitingVerification)
                {{-- STATUS: MENUNGGU VERIFIKASI (Baik DP maupun Pelunasan) --}}
                <div class="w-16 h-16 bg-orange-50 text-brand rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <p class="text-[11px] font-bold text-brand uppercase tracking-wider mb-2">Menunggu Verifikasi Admin</p>
                <h2 class="text-[22px] font-extrabold text-gray-900 mb-3">Bukti Transfer Dikirim!</h2>
                <p class="text-[14px] text-gray-500 leading-relaxed px-2 mb-6">
                    Bukti transfer kamu sudah kami terima. Admin <strong>{{ $companySetting?->company_name ?? $owner->name }}</strong> akan memverifikasi pembayaran dalam 1×24 jam. Kamu akan mendapat notifikasi email setelah dikonfirmasi.
                </p>

            @elseif($booking->payment_status === 'Lunas')
                {{-- STATUS: LUNAS PENUH --}}
                <div class="w-16 h-16 bg-emerald-50 text-[#059669] rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <p class="text-[11px] font-bold text-[#059669] uppercase tracking-wider mb-2">Pembayaran Lunas!</p>
                <h2 class="text-[22px] font-extrabold text-gray-900 mb-3">Booking Terkonfirmasi 🎉</h2>
                <p class="text-[14px] text-gray-500 leading-relaxed px-2 mb-6">
                    Pembayaran kamu sudah lunas dan jadwal sesi foto sudah terkunci. Sampai jumpa di hari sesi foto!
                </p>

            @elseif($booking->payment_status === 'Down Payment')
                {{-- STATUS: DP SUDAH DI-ACC (Tapi belum lunas) --}}
                <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <p class="text-[11px] font-bold text-blue-600 uppercase tracking-wider mb-2">DP Dikonfirmasi!</p>
                <h2 class="text-[22px] font-extrabold text-gray-900 mb-3">Jadwal Terkunci 📅</h2>
                <p class="text-[14px] text-gray-500 leading-relaxed px-2 mb-6">
                    Pembayaran DP kamu sudah diverifikasi dan jadwal aman. Jangan lupa selesaikan sisa pelunasan sebelum atau saat hari H ya.
                </p>

            @else
                {{-- STATUS: PENDING / BELUM BAYAR --}}
                <div class="w-16 h-16 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <p class="text-[11px] font-bold text-red-500 uppercase tracking-wider mb-2">Belum Dibayar</p>
                <h2 class="text-[22px] font-extrabold text-gray-900 mb-3">Selesaikan Pembayaran</h2>
                <p class="text-[14px] text-gray-500 leading-relaxed px-2 mb-6">
                    Harap segera selesaikan pembayaran dan upload bukti transfer agar jadwalmu segera dikonfirmasi.
                </p>
            @endif

            {{-- KOTAK KODE BOOKING --}}
            <div class="inline-flex items-center justify-center gap-3 bg-gray-50 border border-gray-100 rounded-xl px-5 py-3 mt-2 mx-auto w-fit">
                <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-widest">Kode Booking</span>
                <span class="font-mono font-extrabold text-gray-900 text-[14px] tracking-wide">{{ $bookingCode }}</span>
                <button type="button" @click="copyKode()" class="text-brand font-semibold text-[13px] hover:underline no-print border-l border-gray-200 pl-3 ml-1">
                    <span x-show="!copied">Salin</span>
                    <span x-show="copied" x-cloak>Tersalin!</span>
                </button>
            </div>

            {{-- TOMBOL LUNASI PEMBAYARAN JIKA MASIH ADA SISA & BUKAN SEDANG DIVERIFIKASI --}}
            @if(!$isWaitingVerification && ($booking->payment_status === 'Pending' || $booking->payment_status === 'Down Payment' || $booking->payment_status === 'Belum Bayar'))
                <div class="mt-6 no-print">
                    <a href="{{ route('booking.public.pembayaran', ['ownerId' => $ownerId, 'bookingId' => $booking->id]) }}" class="w-full bg-black text-white font-bold text-[13px] py-3.5 rounded-xl flex items-center justify-center gap-2 hover:bg-gray-800 transition">
                        💳 {{ $booking->payment_status === 'Down Payment' ? 'Lunasi Pembayaran Sekarang' : 'Selesaikan Pembayaran' }}
                    </a>
                </div>
            @endif
        </div>

        {{-- KARTU 2: RINGKASAN PEMBAYARAN --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6 print-shadow-none">
            <h3 class="font-bold text-gray-900 mb-5 text-[15px]">Ringkasan Pembayaran</h3>
            <div class="space-y-3.5 text-[14px]">
                <div class="flex justify-between items-center">
                    <span class="text-gray-500">Nama klien</span>
                    <span class="font-semibold text-gray-900">{{ $booking->client_name }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-500">Email</span>
                    <span class="font-semibold text-gray-900">{{ $booking->client_email ?? '-' }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-500">Tanggal sesi</span>
                    <span class="font-semibold text-gray-900">
                        {{ $tglLayanan ? \Carbon\Carbon::parse($tglLayanan)->locale('id')->isoFormat('D MMM YYYY') : '-' }}
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-500">Paket</span>
                    <span class="font-semibold text-gray-900">{{ $booking->serviceType->name ?? '-' }}</span>
                </div>
            </div>
            
            <hr class="border-gray-100 my-4">
            
            <div class="space-y-3.5 text-[14px] mb-5">
                <div class="flex justify-between items-center">
                    <span class="text-gray-500">Total Tagihan</span>
                    <span class="font-extrabold text-gray-900 text-[16px]">Rp {{ number_format($booking->total, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-500">Status</span>
                    @if($booking->payment_status === 'Lunas')
                        <span class="font-bold text-[#059669] bg-[#dcfce7] px-3 py-1 rounded-full text-[11px] uppercase tracking-wider">LUNAS</span>
                    @elseif($booking->payment_status === 'Down Payment')
                        <span class="font-bold text-blue-700 bg-blue-100 px-3 py-1 rounded-full text-[11px] uppercase tracking-wider">DP (UANG MUKA)</span>
                    @elseif($booking->payment_status === 'Tunggu Konfirmasi')
                        <span class="font-bold text-orange-600 bg-orange-50 border border-orange-100 px-3 py-1 rounded-full text-[11px] uppercase tracking-wider">MENUNGGU VERIFIKASI</span>
                    @else
                        <span class="font-bold text-red-600 bg-red-50 border border-red-100 px-3 py-1 rounded-full text-[11px] uppercase tracking-wider">PENDING</span>
                    @endif
                </div>
            </div>

            {{-- TOMBOL UNDUH BUKTI PEMBAYARAN (Tampil jika Lunas / DP) --}}
            @if($booking->payment_status === 'Lunas' || $booking->payment_status === 'Down Payment')
                <a href="{{ route('invoice.show', $booking->id) }}" target="_blank" class="w-full flex items-center justify-center gap-2 bg-[#f0fdf4] hover:bg-[#dcfce7] text-[#059669] font-bold py-3 rounded-xl transition-colors text-[13px] border border-[#bbf7d0] no-print">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Unduh Bukti Pembayaran
                </a>
            @endif
        </div>

        {{-- KARTU 3: LANGKAH SELANJUTNYA --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8 print-shadow-none">
            <h3 class="font-bold text-gray-900 mb-5 text-[15px]">Langkah Selanjutnya</h3>
            <ol class="space-y-4">
                <li class="flex items-start gap-3.5 text-[14px]">
                    <span class="flex-shrink-0 w-6 h-6 bg-orange-50 text-brand font-bold rounded-full flex items-center justify-center text-[12px] mt-0.5">1</span>
                    <span class="text-gray-600 leading-snug">Admin akan memeriksa bukti transfer kamu dalam <span class="font-semibold text-gray-800">1×24 jam</span>.</span>
                </li>
                <li class="flex items-start gap-3.5 text-[14px]">
                    <span class="flex-shrink-0 w-6 h-6 bg-orange-50 text-brand font-bold rounded-full flex items-center justify-center text-[12px] mt-0.5">2</span>
                    <span class="text-gray-600 leading-snug">Kamu akan menerima email konfirmasi setelah admin memverifikasi pembayaran.</span>
                </li>
                <li class="flex items-start gap-3.5 text-[14px]">
                    <span class="flex-shrink-0 w-6 h-6 bg-orange-50 text-brand font-bold rounded-full flex items-center justify-center text-[12px] mt-0.5">3</span>
                    <span class="text-gray-600 leading-snug">Pantau status booking via <span class="font-semibold text-gray-800">Track Booking</span>. Hubungi admin jika lebih dari 24 jam belum ada kabar.</span>
                </li>
            </ol>
        </div>

        {{-- TOMBOL AKSI --}}
        <div class="flex flex-col sm:flex-row gap-3 no-print">
            <a href="{{ route('booking.check.page') }}"
                class="flex-1 h-12 rounded-xl bg-brand hover-bg-brand text-white font-bold text-[14px] flex items-center justify-center transition-colors shadow-sm">
                Track Status Booking
            </a>
            <a href="{{ route('booking.public.show', $ownerId) }}"
                class="flex-1 h-12 rounded-xl border border-gray-200 bg-white text-gray-700 font-bold text-[14px] flex items-center justify-center hover:bg-gray-50 transition">
                Kembali ke Beranda
            </a>
        </div>
        
        <p class="text-center text-[12px] text-gray-400 mt-8 mb-4 no-print">
            Powered by <b>BookPhoto</b>
        </p>
    </div>
</body>
</html>