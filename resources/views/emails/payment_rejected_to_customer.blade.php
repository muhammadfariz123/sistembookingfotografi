<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; color: #374151; line-height: 1.6; background-color: #f9fafb; padding: 20px; }
        .container { max-width: 560px; margin: 0 auto; background: #ffffff; padding: 24px; border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .top-bar { height: 4px; background-color: #ef4444; margin: -24px -24px 20px -24px; border-top-left-radius: 12px; border-top-right-radius: 12px; }
        .sender-header { margin-bottom: 20px; }
        .sender-name { font-size: 17px; font-weight: 700; color: #ef4444; }
        .sender-via { font-size: 13px; color: #9ca3af; margin-top: 2px; }
        .hero { background-color: #111827; padding: 18px 24px; margin: 0 -24px 24px -24px; }
        .hero h2 { margin: 0; color: #ffffff; font-size: 17px; font-weight: 700; }
        .content p { margin: 0 0 16px 0; font-size: 14px; color: #4b5563; }
        
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; border-top: 1px solid #e5e7eb; }
        .info-table td { padding: 12px 0; border-bottom: 1px solid #e5e7eb; font-size: 14px; vertical-align: top;}
        .info-table td.label { color: #6b7280; width: 35%; }
        .info-table td.value { color: #111827; font-weight: 600; }
        
        .reason-box { background-color: #fef2f2; border: 1px solid #fee2e2; border-radius: 8px; padding: 14px 16px; margin: 15px 0; font-size: 14px; color: #991b1b; }
        .action-box { background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; margin: 20px 0; }
        
        .btn { display: inline-block; background-color: #ef4444; color: #ffffff; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 14px; margin-top: 10px; }
        .footer { text-align: center; font-size: 12px; color: #9ca3af; margin-top: 30px; border-top: 1px solid #e5e7eb; padding-top: 20px; }
        .footer p { margin: 0 0 4px 0; }
    </style>
</head>
<body>
    @php
        $tglLayanan = $booking->booking_date ?? $booking->start_date;
        $tglFormat = $tglLayanan ? \Carbon\Carbon::parse($tglLayanan)->locale('id')->isoFormat('D MMM YYYY') : '-';
    @endphp
    
    <div class="container">
        <div class="top-bar"></div>
        <div class="sender-header">
            <div class="sender-name">{{ $companyName }}</div>
            <div class="sender-via">via BookPhoto</div>
        </div>

        <div class="hero">
            <h2>❌ Bukti Transfer Ditolak</h2>
        </div>
        
        <div class="content">
            <p>Halo <strong>{{ $booking->client_name }}</strong>,</p>
            <p>Maaf, bukti transfer {{ $paymentTypeText }} kamu untuk booking berikut tidak dapat dikonfirmasi oleh admin.</p>

            <table class="info-table">
                <tr>
                    <td class="label">Kode Booking:</td>
                    <td class="value">{{ $bookingCode }}</td>
                </tr>
                <tr>
                    <td class="label">Jenis Pembayaran:</td>
                    <td class="value">{{ $paymentTypeText }}</td>
                </tr>
                <tr>
                    <td class="label">Tanggal Sesi:</td>
                    <td class="value">{{ $tglFormat }}</td>
                </tr>
                <tr>
                    <td class="label">Jam Sesi:</td>
                    <td class="value">{{ $sessionTime }}</td>
                </tr>
                <tr>
                    <td class="label" style="border-bottom: none;">Nominal:</td>
                    <td class="value" style="border-bottom: none; color: #ef4444;">Rp {{ number_format($booking->total, 0, ',', '.') }}</td>
                </tr>
            </table>

            <div class="reason-box">
                <strong>Alasan Penolakan:</strong><br>
                {{ $rejectReason }}
            </div>

            <p style="font-weight: 700; color: #111827; margin-top: 20px; margin-bottom: 5px;">Apa yang perlu kamu lakukan?</p>
            
            <div class="action-box">
                <p style="margin: 0; font-size: 13.5px;">Silakan upload ulang bukti transfer yang benar melalui halaman pembayaran, atau hubungi admin studio jika ada pertanyaan.</p>
                <a href="{{ route('booking.public.pembayaran', ['ownerId' => $booking->user_id, 'bookingId' => $booking->id]) }}" class="btn">Cek Status Booking</a>
            </div>

            <p style="font-size: 13.5px; color: #4b5563;">Jika ada pertanyaan, silakan balas email ini atau hubungi admin studio langsung.</p>
        </div>

        <div class="footer">
            <p>Terima kasih, <br><strong>{{ $companyName }}</strong></p>
            <p>&copy; {{ date('Y') }} BookPhoto. All rights reserved.</p>
        </div>
    </div>
</body>
</html>