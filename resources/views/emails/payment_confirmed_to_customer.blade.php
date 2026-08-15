<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; color: #374151; line-height: 1.6; background-color: #f9fafb; padding: 20px; }
        .container { max-width: 560px; margin: 0 auto; background: #ffffff; padding: 24px; border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .top-bar { height: 4px; background-color: #f59e0b; margin: -24px -24px 20px -24px; border-top-left-radius: 12px; border-top-right-radius: 12px; }
        .sender-header { margin-bottom: 20px; }
        .sender-name { font-size: 17px; font-weight: 700; color: #d97706; }
        .sender-via { font-size: 13px; color: #9ca3af; margin-top: 2px; }
        .hero { background-color: #111827; padding: 18px 24px; margin: 0 -24px 24px -24px; }
        .hero h2 { margin: 0; color: #ffffff; font-size: 17px; font-weight: 700; }
        .booking-code-text { font-size: 13px; color: #9ca3af; margin-top: 6px; }
        .booking-code-highlight { font-weight: 700; color: #ffffff; }
        .content p { margin: 0 0 16px 0; font-size: 14px; color: #4b5563; }
        
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; border-top: 1px solid #e5e7eb; }
        .info-table td { padding: 12px 0; border-bottom: 1px solid #e5e7eb; font-size: 14px; vertical-align: top;}
        .info-table td.label { color: #6b7280; width: 40%; }
        .info-table td.value { color: #111827; font-weight: 500; }
        
        .highlight-row td { background-color: #f9fafb; }
        .total-highlight { font-weight: 700; font-size: 15px; color: #111827; }
        .sisa-highlight { font-weight: 700; color: #ef4444; }
        
        .action-box { background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; margin-bottom: 20px; }
        .btn { display: inline-block; background-color: #111827; color: #ffffff; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 14px; margin-top: 10px; }
        
        .footer { font-size: 12px; color: #9ca3af; margin-top: 30px; border-top: 1px solid #e5e7eb; padding-top: 20px; text-align: center; }
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
            <h2>{{ $isFullyPaid ? 'Pembayaran kamu sudah kami terima 🎉' : 'DP kamu sudah kami terima 🎉' }}</h2>
            <div class="booking-code-text">Kode booking: <span class="booking-code-highlight">{{ $bookingCode }}</span></div>
        </div>
        
        <div class="content">
            <p>Halo <strong>{{ $booking->client_name }}</strong>, {{ $isFullyPaid ? 'pembayaran' : 'DP' }} kamu sudah berhasil kami catat. Terima kasih {{ $isFullyPaid ? '🙌' : 'sudah melakukan pembayaran 🙌' }}</p>

            <table class="info-table">
                <tr>
                    <td class="label">Jenis pembayaran</td>
                    <td class="value">{{ $paymentTypeString }}</td>
                </tr>
                <tr>
                    <td class="label">Dibayar sekarang</td>
                    <td class="value">Rp {{ number_format($currentPaymentAmount, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="label">Total sudah dibayar</td>
                    <td class="value">Rp {{ number_format($booking->paid_amount, 0, ',', '.') }}</td>
                </tr>
                <tr class="highlight-row">
                    <td class="label" style="padding-left: 10px;">Total tagihan</td>
                    <td class="value total-highlight">Rp {{ number_format($booking->total, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="label">Status booking</td>
                    <td class="value" style="color: #059669; font-weight: 600;">Scheduled</td>
                </tr>
                
                @if(!$isFullyPaid)
                <tr>
                    <td class="label">Sisa pelunasan</td>
                    <td class="value sisa-highlight">Rp {{ number_format($booking->remaining, 0, ',', '.') }}</td>
                </tr>
                @endif
                
                <tr>
                    <td class="label">Tanggal sesi</td>
                    <td class="value">{{ $tglFormat }}</td>
                </tr>
                <tr>
                    <td class="label">Jadwal sesi</td>
                    <td class="value">🕐 {{ $sessionTime }}</td>
                </tr>
                <tr>
                    <td class="label">Paket</td>
                    <td class="value">{{ $booking->serviceType->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label" style="border-bottom: none;">Lokasi</td>
                    <td class="value" style="border-bottom: none;">{{ $booking->client_address ?? 'Studio / Menyesuaikan' }}</td>
                </tr>
            </table>

            @if($isFullyPaid)
                <div class="action-box text-center">
                    <a href="{{ route('invoice.show', $booking->id) }}" class="btn">Unduh bukti pembayaran</a>
                </div>
                <p>Terima kasih, pembayaran kamu sudah kami terima. Sampai ketemu di hari sesi foto! 🙌</p>
            @else
                <div class="action-box">
                    <p style="margin-bottom: 5px;">Kamu bisa melanjutkan pelunasan melalui halaman berikut:</p>
                    <a href="{{ route('booking.public.pembayaran', ['ownerId' => $booking->user_id, 'bookingId' => $booking->id]) }}" class="btn">Bayar pelunasan sekarang</a>
                </div>
                <p style="font-size: 13px;">Tombol di atas akan membuka halaman rincian pelunasan. Pembayaran baru diproses setelah kamu menekan tombol bayar di halaman tersebut.</p>
                <p>Terima kasih sudah melakukan pembayaran DP. Kalau ada perubahan jadwal atau pertanyaan, silakan hubungi admin kami ya 🙌</p>
            @endif
        </div>

        <div class="footer">
            <p>Email ini dikirim otomatis oleh sistem booking {{ $companyName }}. Jangan balas email ini.</p>
            @if($companyPhone)
                <p>Pertanyaan? Hubungi {{ $companyPhone }}</p>
            @endif
            <p style="margin-top: 15px;">Powered by <b>BookPhoto</b></p>
        </div>
    </div>
</body>
</html>