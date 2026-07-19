<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; color: #374151; line-height: 1.6; background-color: #f9fafb; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .header { margin-bottom: 25px; }
        .header h2 { margin: 0; color: #111827; font-size: 20px; display: flex; align-items: center; gap: 8px;}
        .content p { margin: 0 0 15px 0; font-size: 15px; color: #4b5563; }
        .details-box { background: #f3f4f6; padding: 20px; border-radius: 8px; margin-bottom: 25px; }
        .detail-row { margin-bottom: 10px; font-size: 14px; }
        .detail-row:last-child { margin-bottom: 0; }
        .detail-label { color: #6b7280; display: inline-block; width: 140px; }
        .detail-value { font-weight: 600; color: #111827; }
        .btn-container { text-align: center; margin: 30px 0; }
        .btn { background-color: #f59e0b; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 15px; display: inline-block; }
        .footer { text-align: center; font-size: 12px; color: #9ca3af; margin-top: 30px; border-top: 1px solid #e5e7eb; padding-top: 20px; }
    </style>
</head>
<body>
    @php
        $tglLayanan = $booking->booking_date ?? $booking->start_date;
        $tglFormat = $tglLayanan ? \Carbon\Carbon::parse($tglLayanan)->locale('id')->isoFormat('D MMM YYYY') : '-';
        
        // Menangkap waktu saat customer klik tombol kirim bukti transfer, diubah ke zona WIB (Asia/Jakarta)
        $waktuDikirim = \Carbon\Carbon::now()->timezone('Asia/Jakarta')->locale('id')->isoFormat('D MMM YYYY, HH:mm');
    @endphp

    <div class="container">
        <div class="header">
            <h2>⏳ Bukti Transfer Menunggu Konfirmasi</h2>
        </div>
        
        <div class="content">
            <p>Halo Admin,</p>
            <p>Customer telah mengupload bukti transfer <strong>{{ $paymentTypeText }}</strong> untuk booking berikut. Silakan konfirmasi atau tolak di admin panel.</p>

            <div class="details-box">
                <div class="detail-row"><span class="detail-label">Kode Booking:</span> <span class="detail-value">{{ $bookingCode }}</span></div>
                <div class="detail-row"><span class="detail-label">Nama Klien:</span> <span class="detail-value">{{ $booking->client_name }}</span></div>
                <div class="detail-row"><span class="detail-label">Email Klien:</span> <span class="detail-value">{{ $booking->client_email ?? '-' }}</span></div>
                <div class="detail-row"><span class="detail-label">WhatsApp:</span> <span class="detail-value">{{ $booking->client_contact }}</span></div>
                
                <div style="border-top: 1px solid #e5e7eb; margin: 15px 0;"></div>
                
                <div class="detail-row"><span class="detail-label">Layanan:</span> <span class="detail-value">{{ $booking->serviceType->name ?? '-' }}</span></div>
                <div class="detail-row"><span class="detail-label">Tanggal Foto:</span> <span class="detail-value">{{ $tglFormat }}</span></div>
                <div class="detail-row"><span class="detail-label">Jam:</span> <span class="detail-value">{{ $booking->booking_time ? \Carbon\Carbon::parse($booking->booking_time)->format('H:i') : '-' }}</span></div>
                
                <div style="border-top: 1px solid #e5e7eb; margin: 15px 0;"></div>
                
                <div class="detail-row"><span class="detail-label">Jenis Pembayaran:</span> <span class="detail-value">{{ $paymentTypeText }}</span></div>
                <div class="detail-row"><span class="detail-label">Nominal:</span> <span class="detail-value" style="color: #f59e0b;">Rp {{ number_format($amountToPay, 0, ',', '.') }}</span></div>
                <div class="detail-row"><span class="detail-label">Dikirim:</span> <span class="detail-value">{{ $waktuDikirim }} WIB</span></div>
            </div>

            <div class="btn-container">
                <a href="{{ route('dashboard') }}" class="btn">Buka Transaksi Pembayaran</a>
            </div>

            <p style="font-size: 13px; text-align: center; color: #6b7280;">Bukti transfer akan kedaluwarsa otomatis jika tidak dikonfirmasi dalam 24 jam.</p>
        </div>

        <div class="footer">
            <p>Terima kasih, <br><strong>BookPhoto</strong></p>
            <p>&copy; {{ date('Y') }} BookPhoto. All rights reserved.</p>
        </div>
    </div>
</body>
</html>