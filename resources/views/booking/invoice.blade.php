{{-- resources/views/booking/invoice.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Pembayaran - {{ $bookingCode }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; }
        /* Memaksa browser mencetak warna background */
        @media print {
            body { background-color: white !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
            .print-container { box-shadow: none !important; margin: 0 !important; max-width: 100% !important; padding: 0 !important; }
        }
        .header-dark { background-color: #0f172a; color: white; }
    </style>
</head>
<body class="py-10 flex justify-center items-start min-h-screen">
    
    <div class="print-container bg-white w-full max-w-4xl p-10 mx-4 shadow-xl">
        
        {{-- TOMBOL PRINT (Hanya muncul di layar, hilang saat dicetak) --}}
        <div class="flex justify-end mb-8 no-print">
            <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-sm font-bold flex items-center gap-2 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak / Simpan PDF
            </button>
        </div>

        {{-- HEADER INVOICE --}}
        <div class="flex justify-between items-start border-b-2 border-gray-900 pb-6 mb-6">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ $company?->company_name ?? $booking->user->name }}</h1>
                <div class="mt-2 text-[13px] text-gray-500">
                    <p>{{ $company?->company_email ?? $booking->user->email }}</p>
                    <p>{{ $company?->company_phone ?? '-' }}</p>
                </div>
            </div>
            <div class="text-right">
                <h2 class="text-2xl font-bold text-gray-900 uppercase tracking-widest">Bukti Pembayaran</h2>
                <p class="text-sm text-gray-500 mt-1 font-mono">#{{ $bookingCode }}</p>
                <div class="mt-2 flex justify-end">
                    @if($booking->payment_status === 'Lunas')
                        <span class="bg-[#dcfce7] text-[#16a34a] px-3 py-1 text-[11px] font-extrabold uppercase tracking-widest rounded">LUNAS</span>
                    @elseif($booking->payment_status === 'Down Payment')
                        <span class="bg-blue-100 text-blue-700 px-3 py-1 text-[11px] font-extrabold uppercase tracking-widest rounded">DP / SEBAGIAN</span>
                    @else
                        <span class="bg-orange-100 text-orange-600 px-3 py-1 text-[11px] font-extrabold uppercase tracking-widest rounded">PENDING</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- INFO BOX: DITERIMA DARI & DETAIL PEMBAYARAN --}}
        <div class="grid grid-cols-2 gap-6 mb-8">
            <div class="bg-[#f8fafc] border border-gray-200 rounded-lg p-5">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">Diterima Dari</p>
                <p class="text-[15px] font-bold text-gray-900 mb-1">{{ $booking->client_name }}</p>
                <p class="text-[13px] text-gray-600">{{ $booking->client_email ?? '-' }}</p>
                <p class="text-[13px] text-gray-600">{{ $booking->client_contact ?? '-' }}</p>
            </div>
            <div class="bg-[#f8fafc] border border-gray-200 rounded-lg p-5">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">Detail Pembayaran</p>
                <div class="flex justify-between text-[13px] mb-2">
                    <span class="text-gray-500">Tanggal lunas</span>
                    <span class="font-bold text-gray-900">
                        @if($booking->paid_at)
                            {{ \Carbon\Carbon::parse($booking->paid_at)->translatedFormat('d M Y H:i') }} WIB
                        @elseif(in_array($booking->payment_status, ['Lunas', 'Down Payment']))
                            {{ \Carbon\Carbon::parse($booking->updated_at)->translatedFormat('d M Y H:i') }} WIB
                        @else
                            -
                        @endif
                    </span>
                </div>
                <div class="flex justify-between text-[13px] mb-2">
                    <span class="text-gray-500">Kode booking</span>
                    <span class="font-bold text-gray-900 font-mono">{{ $bookingCode }}</span>
                </div>
                <div class="flex justify-between text-[13px]">
                    <span class="text-gray-500">Status</span>
                    <span class="font-bold text-gray-900 uppercase">{{ $booking->payment_status }}</span>
                </div>
            </div>
        </div>

        {{-- DETAIL BOOKING --}}
        <div class="mb-8">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-1.5 h-4 bg-emerald-500 rounded-full"></div>
                <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Detail Booking</h3>
            </div>
            <div class="border-t border-gray-200">
                
                @php
                    $durasiJam = $booking->serviceType->duration ?? 0;
                    $waktuMulaiStr = $booking->booking_time ? \Carbon\Carbon::parse($booking->booking_time)->format('H:i') : null;
                    $waktuSelesaiStr = ($waktuMulaiStr && $durasiJam > 0) ? \Carbon\Carbon::parse($booking->booking_time)->addHours($durasiJam)->format('H:i') : null;
                    $teksWaktuJadwal = $waktuSelesaiStr ? "{$waktuMulaiStr} - {$waktuSelesaiStr} WIB" : ($waktuMulaiStr ? "{$waktuMulaiStr} WIB" : '-');
                @endphp

                <div class="flex py-3 border-b border-gray-100 text-[13px]">
                    <div class="w-1/4 text-gray-500">Paket</div>
                    <div class="w-3/4">
                        <p class="font-bold text-gray-900">{{ $booking->serviceType->name ?? '-' }}</p>
                        @if($durasiJam > 0)
                            <p class="text-[12px] font-semibold text-gray-500 mt-0.5">Durasi {{ $durasiJam }} jam</p>
                        @endif
                    </div>
                </div>
                <div class="flex py-3 border-b border-gray-100 text-[13px]">
                    <div class="w-1/4 text-gray-500">Tanggal sesi</div>
                    <div class="w-3/4 font-bold text-gray-900">
                        {{ $booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date)->translatedFormat('l, d F Y') : '-' }}
                    </div>
                </div>
                <div class="flex py-3 border-b border-gray-100 text-[13px] items-center">
                    <div class="w-1/4 text-gray-500">Jadwal</div>
                    <div class="w-3/4 font-bold text-gray-900">
                        {{ $teksWaktuJadwal }}
                    </div>
                </div>
                <div class="flex py-3 border-b border-gray-100 text-[13px]">
                    <div class="w-1/4 text-gray-500">Lokasi</div>
                    <div class="w-3/4 font-bold text-gray-900">{{ $booking->client_address ?? '-' }}</div>
                </div>
            </div>
        </div>

        {{-- RINCIAN TAGIHAN --}}
        <div class="mb-6">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-1.5 h-4 bg-emerald-500 rounded-full"></div>
                <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Rincian Tagihan</h3>
            </div>
            <table class="w-full text-[13px]">
                <thead class="header-dark">
                    <tr>
                        <th class="py-3 px-4 text-left font-semibold">Deskripsi</th>
                        <th class="py-3 px-4 text-center font-semibold">Qty</th>
                        <th class="py-3 px-4 text-right font-semibold">Harga</th>
                        <th class="py-3 px-4 text-right font-semibold rounded-tr-sm">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-gray-200">
                        <td class="py-4 px-4">
                            <p class="font-bold text-gray-900">{{ $booking->serviceType->name ?? '-' }}</p>
                            @if($durasiJam > 0)
                                <p class="text-[12px] font-semibold text-gray-500 mt-0.5">Durasi {{ $durasiJam }} jam</p>
                            @endif
                        </td>
                        <td class="py-4 px-4 text-center">1</td>
                        <td class="py-4 px-4 text-right">Rp {{ number_format($booking->unit_price, 0, ',', '.') }}</td>
                        <td class="py-4 px-4 text-right font-bold text-gray-900">Rp {{ number_format($booking->total, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- TOTAL BOX --}}
        <div class="header-dark rounded-lg flex justify-between items-center px-6 py-5 mb-8">
            <span class="text-sm font-semibold text-gray-200">Total dibayar</span>
            <span class="text-2xl font-bold text-emerald-400">Rp {{ number_format($booking->paid_amount, 0, ',', '.') }}</span>
        </div>

        {{-- RIWAYAT PEMBAYARAN --}}
        <div class="mb-8">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-1.5 h-4 bg-emerald-500 rounded-full"></div>
                <h3 class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Riwayat Pembayaran</h3>
            </div>
            <table class="w-full text-[13px]">
                <thead class="header-dark">
                    <tr>
                        <th class="py-2.5 px-4 text-left font-semibold rounded-tl-sm">Tanggal</th>
                        <th class="py-2.5 px-4 text-left font-semibold">Jenis</th>
                        <th class="py-2.5 px-4 text-left font-semibold">Metode</th>
                        <th class="py-2.5 px-4 text-right font-semibold rounded-tr-sm">Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    @if($booking->transactions && $booking->transactions->count() > 0)
                        @foreach($booking->transactions as $tx)
                            @if($tx->payment_status === 'Berhasil' || $tx->payment_status === 'Lunas' || $tx->payment_status === 'Down Payment')
                            <tr class="border-b border-gray-200">
                                <td class="py-3 px-4 text-gray-600">{{ $tx->paid_at ? \Carbon\Carbon::parse($tx->paid_at)->translatedFormat('d M Y H:i') : \Carbon\Carbon::parse($tx->updated_at)->translatedFormat('d M Y H:i') }}</td>
                                <td class="py-3 px-4 font-medium text-gray-900">
                                    @if(strtoupper($tx->payment_type) === 'DP')
                                        Uang Muka (DP)
                                    @elseif(strtoupper($tx->payment_type) === 'PELUNASAN')
                                        Pelunasan
                                    @else
                                        Pembayaran Penuh
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-gray-600">
                                    @if($company?->payment_method === 'qris')
                                        QRIS
                                    @else
                                        Transfer Bank
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right font-bold text-gray-900">Rp {{ number_format($tx->amount, 0, ',', '.') }}</td>
                            </tr>
                            @endif
                        @endforeach
                    @else
                        {{-- Fallback jika belum ada tabel transactions tapi statusnya sudah lunas/dp --}}
                        @if($booking->paid_amount > 0)
                            <tr class="border-b border-gray-200">
                                <td class="py-3 px-4 text-gray-600">{{ $booking->paid_at ? \Carbon\Carbon::parse($booking->paid_at)->translatedFormat('d M Y H:i') : \Carbon\Carbon::parse($booking->updated_at)->translatedFormat('d M Y H:i') }}</td>
                                <td class="py-3 px-4 font-medium text-gray-900">Pembayaran</td>
                                <td class="py-3 px-4 text-gray-600">
                                    @if($company?->payment_method === 'qris')
                                        QRIS
                                    @else
                                        Transfer Bank
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right font-bold text-gray-900">Rp {{ number_format($booking->paid_amount, 0, ',', '.') }}</td>
                            </tr>
                        @endif
                    @endif
                </tbody>
            </table>
        </div>

        {{-- FOOTER NOTE --}}
        <div class="bg-[#f8fafc] border border-gray-200 rounded-lg p-5 text-[12px] text-gray-500 leading-relaxed text-center mb-8">
            Dokumen ini dibuat otomatis oleh {{ $company?->company_name ?? $booking->user->name }} melalui sistem sebagai bukti pembayaran booking yang sah. Simpan kode booking <span class="font-mono font-bold text-gray-700">{{ $bookingCode }}</span> untuk kebutuhan konfirmasi jadwal atau bantuan dari studio.
        </div>

        {{-- WATERMARK TIMESTAMP --}}
        <div class="text-center text-[10px] text-gray-400">
            Dibuat pada {{ \Carbon\Carbon::now()->translatedFormat('d M Y H:i') }} WIB<br>
            {{ strtolower($company?->company_name ?? $booking->user->name) }} - Powered by BookPhoto
        </div>

    </div>
</body>
</html>