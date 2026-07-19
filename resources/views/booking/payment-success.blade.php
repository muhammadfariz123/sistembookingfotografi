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
            color: #f59e0b;
        }
        .bg-brand {
            background-color: #fbbf59;
        }
        .hover-bg-brand:hover {
            background-color: #f7a934;
        }
    </style>
</head>
<body class="min-h-screen bg-gray-50 font-sans antialiased pb-12">
    @php
        $tglLayanan = $booking->booking_date ?? $booking->start_date;
    @endphp
    
    {{-- TOPBAR --}}
    <div class="bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between shadow-sm sticky top-0 z-50">
        <a href="{{ route('booking.public.show', $ownerId) }}"
            class="text-sm font-medium text-gray-500 hover:text-gray-900 flex items-center gap-2 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Kembali ke Beranda
        </a>
        <h1 class="text-[15px] font-extrabold text-gray-900">{{ $companySetting?->company_name ?? $owner->name }}</h1>
    </div>

    <div class="max-w-xl mx-auto px-4 pt-12"
        x-data="{ copied: false, copyKode() { navigator.clipboard.writeText('{{ $bookingCode }}'); this.copied = true; setTimeout(() => this.copied = false, 1500); } }">
        
        {{-- KARTU 1: ICON + PESAN (DINAMIS SESUAI STATUS) --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center mb-6">
            
            @if($booking->payment_status === 'Lunas' || $booking->payment_status === 'Down Payment')
                <div class="w-16 h-16 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-5">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <p class="text-[12px] font-extrabold text-emerald-600 uppercase tracking-wider mb-2">Pembayaran Terverifikasi</p>
                <h2 class="text-2xl font-extrabold text-gray-900 mb-3">Booking Terkonfirmasi!</h2>
                <p class="text-sm text-gray-500 leading-relaxed px-2">
                    Pembayaran kamu telah berhasil dikonfirmasi oleh admin. Sampai jumpa di hari sesi foto ya!
                </p>
            @else
                <div class="w-16 h-16 bg-orange-50 text-brand rounded-full flex items-center justify-center mx-auto mb-5">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <p class="text-[12px] font-extrabold text-brand uppercase tracking-wider mb-2">Menunggu Verifikasi Admin</p>
                <h2 class="text-2xl font-extrabold text-gray-900 mb-3">Bukti Transfer Dikirim!</h2>
                <p class="text-sm text-gray-500 leading-relaxed px-2">
                    Bukti transfer kamu sudah kami terima. Admin
                    <span class="font-semibold text-gray-700">{{ $companySetting?->company_name ?? $owner->name }}</span>
                    akan memverifikasi pembayaran dalam 1x24 jam. Kamu akan mendapat notifikasi email setelah dikonfirmasi.
                </p>
            @endif

            <div class="mt-6 flex items-center justify-between bg-gray-50 border border-gray-200 rounded-xl px-5 py-3.5">
                <div class="text-left">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Kode Booking</p>
                    <p class="font-mono font-extrabold text-gray-900 text-[15px]">{{ $bookingCode }}</p>
                </div>
                <button type="button" @click="copyKode()" class="text-brand font-semibold text-sm hover:underline">
                    <span x-show="!copied">Salin</span>
                    <span x-show="copied" x-cloak>Tersalin!</span>
                </button>
            </div>
        </div>

        {{-- KARTU 2: RINGKASAN PEMBAYARAN --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
            <h3 class="font-bold text-gray-900 mb-4 text-[15px]">Ringkasan Pembayaran</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between items-center">
                    <span class="text-gray-500">Nama klien</span>
                    <span class="font-semibold text-gray-900">{{ $booking->client_name }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-500">Email</span>
                    <span class="font-semibold text-gray-900">{{ $booking->client_email }}</span>
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
            
            <div class="border-t border-gray-100 mt-4 pt-4 space-y-3 text-sm">
                <div class="flex justify-between items-center">
                    <span class="text-gray-500">Total Tagihan</span>
                    <span class="font-extrabold text-gray-900 text-[15px]">Rp {{ number_format($booking->total, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-500">Status</span>
                    @if($booking->payment_status === 'Lunas')
                        <span class="font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-md text-[11px] uppercase tracking-wide">Lunas Penuh</span>
                    @elseif($booking->payment_status === 'Down Payment')
                        <span class="font-bold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-md text-[11px] uppercase tracking-wide">DP (Uang Muka)</span>
                    @else
                        <span class="font-bold text-orange-600 bg-orange-50 px-2.5 py-1 rounded-md text-[11px] uppercase tracking-wide">PENDING</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- KARTU 3: LANGKAH SELANJUTNYA (Sembunyikan jika sudah Lunas/DP) --}}
        @if($booking->payment_status !== 'Lunas' && $booking->payment_status !== 'Down Payment')
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                <h3 class="font-bold text-gray-900 mb-4 text-[15px]">Langkah Selanjutnya</h3>
                <ol class="space-y-3.5">
                    <li class="flex gap-3 text-sm">
                        <span class="flex-shrink-0 w-5 h-5 bg-orange-50 text-brand font-bold rounded-full flex items-center justify-center text-[11px]">1</span>
                        <span class="text-gray-600">Admin akan memeriksa bukti transfer kamu dalam <span class="font-semibold text-gray-800">1x24 jam</span>.</span>
                    </li>
                    <li class="flex gap-3 text-sm">
                        <span class="flex-shrink-0 w-5 h-5 bg-orange-50 text-brand font-bold rounded-full flex items-center justify-center text-[11px]">2</span>
                        <span class="text-gray-600">Kamu akan menerima email konfirmasi setelah admin memverifikasi pembayaran.</span>
                    </li>
                    <li class="flex gap-3 text-sm">
                        <span class="flex-shrink-0 w-5 h-5 bg-orange-50 text-brand font-bold rounded-full flex items-center justify-center text-[11px]">3</span>
                        <span class="text-gray-600">Pantau status booking via <span class="font-semibold text-gray-800">Track Booking</span>. Hubungi admin jika lebih dari 24 jam belum ada kabar.</span>
                    </li>
                </ol>
            </div>
        @endif

        {{-- TOMBOL AKSI --}}
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('booking.check.page') }}"
                class="flex-1 h-12 rounded-xl bg-brand hover-bg-brand text-white font-bold text-[14px] flex items-center justify-center transition-colors shadow-sm">
                Track Status Booking
            </a>
            <a href="{{ route('booking.public.show', $ownerId) }}"
                class="flex-1 h-12 rounded-xl border border-gray-200 text-gray-700 font-bold text-[14px] flex items-center justify-center hover:bg-gray-50 transition">
                Kembali ke Beranda
            </a>
        </div>
        
        <p class="text-center text-[11px] text-gray-400 mt-8 mb-8">
            Powered by <b>BookPhoto</b>
        </p>
    </div>
</body>
</html>