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
        .bg-brand { background-color: #f59e0b; }
        .hover-bg-brand:hover { background-color: #d97706; }
        
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
        
        /* State Timeline: Selesai (Centang) / Aktif (Halo Oranye) */
        .timeline-item.completed .timeline-dot { background-color: #f59e0b; border-color: #f59e0b; }
        .timeline-item.active .timeline-dot { background-color: #f59e0b; box-shadow: 0 0 0 4px #fef3c7; }
        
        /* Menyambung garis HANYA JIKA tahap selanjutnya sudah aktif/selesai */
        .timeline-item.connected-next::before {
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
        $isPaymentApproved = in_array($statusPembayaran, ['Lunas', 'Down Payment']);

        // Hitung Countdown Hari Acara
        $hariIni = \Carbon\Carbon::now()->startOfDay();
        $tglAcara = $tglLayanan ? \Carbon\Carbon::parse($tglLayanan)->startOfDay() : null;
        $selisihHari = $tglAcara ? $hariIni->diffInDays($tglAcara, false) : null;

        // Hitung Deadline Pilih Foto
        $deadlineDb = $booking->deadline_pilih ? \Carbon\Carbon::parse($booking->deadline_pilih) : \Carbon\Carbon::parse($tglLayanan)->addDays(7);
        $sisaHariPilih = max(0, \Carbon\Carbon::now()->startOfDay()->diffInDays($deadlineDb->startOfDay(), false));
        $deadlineStr = $deadlineDb->locale('id')->isoFormat('DD MMM YYYY');

        // LOGIKA NOMINAL "SEDANG DIVERIFIKASI"
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

        // Hitung jumlah foto yang dipilih
        $selectedPhotosArray = json_decode($booking->selected_photos ?? '[]');
        $totalSelectedPhotos = count($selectedPhotosArray);
        $waktuPilihFoto = $booking->updated_at ? \Carbon\Carbon::parse($booking->updated_at)->format('d M Y, H:i') : '';

        // LOGIKA WAKTU REAL-TIME (KHUSUS BANNER)
        $now = \Carbon\Carbon::now()->timezone('Asia/Jakarta'); 
        $tglHanyaTanggal = \Carbon\Carbon::parse($tglLayanan)->format('Y-m-d');
        $jamHanyaWaktu = $booking->booking_time ? \Carbon\Carbon::parse($booking->booking_time)->format('H:i:s') : '00:00:00';
        $hasTime = !empty($booking->booking_time);
        
        $startSesi = \Carbon\Carbon::parse($tglHanyaTanggal . ' ' . $jamHanyaWaktu, 'Asia/Jakarta');
        $endSesiInfo = $startSesi->copy()->addHours(2); 

        $stateSesiRealTime = ''; 
        if ($isPaymentApproved && $statusJadwal === 'Dijadwalkan') {
            if ($hasTime) {
                if ($now->between($startSesi, $endSesiInfo)) {
                    $stateSesiRealTime = 'active'; // Sedang Sesi
                } elseif ($now->greaterThan($endSesiInfo)) {
                    $stateSesiRealTime = 'finished'; // Sesi Selesai (Nunggu Upload)
                }
            } else {
                $todayStr = $now->format('Y-m-d');
                if ($tglHanyaTanggal == $todayStr) {
                    $stateSesiRealTime = 'active';
                } elseif ($tglHanyaTanggal < $todayStr) {
                    $stateSesiRealTime = 'finished';
                }
            }
        }
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
        @if($statusJadwal === 'Selesai')
            {{-- DESAIN BANNER KHUSUS HASIL SELESAI --}}
            <div class="bg-emerald-50 rounded-2xl shadow-sm border border-emerald-100 p-6 mb-6 relative overflow-hidden">
                <div class="absolute bottom-0 left-6 right-6 h-1 bg-emerald-500 rounded-t-md"></div>
                
                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-emerald-500 text-white flex items-center justify-center shrink-0 shadow-sm text-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Status Booking</p>
                            <h2 class="text-[16px] font-extrabold text-emerald-600">Foto Siap! 🎉</h2>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Kode</p>
                        <p class="font-mono font-bold text-gray-900 text-[14px]">{{ $bookingCode }}</p>
                    </div>
                </div>
                
                <p class="text-[13px] text-gray-700 leading-relaxed mb-5">
                    Hasil foto kamu sudah siap! Klik tombol di bawah untuk membuka dan download fotomu.
                </p>

                <a href="{{ $booking->link_hasil }}" target="_blank" class="w-full bg-brand text-white font-bold text-[13px] py-3 rounded-xl flex items-center justify-center gap-2 hover:opacity-90 transition shadow-sm">
                    📥 Buka & Download Hasil Foto
                </a>
            </div>

        @elseif($statusJadwal === 'Proses Edit')
            {{-- DESAIN BANNER KHUSUS PROSES EDITING --}}
            <div class="bg-purple-50 rounded-2xl shadow-sm border border-purple-100 p-6 mb-6 relative overflow-hidden">
                <div class="absolute bottom-0 left-6 right-6 h-1 bg-purple-600 rounded-t-md"></div>
                
                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-purple-600 text-white flex items-center justify-center shrink-0 shadow-sm text-lg">
                            ✏️
                        </div>
                        <div>
                            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Status Booking</p>
                            <h2 class="text-[16px] font-extrabold text-purple-600">Proses Editing</h2>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Kode</p>
                        <p class="font-mono font-bold text-gray-900 text-[14px]">{{ $bookingCode }}</p>
                    </div>
                </div>
                
                <p class="text-[13px] text-gray-700 leading-relaxed">
                    Foto kamu sedang diedit oleh tim kami. Sabar ya, hampir selesai!
                </p>
            </div>

        @elseif($statusJadwal === 'Pilihan Diterima')
            {{-- DESAIN BANNER KETIKA PILIHAN FOTO SUDAH DIKIRIM --}}
            <div class="bg-orange-50 rounded-2xl shadow-sm border border-orange-100 p-6 mb-6 relative overflow-hidden">
                <div class="absolute bottom-0 left-6 right-6 h-1 bg-brand rounded-t-md"></div>
                
                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-brand text-white flex items-center justify-center shrink-0 shadow-sm text-lg">
                            ⏳
                        </div>
                        <div>
                            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Status Booking</p>
                            <h2 class="text-[16px] font-extrabold text-brand">Menunggu Editing</h2>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Kode</p>
                        <p class="font-mono font-bold text-gray-900 text-[14px]">{{ $bookingCode }}</p>
                    </div>
                </div>
                
                <p class="text-[13px] text-gray-700 leading-relaxed">
                    Pilihan foto sudah diterima. Menunggu proses editing dimulai oleh studio.
                </p>
            </div>

        @elseif($statusJadwal === 'File Original Disiapkan')
            {{-- DESAIN BANNER KHUSUS TAHAP PILIH FOTO --}}
            <div class="bg-orange-50 rounded-2xl shadow-sm border border-orange-100 p-6 mb-6 relative overflow-hidden">
                <div class="absolute bottom-0 left-6 right-6 h-1 bg-brand rounded-t-md"></div>
                
                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-brand text-white flex items-center justify-center shrink-0 shadow-sm text-lg">
                            📸
                        </div>
                        <div>
                            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Status Booking</p>
                            <h2 class="text-[16px] font-extrabold text-brand">Pilih Foto</h2>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Kode</p>
                        <p class="font-mono font-bold text-gray-900 text-[14px]">{{ $bookingCode }}</p>
                    </div>
                </div>
                
                <p class="text-[13px] text-gray-700 leading-relaxed mb-5">
                    File original sudah siap! Silakan pilih foto yang ingin diedit.
                </p>

                <a href="{{ url('/seleksi/' . $bookingCode) }}" class="w-full bg-brand text-white font-bold text-[13px] py-3 rounded-xl flex items-center justify-center gap-2 hover:opacity-90 transition shadow-sm">
                    📸 Pilih Foto Sekarang
                </a>
            </div>

        @elseif($stateSesiRealTime === 'active' || $stateSesiRealTime === 'finished')
            {{-- DESAIN BANNER KHUSUS SESI BERLANGSUNG / SELESAI --}}
            <div class="bg-orange-50 rounded-2xl shadow-sm border border-orange-100 p-6 mb-6 relative overflow-hidden">
                <div class="absolute bottom-0 left-6 right-6 h-1 bg-brand rounded-t-md"></div>
                
                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-brand text-white flex items-center justify-center shrink-0 shadow-sm">
                            <svg class="w-5 h-5 {{ $stateSesiRealTime === 'active' ? 'animate-[spin_3s_linear_infinite]' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Status Booking</p>
                            <h2 class="text-[16px] font-extrabold text-brand">
                                {{ $stateSesiRealTime === 'active' ? 'Sesi Sedang Berlangsung' : 'Sesi Selesai' }}
                            </h2>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Kode</p>
                        <p class="font-mono font-bold text-gray-900 text-[14px]">{{ $bookingCode }}</p>
                    </div>
                </div>
                
                <p class="text-[13px] text-gray-700 leading-relaxed">
                    @if($stateSesiRealTime === 'active')
                        Sesi sedang berlangsung. Setelah sesi selesai, selanjutnya menunggu studio menyiapkan file original / RAW.
                    @else
                        Sesi telah selesai. Selanjutnya menunggu studio menyiapkan file original / RAW.
                    @endif
                </p>
            </div>
        @else
            {{-- DESAIN BANNER STANDAR --}}
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
                            <p class="text-[13px] text-gray-500 mt-0.5">DP sudah diterima dan jadwal terkonfirmasi. Sisa tagihan dapat dilunasi pada saat Hari H pemotretan atau maksimal sebelum penyerahan hasil foto.</p>
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
        @endif

        {{-- KARTU 2: DETAIL BOOKING --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
            <h3 class="font-bold text-gray-900 mb-4 text-[15px] border-b border-gray-100 pb-3">Detail Booking</h3>
            
            <div class="space-y-4 mb-6">
                {{-- Baris 1: Nama Klien & Paket --}}
                <div class="flex justify-between items-start">
                    <div class="flex flex-col w-1/2 pr-2">
                        <span class="text-gray-500 text-[11px] uppercase tracking-wider font-bold mb-1">Nama Klien</span>
                        <span class="font-bold text-gray-900 text-[14px] truncate">{{ $booking->client_name }}</span>
                    </div>
                    <div class="flex flex-col w-1/2 pl-2 text-right">
                        <span class="text-gray-500 text-[11px] uppercase tracking-wider font-bold mb-1">Paket</span>
                        <span class="font-bold text-gray-900 text-[14px] truncate">{{ $booking->serviceType->name ?? '-' }}</span>
                    </div>
                </div>

                <hr class="border-gray-100">

                {{-- Baris 2: Jadwal Sesi Foto --}}
                <div class="flex flex-col">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-gray-500 text-[11px] uppercase tracking-wider font-bold">Jadwal Sesi Foto</span>
                    </div>
                    
                    <div class="flex items-start gap-3 mt-1.5">
                        <div class="w-10 h-10 rounded-xl bg-orange-50 text-brand flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <span class="font-bold text-gray-900 text-[14px] block">{{ $tglFormat }}</span>
                            @if($selisihHari !== null && $selisihHari > 0)
                                <span class="text-gray-500 text-[12px] block">Sisa {{ $selisihHari }} hari lagi</span>
                            @elseif($selisihHari === 0 && $stateSesiRealTime === 'active')
                                <span class="text-brand font-semibold text-[12px] block">Sesi sudah berlangsung</span>
                            @elseif($selisihHari === 0)
                                <span class="text-emerald-600 font-semibold text-[12px] block">HARI INI</span>
                            @elseif($selisihHari < 0)
                                <span class="text-gray-400 text-[12px] block">Sesi telah berlalu</span>
                            @endif
                        </div>
                    </div>
                    
                    @if($booking->booking_time)
                        <div class="bg-gray-50 rounded-lg p-3 mt-3 flex items-center gap-2 text-[13px] font-semibold text-gray-700 border border-gray-100">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ \Carbon\Carbon::parse($booking->booking_time)->format('H:i') }} WIB
                        </div>
                    @endif
                </div>

                {{-- Baris 3: Lokasi Sesi --}}
                @if($booking->client_address || $booking->link_gmaps)
                    <div class="flex flex-col mt-4">
                        <span class="text-gray-500 text-[11px] uppercase tracking-wider font-bold mb-1">Lokasi Sesi Foto</span>
                        <div class="flex items-start gap-2 mt-1">
                            <svg class="w-4 h-4 text-gray-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <div>
                                <span class="font-medium text-gray-900 text-[13px] block leading-snug">{{ $booking->client_address ?? 'Lokasi via Google Maps' }}</span>
                                @if($booking->link_gmaps)
                                    <a href="{{ $booking->link_gmaps }}" target="_blank" class="text-blue-600 text-[12px] font-semibold hover:underline mt-1 inline-block">Buka di Maps &rarr;</a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <h3 class="font-bold text-gray-900 mb-3 text-[15px] border-b border-gray-100 pb-3 uppercase tracking-wider">Progress</h3>
            
            <div class="timeline-container">
                @php
                    // --- LOGIKA PROGRESS WORKBOARD ---
                    $statusUrutan = [
                        'Dijadwalkan' => 1, 
                        'File Original Disiapkan' => 2, 
                        'Pilihan Diterima' => 3, 
                        'Proses Edit' => 4, 
                        'Selesai' => 5
                    ];
                    $currentLevel = $statusUrutan[$statusJadwal] ?? 0;

                    $stateBooking = 'completed'; // Booking Masuk selalu komplit
                    $stateJadwal = '';
                    $stateSesi = '';
                    $stateFile = '';
                    $statePilih = '';
                    $stateEdit = '';
                    $stateHasil = '';

                    if ($isPaymentApproved && $currentLevel >= 1) {
                        
                        // Cek apakah sudah menyentuh jam sesi foto
                        $hasReachedSession = ($stateSesiRealTime === 'active' || $stateSesiRealTime === 'finished');

                        // Jadwal & Sesi Foto
                        if ($currentLevel > 1) {
                            $stateJadwal = 'completed';
                            $stateSesi = 'completed';
                        } elseif ($currentLevel == 1) {
                            if ($hasReachedSession) {
                                $stateJadwal = 'completed';
                                $stateSesi = 'active';
                            } else {
                                $stateJadwal = 'active';
                                $stateSesi = ''; // Belum masuk tahap sesi, mengunci timeline agar tidak lanjut
                            }
                        }

                        // File Original
                        if ($currentLevel > 2) {
                            $stateFile = 'completed';
                        } elseif ($currentLevel == 2) {
                            $stateFile = 'active';
                        }

                        // Pilih Foto
                        if ($currentLevel > 3) {
                            $statePilih = 'completed';
                        } elseif ($currentLevel == 3) {
                            $statePilih = 'active';
                        }

                        // Edit
                        if ($currentLevel > 4) {
                            $stateEdit = 'completed';
                        } elseif ($currentLevel == 4) {
                            $stateEdit = 'active';
                        }

                        // Selesai
                        if ($currentLevel == 5) {
                            $stateHasil = 'completed';
                        }
                    }
                @endphp

                {{-- STEP 1: Booking Masuk --}}
                <div class="timeline-item {{ $stateBooking }} {{ $stateJadwal !== '' ? 'connected-next' : '' }}">
                    <div class="timeline-dot">
                        @if($stateBooking === 'completed')
                            <svg class="w-2.5 h-2.5 text-white absolute top-[2px] left-[2px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path></svg>
                        @endif
                    </div>
                    <div class="{{ $stateBooking !== '' ? '' : 'opacity-40' }}">
                        <div class="flex justify-between items-start">
                            <h4 class="font-bold text-gray-900 text-[14px]">Booking Masuk</h4>
                            @if($stateBooking === 'completed')
                                <p class="text-[11px] text-gray-400 mt-0.5">{{ \Carbon\Carbon::parse($booking->created_at)->format('d M Y') }}</p>
                            @endif
                        </div>
                        <p class="text-[12px] text-gray-500 mt-0.5">Formulir berhasil diterima</p>
                    </div>
                </div>

                {{-- STEP 2: Jadwal Dikonfirmasi --}}
                <div class="timeline-item {{ $stateJadwal }} {{ $stateSesi !== '' ? 'connected-next' : '' }}">
                    <div class="timeline-dot">
                        @if($stateJadwal === 'completed')
                            <svg class="w-2.5 h-2.5 text-white absolute top-[2px] left-[2px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path></svg>
                        @endif
                    </div>
                    <div class="{{ $stateJadwal !== '' ? '' : 'opacity-40' }}">
                        <h4 class="font-bold text-gray-900 text-[14px]">Jadwal Dikonfirmasi</h4>
                        <p class="text-[12px] text-gray-500 mt-0.5">Admin mengkonfirmasi jadwal sesi</p>
                    </div>
                </div>

                {{-- STEP 3: Sesi Foto --}}
                <div class="timeline-item {{ $stateSesi }} {{ $stateFile !== '' ? 'connected-next' : '' }}">
                    <div class="timeline-dot">
                        @if($stateSesi === 'completed')
                            <svg class="w-2.5 h-2.5 text-white absolute top-[2px] left-[2px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path></svg>
                        @endif
                    </div>
                    <div class="{{ $stateSesi !== '' ? '' : 'opacity-40' }}">
                        <h4 class="font-bold text-gray-900 text-[14px]">Sesi Foto</h4>
                        <p class="text-[12px] text-gray-500 mt-0.5">
                            @if($stateSesi === 'active' && $stateSesiRealTime === 'active')
                                Sesi sedang berlangsung. Setelah sesi selesai, selanjutnya menunggu studio menyiapkan file original / RAW.
                            @elseif($stateSesi === 'active' && $stateSesiRealTime === 'finished')
                                Sesi telah selesai. Selanjutnya menunggu studio menyiapkan file original / RAW.
                            @else
                                {{ \Carbon\Carbon::parse($tglHanyaTanggal)->format('d M Y') }} 
                                @if($hasTime)
                                    · {{ \Carbon\Carbon::parse($jamHanyaWaktu)->format('H:i') }} WIB
                                @endif
                            @endif
                        </p>
                    </div>
                </div>

                {{-- STEP 4: File Original Disiapkan --}}
                <div class="timeline-item {{ $stateFile }} {{ $statePilih !== '' ? 'connected-next' : '' }}">
                    <div class="timeline-dot">
                        @if($stateFile === 'completed')
                            <svg class="w-2.5 h-2.5 text-white absolute top-[2px] left-[2px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path></svg>
                        @endif
                    </div>
                    <div class="{{ $stateFile !== '' ? '' : 'opacity-40' }}">
                        <div class="flex justify-between items-start">
                            <h4 class="font-bold text-gray-900 text-[14px]">File Original Disiapkan</h4>
                            @if($stateFile === 'active' || $stateFile === 'completed')
                                <p class="text-[11px] text-gray-400 mt-0.5">{{ \Carbon\Carbon::parse($booking->updated_at)->format('d M Y') }}</p>
                            @endif
                        </div>
                        <p class="text-[12px] text-gray-500 mt-0.5">
                            {{ $stateFile === 'active' || $stateFile === 'completed' ? 'File original / RAW sudah siap di studio' : 'Studio menyiapkan foto original / RAW' }}
                        </p>
                    </div>
                </div>

                {{-- STEP 5: Pilih Foto untuk Diedit --}}
                <div class="timeline-item {{ $statePilih }} {{ $stateEdit !== '' ? 'connected-next' : '' }}">
                    <div class="timeline-dot">
                        @if($statePilih === 'completed')
                            <svg class="w-2.5 h-2.5 text-white absolute top-[2px] left-[2px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path></svg>
                        @endif
                    </div>
                    <div class="{{ $statePilih !== '' ? '' : 'opacity-40' }}">
                        <div class="flex justify-between items-start">
                            <h4 class="font-bold text-gray-900 text-[14px]">Pilih Foto untuk Diedit</h4>
                            @if(($statusJadwal === 'Pilihan Diterima' || $statusJadwal === 'Proses Edit' || $statusJadwal === 'Selesai') && $totalSelectedPhotos > 0)
                                <p class="text-[11px] text-gray-400 mt-0.5">{{ \Carbon\Carbon::parse($booking->updated_at)->format('d M Y') }}</p>
                            @endif
                        </div>
                        <p class="text-[12px] text-gray-500 mt-0.5">
                            @if(($statusJadwal === 'Pilihan Diterima' || $statusJadwal === 'Proses Edit' || $statusJadwal === 'Selesai') && $totalSelectedPhotos > 0)
                                {{ $totalSelectedPhotos }} foto dipilih · {{ $waktuPilihFoto }}
                            @else
                                Kamu memilih foto yang ingin diedit
                            @endif
                        </p>
                    </div>
                </div>

                {{-- STEP 6: Proses Editing --}}
                <div class="timeline-item {{ $stateEdit }} {{ $stateHasil !== '' ? 'connected-next' : '' }}">
                    <div class="timeline-dot">
                        @if($stateEdit === 'completed')
                            <svg class="w-2.5 h-2.5 text-white absolute top-[2px] left-[2px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path></svg>
                        @endif
                    </div>
                    <div class="{{ $stateEdit !== '' ? '' : 'opacity-40' }}">
                        <h4 class="font-bold text-gray-900 text-[14px]">Proses Editing</h4>
                        <p class="text-[12px] text-gray-500 mt-0.5">Foto sedang diedit oleh tim kami</p>
                    </div>
                </div>

                {{-- STEP 7: Hasil Dikirim --}}
                <div class="timeline-item {{ $stateHasil }}">
                    <div class="timeline-dot">
                        @if($stateHasil === 'completed')
                            <svg class="w-2.5 h-2.5 text-white absolute top-[2px] left-[2px]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"></path></svg>
                        @endif
                    </div>
                    <div class="{{ $stateHasil !== '' ? '' : 'opacity-40' }}">
                        <div class="flex justify-between items-start">
                            <h4 class="font-bold text-gray-900 text-[14px]">Hasil Dikirim</h4>
                            @if($stateHasil === 'completed')
                                <p class="text-[11px] text-gray-400 mt-0.5">{{ \Carbon\Carbon::parse($booking->updated_at)->format('d M Y') }}</p>
                            @endif
                        </div>
                        <p class="text-[12px] text-gray-500 mt-0.5">Link hasil siap untuk diunduh</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- KARTU TAMBAHAN KHUSUS: INFO PROSES EDITING (HANYA MUNCUL KETIKA STATUS 'Proses Edit') --}}
        @if($statusJadwal === 'Proses Edit')
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                <h3 class="font-bold text-gray-900 mb-4 text-[15px] border-b border-gray-100 pb-3 flex items-center gap-2">
                    <span class="text-lg">✏️</span> Info Proses Editing
                </h3>
                
                <div class="grid grid-cols-2 gap-4">
                    {{-- Posisi Antrian --}}
                    <div class="bg-purple-50/60 border border-purple-100 rounded-xl p-4">
                        <p class="text-[11px] font-bold text-purple-700 uppercase tracking-wider mb-1">Posisi Antrian</p>
                        <p class="text-2xl font-extrabold text-purple-900">#{{ $booking->queue_number ?? '1' }}</p>
                        <p class="text-[11px] text-purple-600 mt-1">dalam antrian editing</p>
                    </div>
                    
                    {{-- Estimasi Selesai --}}
                    <div class="bg-purple-50/60 border border-purple-100 rounded-xl p-4">
                        <p class="text-[11px] font-bold text-purple-700 uppercase tracking-wider mb-1">Estimasi Selesai</p>
                        <p class="text-[15px] font-extrabold text-purple-900">
                            {{ $booking->estimate_date ? \Carbon\Carbon::parse($booking->estimate_date)->locale('id')->isoFormat('D MMM YYYY') : 'Menyusul' }}
                        </p>
                        @if($booking->estimate_date)
                            @php
                                $diffDaysEst = \Carbon\Carbon::now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($booking->estimate_date)->startOfDay(), false);
                            @endphp
                            <p class="text-[11px] text-purple-700 font-bold mt-1">
                                {{ $diffDaysEst > 0 ? $diffDaysEst . ' hari lagi' : ($diffDaysEst === 0 ? 'Hari ini' : 'Terlewat ' . Math.abs($diffDaysEst) . ' hari') }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- KARTU TAHAP PILIH FOTO (MUNCUL PADA STATUS 'File Original Disiapkan', 'Pilihan Diterima', ATAU 'Proses Edit') --}}
        @if(in_array($statusJadwal, ['File Original Disiapkan', 'Pilihan Diterima', 'Proses Edit']))
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                <h3 class="font-bold text-gray-900 mb-4 text-[15px] border-b border-gray-100 pb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M4 6h16a1 1 0 011 1v10a1 1 0 01-1 1H4a1 1 0 01-1-1V7a1 1 0 011-1z"></path></svg>
                    Pilih Foto untuk Diedit
                </h3>
                
                @if(($statusJadwal === 'Pilihan Diterima' || $statusJadwal === 'Proses Edit') && $totalSelectedPhotos > 0)
                    <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-4 mb-5 flex items-center gap-3 text-emerald-800">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        <div class="text-[13px] font-bold">
                            {{ $totalSelectedPhotos }} foto sudah dipilih · {{ $waktuPilihFoto }}
                        </div>
                    </div>
                @else
                    <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-5 flex items-start gap-3">
                        <div class="text-xl leading-none">⏳</div>
                        <div>
                            <p class="text-[13px] font-bold text-blue-900">Sisa {{ $sisaHariPilih }} hari untuk memilih foto</p>
                            <p class="text-[12px] text-blue-700 mt-0.5">Deadline: {{ $deadlineStr }}</p>
                        </div>
                    </div>
                @endif

                <p class="text-[13px] text-gray-600 mb-5 leading-relaxed">
                    @if($statusJadwal === 'Proses Edit')
                        Foto pilihan kamu sedang dalam proses pengerjaan editing oleh tim studio.
                    @elseif($statusJadwal === 'Pilihan Diterima')
                        Kamu dapat mengubah pilihan foto sebelum admin mulai memproses editing.
                    @else
                        File original sudah siap! Pilih foto yang ingin diedit langsung dari platform.
                    @endif
                </p>

                <div class="space-y-3">
                    {{-- Tombol Ubah Pilihan Foto HANYA MUNCUL jika BELUM dalam Proses Edit --}}
                    @if($statusJadwal !== 'Proses Edit')
                        <a href="{{ url('/seleksi/' . $bookingCode) }}" class="w-full bg-brand text-white font-bold text-[13px] py-3 rounded-xl flex items-center justify-center gap-2 hover:opacity-90 transition shadow-sm">
                            {{ $statusJadwal === 'Pilihan Diterima' ? 'Ubah Pilihan Foto' : '📸 Pilih Foto Sekarang' }}
                        </a>
                    @endif

                    @if($booking->link_original)
                        <a href="{{ $booking->link_original }}" target="_blank" class="w-full bg-white border border-gray-300 text-gray-700 font-bold text-[13px] py-3 rounded-xl flex items-center justify-center gap-2 hover:bg-gray-50 transition shadow-sm">
                            Buka Google Drive / Download Foto ↗
                        </a>
                    @endif
                </div>
            </div>
        @endif

        {{-- KARTU 3: RINCIAN PEMBAYARAN PINTAR --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
            <h3 class="font-bold text-gray-900 mb-4 text-[15px] border-b border-gray-100 pb-3 uppercase tracking-wider">Rincian Pembayaran</h3>
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
                        <span class="font-extrabold text-emerald-600 text-[12px] bg-emerald-50 px-3 py-1 rounded-full flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg> Lunas</span>
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
                    <a href="{{ route('invoice.show', $booking->id) }}" target="_blank" class="w-full bg-emerald-50 text-emerald-600 font-bold text-[13px] py-3 rounded-xl flex items-center justify-center gap-2 hover:bg-emerald-100 transition shadow-sm border border-emerald-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Unduh Bukti Pembayaran
                    </a>
                    <p class="text-[11px] text-gray-400 mt-2">PDF resmi dari studio untuk booking ini.</p>
                </div>
            @endif
        </div>

        {{-- KARTU 4: HASIL FOTO --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8 text-center">
            <h3 class="font-bold text-gray-400 text-[11px] uppercase tracking-wider mb-4 text-left">Hasil Foto</h3>
            
            @if($statusJadwal === 'Selesai' && $booking->link_hasil)
                <div class="bg-orange-50/50 border border-orange-100 rounded-xl p-5 mb-4 text-left">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-white rounded-lg shadow-sm border border-orange-100 flex items-center justify-center shrink-0 text-xl">
                            🖼️
                        </div>
                        <div>
                            <h4 class="font-extrabold text-gray-900 text-[14px]">Foto kamu sudah siap! 📷</h4>
                            <p class="text-[12px] text-gray-500 mt-0.5">Segera download ke perangkatmu</p>
                        </div>
                    </div>
                    <a href="{{ $booking->link_hasil }}" target="_blank" class="w-full bg-brand text-white font-bold text-[13px] py-2.5 rounded-xl flex items-center justify-center gap-2 hover:opacity-90 transition shadow-sm">
                        Buka Hasil Foto ↗
                    </a>
                </div>

                @if(!empty($booking->admin_notes))
                    <div class="bg-orange-50 border border-orange-100 rounded-xl p-4 text-left flex items-start gap-3">
                        <div class="text-lg">📝</div>
                        <div>
                            <p class="text-[11px] font-bold text-orange-800 uppercase tracking-wider mb-1">Catatan dari admin</p>
                            <p class="text-[12px] text-orange-900 leading-relaxed">{!! nl2br(e($booking->admin_notes)) !!}</p>
                        </div>
                    </div>
                @endif
            @else
                <div class="bg-gray-50 rounded-xl p-6 flex flex-col items-center justify-center border border-gray-100">
                    <div class="w-10 h-10 bg-white shadow-sm rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <p class="text-[13px] font-semibold text-gray-900 mb-1">Hasil foto belum tersedia</p>
                    <p class="text-[12px] text-gray-500">Admin akan kirim link setelah editing selesai.</p>
                </div>
            @endif
        </div>

        {{-- TOMBOL NAVIGASI BAWAH --}}
        <div class="space-y-3 mb-10">
            @if($companySetting?->company_phone)
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $companySetting->company_phone) }}" target="_blank" class="w-full flex items-center justify-center gap-2 border border-gray-200 text-gray-700 font-bold text-[13px] py-3 rounded-xl hover:bg-gray-50 transition shadow-sm">
                    Hubungi Admin via WhatsApp
                </a>
            @endif
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('booking.public.show', $ownerId) }}" class="flex items-center justify-center gap-2 border border-gray-200 bg-white text-gray-700 font-bold text-[13px] py-3.5 rounded-xl hover:bg-gray-50 transition shadow-sm">
                    &larr; Beranda
                </a>
                <a href="{{ route('booking.check.page') }}" class="flex items-center justify-center gap-2 bg-brand text-white font-bold text-[13px] py-3.5 rounded-xl hover:bg-orange-500 transition shadow-sm">
                    Cek Booking Lain
                </a>
            </div>
        </div>
        
        <p class="text-center text-[11px] text-gray-400 mb-8">
            Powered by <b>BookPhoto</b>
        </p>
    </div>
</body>
</html>