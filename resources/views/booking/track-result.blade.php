{{-- resources/views/booking/track-result.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Booking — {{ $bookingCode }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .text-brand { color: #f59e0b; }
        .bg-brand { background-color: #fbbf59; }
        .hover-bg-brand:hover { background-color: #f7a934; }
        
        /* CSS Untuk Timeline Vertikal */
        .timeline-container { position: relative; padding-left: 28px; margin-top: 15px; }
        .timeline-container::before {
            content: ''; position: absolute; left: 6px; top: 8px; bottom: 8px; width: 2px;
            background-color: #f3f4f6;
        }
        .timeline-item { position: relative; padding-bottom: 24px; }
        .timeline-item:last-child { padding-bottom: 0; }
        .timeline-dot {
            position: absolute; left: -28px; top: 4px; width: 14px; height: 14px;
            border-radius: 50%; background-color: #e5e7eb; border: 2px solid #ffffff; z-index: 10;
        }
        
        /* State Timeline: Selesai / Aktif */
        .timeline-item.completed .timeline-dot { background-color: #f59e0b; border-color: #fef3c7; }
        .timeline-item.active .timeline-dot { background-color: #f59e0b; box-shadow: 0 0 0 4px #fef3c7; }
        
        /* Menyambung garis untuk item yang selesai */
        .timeline-item.completed::before {
            content: ''; position: absolute; left: -22px; top: 18px; bottom: -8px; width: 2px;
            background-color: #f59e0b; z-index: 5;
        }
        .timeline-item:last-child::before { display: none; }
    </style>
</head>
<body class="min-h-screen bg-gray-50 font-sans antialiased pb-12">
    @php
        $tglLayanan = $booking->booking_date ?? $booking->start_date;
        $tglFormat = $tglLayanan ? \Carbon\Carbon::parse($tglLayanan)->locale('id')->isoFormat('dddd, D MMMM YYYY') : '-';
        $waktuPesan = \Carbon\Carbon::parse($booking->created_at)->locale('id')->isoFormat('D MMM YYYY');
        
        // Cek Status Pembayaran dan Jadwal
        $statusPembayaran = $booking->payment_status;
        $statusJadwal = $booking->status;
        
        $isLunas = strtoupper($booking->payment_type) === 'LUNAS' || strtoupper($booking->payment_type) === 'PELUNASAN';

        // Hitung Countdown Hari
        $hariIni = \Carbon\Carbon::now()->startOfDay();
        $tglAcara = $tglLayanan ? \Carbon\Carbon::parse($tglLayanan)->startOfDay() : null;
        $selisihHari = $tglAcara ? $hariIni->diffInDays($tglAcara, false) : null;

        // LOGIKA BARU: HITUNG NOMINAL YANG "SEDANG DIVERIFIKASI"
        $dpAmount = (int) ceil($booking->total * 0.3);
        $pendingAmount = 0;
        
        if ($statusPembayaran === 'Tunggu Konfirmasi') {
            if ($isLunas) {
                $pendingAmount = $booking->remaining > 0 ? $booking->remaining : $booking->total;
            } else {
                $pendingAmount = $dpAmount;
            }
        }
        
        $sisaVisual = $booking->remaining;
    @endphp
    
    {{-- TOPBAR --}}
    <div class="bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between shadow-sm sticky top-0 z-50">
        <a href="{{ route('booking.public.show', $ownerId) }}" class="text-sm font-medium text-gray-500 hover:text-gray-900 flex items-center gap-2 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            Beranda
        </a>
        <h1 class="text-[15px] font-extrabold text-gray-900">{{ $companySetting?->company_name ?? $owner->name }}</h1>
    </div>

    <div class="max-w-xl mx-auto px-4 pt-8" x-data="{ copied: false, copyKode() { navigator.clipboard.writeText('{{ $bookingCode }}'); this.copied = true; setTimeout(() => this.copied = false, 1500); } }">
        
        {{-- KARTU 1: STATUS BANNER --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Status Booking</p>
            
            @if($statusPembayaran === 'Lunas')
                <div class="flex items-start gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-[18px] font-extrabold text-gray-900">Terjadwal</h2>
                        <p class="text-[13px] text-gray-500 mt-0.5">Booking sudah dikonfirmasi. Sampai jumpa di sesi foto!</p>
                    </div>
                </div>
            @elseif($statusPembayaran === 'Tunggu Konfirmasi')
                <div class="flex items-start gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-orange-100 text-orange-500 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-[18px] font-extrabold text-gray-900">Menunggu Konfirmasi</h2>
                        <p class="text-[13px] text-gray-500 mt-0.5">Booking kamu sedang menunggu konfirmasi dari Admin.</p>
                    </div>
                </div>
            @elseif($statusPembayaran === 'Pending' || $statusPembayaran === 'Belum Bayar')
                <div class="flex items-start gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-red-100 text-red-500 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-[18px] font-extrabold text-gray-900">Belum Dibayar</h2>
                        <p class="text-[13px] text-gray-500 mt-0.5">Selesaikan pembayaran agar jadwalmu segera dikonfirmasi.</p>
                    </div>
                </div>
            @elseif($statusPembayaran === 'Down Payment')
                <div class="flex items-start gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-[18px] font-extrabold text-gray-900">DP Dikonfirmasi</h2>
                        <p class="text-[13px] text-gray-500 mt-0.5">Jadwalmu sudah aman. Jangan lupa selesaikan sisa pelunasan.</p>
                    </div>
                </div>
            @endif

            <div class="bg-gray-50 rounded-xl p-3 flex justify-between items-center border border-gray-100">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Kode</p>
                    <p class="font-mono font-bold text-gray-900 text-[14px]">{{ $bookingCode }}</p>
                </div>
                <button type="button" @click="copyKode()" class="text-brand font-semibold text-[13px] hover:underline">
                    <span x-show="!copied">Salin</span>
                    <span x-show="copied" x-cloak class="text-green-600">Tersalin!</span>
                </button>
            </div>
            
            @if($statusPembayaran === 'Pending' || $statusPembayaran === 'Down Payment')
                <div class="mt-4">
                    <a href="{{ route('booking.public.pembayaran', ['ownerId' => $ownerId, 'bookingId' => $booking->id]) }}" class="w-full bg-black text-white font-bold text-[13px] py-3 rounded-xl flex items-center justify-center gap-2 hover:bg-gray-800 transition">
                        💳 {{ $statusPembayaran === 'Down Payment' ? 'Lunasi Pembayaran' : 'Selesaikan Pembayaran' }}
                    </a>
                </div>
            @endif
        </div>

        {{-- KARTU 2: DETAIL BOOKING & PROGRESS --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
            <h3 class="font-bold text-gray-900 mb-4 text-[15px] border-b border-gray-100 pb-3">Detail Booking</h3>
            
            <div class="space-y-3.5 text-sm mb-6">
                <div class="flex flex-col">
                    <span class="text-gray-500 text-[12px] mb-0.5">Nama Klien</span>
                    <span class="font-semibold text-gray-900">{{ $booking->client_name }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="text-gray-500 text-[12px] mb-0.5">Paket</span>
                    <span class="font-semibold text-gray-900">{{ $booking->serviceType->name ?? '-' }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="text-gray-500 text-[12px] mb-0.5">Jadwal Sesi Foto</span>
                    <span class="font-semibold text-gray-900">{{ $tglFormat }}</span>
                    @if($selisihHari !== null && $selisihHari > 0)
                        <span class="text-brand font-bold text-[12px] mt-0.5">{{ $selisihHari }} hari lagi</span>
                    @elseif($selisihHari === 0)
                        <span class="text-emerald-500 font-bold text-[12px] mt-0.5">HARI INI</span>
                    @endif
                    <span class="text-gray-600 text-[13px] mt-0.5">{{ $booking->booking_time ? \Carbon\Carbon::parse($booking->booking_time)->format('H:i') : '-' }}</span>
                </div>
            </div>

            <h3 class="font-bold text-gray-900 mb-3 text-[15px] border-b border-gray-100 pb-3">Progress</h3>
            
            <div class="timeline-container">
                @php
                    // Pastikan zona waktu sinkron dengan WIB
                    $now = \Carbon\Carbon::now()->timezone('Asia/Jakarta'); 
                    
                    $tglHanyaTanggal = \Carbon\Carbon::parse($booking->booking_date ?? $booking->start_date)->format('Y-m-d');
                    $jamHanyaWaktu = $booking->booking_time ? \Carbon\Carbon::parse($booking->booking_time)->format('H:i:s') : '00:00:00';
                    $hasTime = !empty($booking->booking_time); // Cek apakah ada jam yang diinput
                    
                    $startSesi = \Carbon\Carbon::parse($tglHanyaTanggal . ' ' . $jamHanyaWaktu, 'Asia/Jakarta');
                    $endSesi = $startSesi->copy()->addHour(); // Asumsi durasi sesi 1 jam
                    
                    // --- LOGIKA PROGRESS KETAT ---
                    // 1. Cek apakah Admin SUDAH ACC Pembayaran
                    $isPaymentApproved = in_array($statusPembayaran, ['Lunas', 'Down Payment']);
                    
                    // 2. Jadwal Dikonfirmasi (Hanya menyala JIKA payment_approved)
                    $isJadwalDikonfirmasi = $isPaymentApproved && in_array($statusJadwal, ['Dijadwalkan', 'Proses Edit', 'Selesai']);
                    
                    // 3. Sesi Foto (Hanya dicek JIKA Jadwal sudah Dikonfirmasi)
                    $isSesiAktif = false;
                    $isSesiSelesai = false;
                    
                    if ($isJadwalDikonfirmasi) {
                        if (in_array($statusJadwal, ['Proses Edit', 'Selesai'])) {
                            $isSesiSelesai = true;
                        } elseif ($hasTime) { // Hanya berjalan otomatis jika ada data jam booking_time
                            if ($now->greaterThan($endSesi)) {
                                $isSesiSelesai = true;
                            } elseif ($now->greaterThanOrEqualTo($startSesi) && $now->lessThanOrEqualTo($endSesi)) {
                                $isSesiAktif = true;
                            }
                        }
                    }

                    // 4. Proses Editing
                    $isEditAktif = $isJadwalDikonfirmasi && $statusJadwal === 'Proses Edit';
                    $isEditSelesai = $isJadwalDikonfirmasi && $statusJadwal === 'Selesai';
                    
                    // 5. Hasil Selesai
                    $isAllSelesai = $isJadwalDikonfirmasi && $statusJadwal === 'Selesai';
                @endphp

                {{-- STEP 1: Booking Masuk (Selalu Menyala) --}}
                <div class="timeline-item completed">
                    <div class="timeline-dot"></div>
                    <h4 class="font-bold text-gray-900 text-[14px]">Booking Masuk</h4>
                    <p class="text-[12px] text-brand font-semibold mb-0.5">{{ $waktuPesan }}</p>
                    <p class="text-[12px] text-gray-500">Formulir berhasil diterima</p>
                </div>

                {{-- STEP 2: Jadwal Dikonfirmasi --}}
                <div class="timeline-item {{ $isJadwalDikonfirmasi ? 'completed' : '' }}">
                    <div class="timeline-dot"></div>
                    <h4 class="font-bold text-gray-900 text-[14px]">Jadwal Dikonfirmasi</h4>
                    <p class="text-[12px] text-gray-500">Admin mengkonfirmasi jadwal sesi</p>
                </div>

                {{-- STEP 3: Sesi Foto --}}
                <div class="timeline-item {{ $isSesiSelesai ? 'completed' : ($isSesiAktif ? 'active' : '') }}">
                    <div class="timeline-dot"></div>
                    <h4 class="font-bold text-gray-900 text-[14px]">Sesi Foto</h4>
                    <p class="text-[12px] text-gray-500">{{ \Carbon\Carbon::parse($tglHanyaTanggal)->locale('id')->isoFormat('D MMM YYYY') }} · {{ \Carbon\Carbon::parse($jamHanyaWaktu)->format('H:i') }}</p>
                </div>

                {{-- STEP 4: Proses Editing --}}
                <div class="timeline-item {{ $isEditSelesai ? 'completed' : ($isEditAktif ? 'active' : '') }}">
                    <div class="timeline-dot"></div>
                    <h4 class="font-bold text-gray-900 text-[14px]">Proses Editing</h4>
                    <p class="text-[12px] text-gray-500">Foto sedang diedit oleh tim kami</p>
                </div>

                {{-- STEP 5: Hasil Dikirim --}}
                <div class="timeline-item {{ $isAllSelesai ? 'completed' : '' }}">
                    <div class="timeline-dot"></div>
                    <h4 class="font-bold text-gray-900 text-[14px]">Hasil Dikirim</h4>
                    <p class="text-[12px] text-gray-500">Link hasil siap untuk diunduh</p>
                </div>
            </div>
        </div>

        {{-- KARTU 3: RINCIAN PEMBAYARAN PINTAR --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
            <h3 class="font-bold text-gray-900 mb-4 text-[15px] border-b border-gray-100 pb-3">Rincian Pembayaran</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between items-center">
                    <span class="text-gray-500">{{ $booking->serviceType->name ?? '-' }}</span>
                    <span class="font-semibold text-gray-900">Rp {{ number_format($booking->unit_price, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-500">Total Tagihan</span>
                    <span class="font-bold text-gray-900">Rp {{ number_format($booking->total, 0, ',', '.') }}</span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="text-gray-500">Sudah dibayar @if($statusPembayaran !== 'Lunas')<span class="text-[11px] font-normal">(Terverifikasi)</span>@endif</span>
                    <span class="font-semibold text-emerald-600">Rp {{ number_format($booking->paid_amount, 0, ',', '.') }}</span>
                </div>
                
                @if($pendingAmount > 0)
                    <div class="flex justify-between items-center bg-orange-50 p-2 rounded-lg border border-orange-100 mt-1">
                        <span class="text-orange-600 text-[12px] font-semibold flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Sedang Diverifikasi
                        </span>
                        <span class="font-semibold text-orange-600">Rp {{ number_format($pendingAmount, 0, ',', '.') }}</span>
                    </div>
                @endif
                
                {{-- LOGIKA TAMPILAN JIKA LUNAS VS BELUM --}}
                @if($statusPembayaran === 'Lunas')
                    <div class="flex justify-between items-center pt-3 mt-3 border-t border-gray-100">
                        <span class="font-bold text-gray-900">Status pembayaran</span>
                        <span class="font-extrabold text-emerald-600 text-[14px] bg-emerald-50 px-3 py-1 rounded-lg">LUNAS</span>
                    </div>
                @else
                    <div class="flex justify-between items-center pt-3 mt-3 border-t border-gray-100">
                        <span class="font-bold text-gray-900">Sisa pelunasan</span>
                        <span class="font-extrabold text-brand text-[15px]">Rp {{ number_format($sisaVisual, 0, ',', '.') }}</span>
                    </div>
                @endif
            </div>

            @if($sisaVisual > 0 && $statusPembayaran !== 'Tunggu Konfirmasi')
                <a href="{{ route('booking.public.pembayaran', ['ownerId' => $ownerId, 'bookingId' => $booking->id]) }}" class="block text-center w-full mt-5 bg-brand text-white font-bold text-[13px] py-3 rounded-xl hover:opacity-90 transition shadow-sm">
                    Bayar Pelunasan Sekarang &rarr;
                </a>
            @endif
            
            {{-- TOMBOL UNDUH INVOICE (HANYA MUNCUL SAAT LUNAS) --}}
            @if($statusPembayaran === 'Lunas')
                <div class="mt-6 text-center">
                    <a href="{{ route('invoice.show', $booking->id) }}" target="_blank" class="w-full bg-gray-900 text-white font-bold text-[13px] py-3 rounded-xl flex items-center justify-center gap-2 hover:bg-black transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Unduh Bukti Pembayaran
                    </a>
                    <p class="text-[12px] text-gray-400 mt-2">PDF resmi dari studio untuk booking ini.</p>
                </div>
            @endif
        </div>

        {{-- KARTU 4: HASIL FOTO --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8 text-center">
            <h3 class="font-bold text-gray-900 mb-2 text-[15px]">Hasil Foto</h3>
            
            @if($statusJadwal === 'Selesai' && $booking->link_hasil)
                <p class="text-[13px] text-gray-600 mb-4">Yeay! Foto kamu sudah selesai diedit dan siap diunduh.</p>
                <a href="{{ $booking->link_hasil }}" target="_blank" class="inline-flex items-center justify-center gap-2 w-full bg-blue-600 text-white font-bold text-[13px] py-3 rounded-xl hover:bg-blue-700 transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Unduh Hasil Foto
                </a>
            @else
                <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M4 6h16a1 1 0 011 1v10a1 1 0 01-1 1H4a1 1 0 01-1-1V7a1 1 0 011-1z"></path></svg>
                </div>
                <p class="text-[13px] font-semibold text-gray-700 mb-1">Hasil foto belum tersedia</p>
                <p class="text-[12px] text-gray-500">Admin akan kirim link setelah editing selesai.</p>
            @endif
        </div>

        {{-- TOMBOL NAVIGASI BAWAH --}}
        <div class="space-y-3 mb-10">
            @if($companySetting?->company_phone)
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $companySetting->company_phone) }}" target="_blank" class="w-full flex items-center justify-center gap-2 border border-gray-200 text-gray-700 font-bold text-[13px] py-3 rounded-xl hover:bg-gray-50 transition">
                    Hubungi Admin via WhatsApp
                </a>
            @endif
            <a href="{{ route('booking.public.show', $ownerId) }}" class="w-full flex items-center justify-center gap-2 border border-gray-200 text-gray-700 font-bold text-[13px] py-3 rounded-xl hover:bg-gray-50 transition">
                &larr; Beranda
            </a>
            <a href="{{ route('booking.check.page') }}" class="w-full flex items-center justify-center gap-2 text-brand font-bold text-[13px] py-2 hover:underline transition">
                Cek Booking Lain
            </a>
        </div>
        
        <p class="text-center text-[11px] text-gray-300 mb-8">
            Powered by <b>BookPhoto</b>
        </p>
    </div>
</body>
</html>