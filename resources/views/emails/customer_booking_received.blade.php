<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #374151;
            line-height: 1.6;
            background-color: #f3f4f6;
            padding: 24px 16px;
            margin: 0;
        }
        .container {
            max-width: 560px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        .top-bar {
            height: 4px;
            background-color: #f59e0b;
        }
        .sender-header {
            padding: 20px 28px 16px 28px;
        }
        .sender-header .sender-name {
            font-size: 17px;
            font-weight: 700;
            color: #d97706;
        }
        .sender-header .sender-via {
            font-size: 13px;
            color: #9ca3af;
            margin-top: 2px;
        }
        .hero {
            background-color: #111827;
            padding: 18px 28px;
        }
        .hero h2 {
            margin: 0;
            color: #ffffff;
            font-size: 17px;
            font-weight: 700;
        }
        .hero .booking-code-text {
            font-size: 13px;
            color: #9ca3af;
            margin-top: 6px;
        }
        .hero .booking-code-highlight {
            font-weight: 700;
            color: #ffffff;
        }
        .content {
            padding: 24px 28px 8px 28px;
        }
        .content p {
            margin: 0 0 14px 0;
            font-size: 14px;
            color: #4b5563;
        }

        .section {
            padding: 18px 28px;
            border-top: 1px solid #e5e7eb;
        }
        .section-label {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: #9ca3af;
            margin-bottom: 8px;
        }
        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 999px;
            background-color: #f3f4f6;
            border: 1px solid #e5e7eb;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
        }

        .col-table {
            width: 100%;
            border-collapse: collapse;
        }
        .col-table td {
            vertical-align: top;
            width: 50%;
            padding-right: 12px;
        }
        .field-label {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: #9ca3af;
            margin-bottom: 4px;
        }
        .field-value {
            font-size: 14px;
            color: #111827;
            font-weight: 500;
        }
        .field-value a {
            color: #111827;
            text-decoration: none;
        }

        .schedule-box {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 14px;
            color: #111827;
            font-weight: 500;
            margin-top: 4px;
        }

        .total-highlight {
            font-weight: 700;
            color: #111827;
        }

        .notice-text {
            font-size: 13px;
            color: #4b5563;
            padding: 16px 28px 0 28px;
        }

        .track-box {
            background-color: #f3f4f6;
            border-radius: 8px;
            padding: 12px 16px;
            margin: 16px 28px 8px 28px;
            font-size: 13.5px;
            color: #374151;
            border: 1px solid #e5e7eb;
        }
        .track-link {
            color: #2563eb;
            text-decoration: none;
            font-weight: 500;
        }
        .track-link:hover { text-decoration: underline; }

        .footer {
            font-size: 13px;
            color: #6b7280;
            padding: 20px 28px 24px 28px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
        }
        .footer p { margin: 0 0 6px 0; }
    </style>
</head>
<body>
    @php
        $tglLayanan = $booking->booking_date ?? $booking->start_date;
        $tglFormat = $tglLayanan ? \Carbon\Carbon::parse($tglLayanan)->locale('id')->isoFormat('D MMMM YYYY') : '-';

        // Konversi waktu ke Asia/Jakarta agar akurat menjadi WIB, lalu tambah 10 menit
        $batasWaktu = \Carbon\Carbon::parse($booking->created_at)
                        ->timezone('Asia/Jakarta')
                        ->addMinutes(10)
                        ->locale('id')
                        ->isoFormat('D MMM YYYY HH:mm');
    @endphp

    <div class="container">
        <div class="top-bar"></div>

        <div class="sender-header">
            <div class="sender-name">{{ $companyName }}</div>
            <div class="sender-via">via BookPhoto</div>
        </div>

        <div class="hero">
            <h2>Booking kamu kami terima 🎉</h2>
            <div class="booking-code-text">Kode booking: <span class="booking-code-highlight">{{ $bookingCode }}</span></div>
        </div>

        <div class="content">
            <p>Halo {{ $booking->client_name }}, terima kasih sudah booking di {{ $companyName }}. Saat ini booking kamu <strong>belum dianggap fix</strong> karena masih menunggu pembayaran.</p>

            <p>Saat ini booking kamu berstatus menunggu pembayaran. Silakan lanjutkan proses pembayaran melalui halaman yang baru saja terbuka.</p>

            <p>Batas waktu pembayaran: <strong>{{ $batasWaktu }} WIB</strong>.</p>
        </div>

        <div class="section">
            <div class="section-label">Status Booking</div>
            <span class="badge">Menunggu Pembayaran</span>
        </div>

        <div class="section" style="border-top: none; padding-top: 0;">
            <div class="section-label">Jenis Pembayaran</div>
            <span class="badge">Belum dibayar</span>
        </div>

        <div class="section">
            <table class="col-table">
                <tr>
                    <td>
                        <div class="field-label">Nama Klien</div>
                        <div class="field-value">{{ $booking->client_name }}</div>
                    </td>
                    <td>
                        <div class="field-label">Email</div>
                        <div class="field-value">
                            <a href="mailto:{{ $booking->client_email ?? '' }}">{{ $booking->client_email ?? '-' }}</a>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="section" style="border-top: none; padding-top: 0;">
            <table class="col-table">
                <tr>
                    <td>
                        <div class="field-label">WhatsApp</div>
                        <div class="field-value">{{ $booking->client_contact }}</div>
                    </td>
                    <td>
                        <div class="field-label">Instagram</div>
                        <div class="field-value">{{ $booking->client_instagram ?? '-' }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="section">
            <div class="field-label">Tanggal Sesi</div>
            <div class="field-value">{{ $tglFormat }}</div>
        </div>

        <div class="section" style="border-top: none; padding-top: 0;">
            <div class="field-label">Jadwal Sesi</div>
            <div class="schedule-box">
                🕐 {{ $booking->booking_time ? \Carbon\Carbon::parse($booking->booking_time)->format('H:i') : '-' }}
            </div>
        </div>

        <div class="section">
            <table class="col-table">
                <tr>
                    <td>
                        <div class="field-label">Paket</div>
                        <div class="field-value">{{ $booking->serviceType->name ?? '-' }}</div>
                    </td>
                    <td>
                        <div class="field-label">Total Tagihan</div>
                        <div class="field-value total-highlight">Rp {{ number_format($booking->total, 0, ',', '.') }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="section" style="border-top: none; padding-top: 0;">
            <div class="field-label">Lokasi / Area</div>
            <div class="field-value">{{ $booking->client_address ?? 'Studio / Menyesuaikan' }}</div>
        </div>

        <p class="notice-text">
            Jika pembayaran tidak kami terima sampai batas waktu yang tertera, sistem akan otomatis membatalkan booking ini dan slot akan dibuka kembali untuk klien lain.
        </p>

        <div class="track-box">
            Kamu bisa cek status booking & link file hasil foto (nantinya) di:
            <a href="https://fariz.bookphoto.id/booking/track" class="track-link" target="_blank">https://fariz.bookphoto.id/booking/track</a>
        </div>

        <div class="footer">
            <p>Email ini dikirim otomatis oleh sistem booking {{ $companyName }}. Jangan balas email ini.</p>
            @if($companyPhone)
                <p>Pertanyaan? Hubungi {{ $companyPhone }}</p>
            @endif
            <p style="margin: 0; font-size: 12px; color: #9ca3af;">Powered by <b>BookPhoto</b></p>
        </div>
    </div>
</body>
</html>