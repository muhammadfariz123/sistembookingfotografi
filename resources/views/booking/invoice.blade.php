<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice — {{ $invoiceNumber }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100 font-sans antialiased">
    {{-- TOPBAR --}}
    <div class="sticky top-0 z-50 bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between shadow-sm hide-on-print">
        <h1 class="text-[16px] font-bold text-gray-900">Dokumen Invoice</h1>
        <div class="flex items-center gap-2">
            <button onclick="window.print()"
                class="h-[38px] px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold flex items-center gap-2 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print / Save PDF
            </button>
            <a href="{{ route('booking.public.show', $ownerId) }}"
                class="h-[38px] w-[38px] rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-600 flex items-center justify-center transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </a>
        </div>
    </div>
    
    {{-- NOTIF INSTRUKSI ATAS (TAMPIL HANYA JIKA BELUM LUNAS) --}}
    @if($booking->payment_status !== 'Lunas')
    <div class="max-w-3xl mx-auto px-4 pt-4 hide-on-print">
        <div class="bg-blue-50 border border-blue-200 rounded-2xl px-5 py-3 mb-4 flex items-start gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sm text-blue-700">
                @if($booking->payment_status === 'Belum Bayar')
                    Mohon segera lakukan pembayaran <strong>DP {{ $dpPercent }}%</strong> (maks. 2 hari setelah invoice ini dibuat) untuk mengamankan jadwal acara Anda.
                @elseif($booking->payment_status === 'Down Payment')
                    Mohon lakukan <strong>Pelunasan</strong> sisa tagihan maksimal H-7 sebelum tanggal pelaksanaan acara.
                @endif
            </p>
        </div>
    </div>
    @endif

    {{-- INVOICE PAPER --}}
    <div class="max-w-[794px] mx-auto pb-8 box-border" id="invoice-wrapper">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 box-border transition-all" id="print-area">
            {{-- HEADER INVOICE --}}
            <div class="flex items-start justify-between mb-6">
                <div>
                    @if($companySetting?->company_logo)
                        <img src="{{ Storage::url($companySetting->company_logo) }}"
                             class="h-12 w-auto object-contain mb-2" alt="Logo">
                    @else
                        <div class="w-14 h-10 bg-blue-100 rounded-lg flex items-center justify-center mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            </svg>
                        </div>
                    @endif
                    <p class="font-bold text-blue-600 text-[18px]">
                        {{ $companySetting?->company_name ?? $owner->name }}
                    </p>
                    @if($companySetting?->company_address)
                        <p class="text-[12px] text-gray-500 mt-0.5">{{ $companySetting->company_address }}</p>
                    @endif
                    @if($companySetting?->company_phone)
                        <p class="text-[12px] text-gray-500">Tel: {{ $companySetting->company_phone }}</p>
                    @endif
                    @if($companySetting?->company_email)
                        <p class="text-[12px] text-gray-500">Email: {{ $companySetting->company_email }}</p>
                    @endif
                </div>
                <div class="text-right">
                    <p class="text-[32px] font-black text-gray-900 uppercase tracking-tight leading-none">INVOICE</p>
                    <p class="text-[13px] text-gray-500 mt-1">{{ $invoiceNumber }}</p>
                    
                    {{-- Badge Status --}}
                    @if($booking->payment_status === 'Lunas')
                        <div class="inline-block mt-3 px-4 py-1.5 rounded-full bg-green-100 text-green-700 font-bold text-[12px] uppercase tracking-widest border border-green-200">
                            LUNAS
                        </div>
                    @endif
                </div>
            </div>
            
            <hr class="border-gray-200 mb-5">
            
            {{-- BILL TO + TANGGAL --}}
            <div class="flex items-start justify-between mb-5">
                <div>
                    <p class="text-[12px] font-semibold text-gray-500 mb-1 uppercase tracking-wide">Bill To:</p>
                    <p class="font-bold text-gray-900 text-[16px]">{{ $booking->client_name }}</p>
                    <p class="text-[13px] text-gray-600 mt-0.5">{{ $booking->client_contact }}</p>
                    <p class="text-[13px] text-gray-600">{{ $booking->client_address }}</p>
                </div>
                <div class="text-right space-y-1.5">
                    @php 
                        $tglLayanan = $booking->booking_date ?? $booking->start_date; 
                        
                        // Logika Jatuh Tempo Sesuai Instruksi Klien Skripsi
                        $jatuhTempo = null;
                        if ($booking->payment_status === 'Belum Bayar') {
                            // Belum bayar DP: Jatuh tempo 2 hari sejak hari ini
                            $jatuhTempo = now()->addDays(2);
                        } elseif ($booking->payment_status === 'Down Payment' && $tglLayanan) {
                            // Sedang cicil/pelunasan: Jatuh tempo H-7 Acara
                            $jatuhTempo = \Carbon\Carbon::parse($tglLayanan)->subDays(7);
                        }
                    @endphp

                    <div class="flex items-center gap-8 justify-end">
                        <span class="text-[12px] text-gray-500">Tanggal Invoice</span>
                        <span class="text-[13px] font-medium text-gray-800 w-[120px]">
                            {{ now()->locale('id')->isoFormat('D MMMM YYYY') }}
                        </span>
                    </div>

                    @if($jatuhTempo && $booking->payment_status !== 'Lunas')
                    <div class="flex items-center gap-8 justify-end">
                        <span class="text-[12px] font-bold text-red-500">Batas Pembayaran</span>
                        <span class="text-[13px] font-bold text-red-600 w-[120px]">
                            {{ $jatuhTempo->locale('id')->isoFormat('D MMMM YYYY') }}
                        </span>
                    </div>
                    @endif

                    <div class="flex items-center gap-8 justify-end">
                        <span class="text-[12px] text-gray-500">Tanggal Layanan</span>
                        <span class="text-[13px] font-medium text-gray-800 w-[120px]">
                            {{ $tglLayanan ? \Carbon\Carbon::parse($tglLayanan)->locale('id')->isoFormat('D MMMM YYYY') : '-' }}
                        </span>
                    </div>
                </div>
            </div>
            
            {{-- TABEL LAYANAN --}}
            <table class="w-full mb-5 text-[13px]">
                <thead>
                    <tr class="bg-blue-600 text-white">
                        <th class="px-4 py-3 text-left font-semibold rounded-tl-lg">Deskripsi Layanan</th>
                        <th class="px-4 py-3 text-right font-semibold">Harga Paket</th>
                        <th class="px-4 py-3 text-right font-semibold">Waktu Pelaksanaan</th>
                        <th class="px-4 py-3 text-right font-semibold rounded-tr-lg">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border border-gray-200">
                        <td class="px-4 py-4 align-top">
                            <p class="font-bold text-gray-900">
                                {{ $booking->serviceType?->name ?? '-' }}
                            </p>
                            @if($booking->serviceType?->description)
                                <p class="text-[12px] text-blue-600 mt-0.5 whitespace-pre-line">
                                    {{ $booking->serviceType->description }}
                                </p>
                            @endif
                            @if($booking->notes)
                                <p class="text-[12px] text-gray-500 mt-2">Catatan: {{ $booking->notes }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-right">
                            Rp {{ number_format($booking->unit_price, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-4 text-right">
                            {{ $tglLayanan ? \Carbon\Carbon::parse($tglLayanan)->locale('id')->isoFormat('D MMM YYYY') : '-' }}
                            @if($booking->booking_time)
                                <br><span class="text-[12px] text-gray-500">
                                    Pukul {{ substr($booking->booking_time, 0, 5) }} WIB
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-right font-semibold text-gray-900">
                            Rp {{ number_format($subtotal, 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>

            {{-- SUMMARY KEUANGAN --}}
            <div class="flex justify-end mb-5">
                <div class="w-full max-w-[340px] space-y-2 text-[13px]">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Subtotal:</span>
                        <span class="font-medium text-gray-800">
                            Rp {{ number_format($subtotal, 0, ',', '.') }}
                        </span>
                    </div>
                    @if($discountAmount > 0)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Diskon ({{ number_format($discountPercent, 0) }}%)</span>
                            <span class="font-medium text-red-500">
                                - Rp {{ number_format($discountAmount, 0, ',', '.') }}
                            </span>
                        </div>
                    @endif
                    <div class="flex justify-between border-t border-gray-200 pt-2 mt-2 mb-2">
                        <span class="font-medium text-gray-700">Total Keseluruhan:</span>
                        <span class="font-bold text-gray-900">
                            Rp {{ number_format($total, 0, ',', '.') }}
                        </span>
                    </div>
                    
                    {{-- Rincian DP dan Sisa --}}
                    @if($booking->payment_status !== 'Lunas')
                        <div class="bg-gray-50 rounded-xl p-3 border border-gray-200 mt-2 space-y-1.5">
                            
                            {{-- Jika Belum Bayar Sama Sekali --}}
                            @if($booking->payment_status === 'Belum Bayar')
                                <div class="flex justify-between text-orange-600">
                                    <span class="font-semibold">DP Minimal ({{ $dpPercent }}%):</span>
                                    <span class="font-bold">Rp {{ number_format($dpAmount, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between border-t border-gray-200 pt-1 mt-1 text-gray-600">
                                    <span>Sisa (Pelunasan H-7):</span>
                                    <span class="font-semibold">Rp {{ number_format($sisaAfterDp, 0, ',', '.') }}</span>
                                </div>
                            
                            {{-- Jika Sudah DP, tagih sisa/pelunasan --}}
                            @elseif($booking->payment_status === 'Down Payment')
                                <div class="flex justify-between text-green-600">
                                    <span class="font-medium">Sudah Dibayar (DP Masuk):</span>
                                    <span>Rp {{ number_format($booking->paid_amount, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between border-t border-gray-200 pt-1 mt-1 text-red-600">
                                    <span class="font-bold">Sisa Pelunasan (H-7):</span>
                                    <span class="font-bold">Rp {{ number_format($booking->remaining, 0, ',', '.') }}</span>
                                </div>
                            @endif

                        </div>
                    @endif
                </div>
            </div>

            {{-- INFORMASI PEMBAYARAN (HANYA MUNCUL JIKA BELUM LUNAS) --}}
            @if($booking->payment_status !== 'Lunas')
            <div class="border border-gray-200 rounded-2xl p-5 mb-4">
                <div class="flex justify-between items-center mb-2">
                    <p class="text-[14px] font-bold text-gray-800">Instruksi Pembayaran</p>
                    <p class="text-[13px] text-gray-500">Status: 
                        <span class="font-bold {{ $booking->payment_status == 'Belum Bayar' ? 'text-red-500' : 'text-orange-500' }}">
                            {{ $booking->payment_status }}
                        </span>
                    </p>
                </div>
                
                {{-- Kotak Besar Tagihan Saat Ini --}}
                <div class="border-2 border-blue-200 rounded-xl p-4 text-center mb-4 bg-blue-50/50">
                    <p class="text-[12px] font-semibold text-blue-700 mb-1">
                        Jumlah yang harus dibayarkan saat ini
                        @if($booking->payment_status === 'Belum Bayar')
                            (Pembayaran DP)
                        @else
                            (Pelunasan Sisa Tagihan)
                        @endif
                    </p>
                    <p class="text-[26px] font-black text-blue-700 leading-none">
                        @if($booking->payment_status === 'Belum Bayar')
                            Rp {{ number_format($dpAmount, 0, ',', '.') }}
                        @else
                            Rp {{ number_format($booking->remaining, 0, ',', '.') }}
                        @endif
                    </p>
                </div>

                {{-- Rekening Bank --}}
                @if($companySetting?->bank_name || $companySetting?->bank_account)
                    <div>
                        <p class="text-[13px] font-semibold text-gray-700 mb-2">Transfer Bank:</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @if($companySetting->bank_name)
                                <div class="border border-gray-200 rounded-xl p-3 text-[13px] bg-gray-50">
                                    <p><strong>Bank:</strong> {{ $companySetting->bank_name }}</p>
                                    @if($companySetting->bank_account)
                                        <p><strong>No. Rekening:</strong> {{ $companySetting->bank_account }}</p>
                                    @endif
                                    @if($companySetting->bank_holder)
                                        <p><strong>A.n:</strong> {{ $companySetting->bank_holder }}</p>
                                    @endif
                                </div>
                            @endif
                            @if($companySetting?->bank_name_2)
                                <div class="border border-gray-200 rounded-xl p-3 text-[13px] bg-gray-50">
                                    <p><strong>Bank:</strong> {{ $companySetting->bank_name_2 }}</p>
                                    @if($companySetting->bank_account_2)
                                        <p><strong>No. Rekening:</strong> {{ $companySetting->bank_account_2 }}</p>
                                    @endif
                                    @if($companySetting->bank_holder_2)
                                        <p><strong>A.n:</strong> {{ $companySetting->bank_holder_2 }}</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
                
                @if($companySetting?->payment_instruction)
                    <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-xl px-4 py-3">
                        <p class="text-[12px] font-semibold text-yellow-800 mb-0.5">Catatan Penting:</p>
                        <p class="text-[12px] text-yellow-800">{{ $companySetting->payment_instruction }}</p>
                    </div>
                @endif
            </div>
            @endif

            <p class="text-center text-[10px] text-gray-400 mt-6">
                Invoice ini dibuat secara otomatis oleh sistem {{ $companySetting?->company_name ?? $owner->name }}
            </p>
        </div>
    </div>

    <style>
        @media print {
            .sticky, .hide-on-print { display: none !important; }
            body { background: white !important; }
            html, body {
                width: 100%;
                height: 100%;
                margin: 0;
                padding: 0;
                overflow: hidden;
            }
            #print-area {
                border: none !important;
                box-shadow: none !important;
                max-width: 100% !important;
                padding: 10px !important;
            }
        }
    </style>
</body>
</html>