<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #374151;
            line-height: 1.6;
            background-color: #f9fafb;
            padding: 20px;
        }

        .container {
            max-width: 560px;
            margin: 0 auto;
            background: #ffffff;
            padding: 24px;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .top-bar {
            height: 4px;
            background-color: #f59e0b;
            margin: -24px -24px 20px -24px;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }

        .content p {
            margin: 0 0 16px 0;
            font-size: 14px;
            color: #4b5563;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border-top: 1px solid #e5e7eb;
        }

        .info-table td {
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
            vertical-align: top;
        }

        .info-table td.label {
            color: #6b7280;
            width: 38%;
        }

        .info-table td.value {
            color: #111827;
            font-weight: 500;
        }

        .status-lunas {
            color: #059669;
            font-weight: 700;
        }

        .status-dp {
            color: #d97706;
            font-weight: 700;
        }

        .btn-container {
            text-align: center;
            margin: 25px 0 10px 0;
        }

        .btn {
            display: inline-block;
            background-color: #111827;
            color: #ffffff;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
            margin-top: 30px;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }
    </style>
</head>

<body>
    @php
        $tglLayanan = $booking->booking_date ?? $booking->start_date;
        $tglFormat = $tglLayanan ? \Carbon\Carbon::parse($tglLayanan)->locale('id')->isoFormat('D MMM YYYY') : '-';
        $isLunas = strtoupper($booking->payment_type) === 'LUNAS' || strtoupper($booking->payment_type) === 'PELUNASAN';
    @endphp

    <div class="container">
        <div class="top-bar"></div>
        <div style="font-size: 16px; font-weight: 700; color: #111827; margin-bottom: 15px;">
            BookPhoto
        </div>
        <div style="font-size: 18px; font-weight: 700; color: #111827; margin-bottom: 10px;">
            {{ $isLunas ? 'Booking Masuk ✅' : 'Booking Masuk' }}
        </div>

        <div class="content">
            <p>Halo,</p>
            <p>Ada pembayaran {{ $isLunas ? 'Lunas' : 'DP' }} untuk booking berikut:</p>

            <table class="info-table">
                <tr>
                    <td class="label">Kode Booking:</td>
                    <td class="value" style="font-weight: 700;">{{ $bookingCode }}</td>
                </tr>
                <tr>
                    <td class="label">Nama Klien:</td>
                    <td class="value">{{ $booking->client_name }}</td>
                </tr>
                <tr>
                    <td class="label">Email Klien:</td>
                    <td class="value">{{ $booking->client_email ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">WhatsApp:</td>
                    <td class="value">{{ $booking->client_contact }}</td>
                </tr>
                <tr>
                    <td class="label">Tanggal Foto:</td>
                    <td class="value">{{ $tglFormat }}</td>
                </tr>
                <tr>
                    <td class="label">Jam:</td>
                    <td class="value">
                        {{ $booking->booking_time ? \Carbon\Carbon::parse($booking->booking_time)->format('H:i') : '-' }}
                    </td>
                </tr>
                <tr>
                    <td class="label">Paket:</td>
                    <td class="value">{{ $booking->serviceType->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Total Tagihan:</td>
                    <td class="value">Rp {{ number_format($booking->total, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="label">Total Sudah Dibayar:</td>
                    <td class="value">Rp {{ number_format($amountPaid, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="label">Transaksi Terakhir:</td>
                    <td class="value">Rp {{ number_format($amountPaid, 0, ',', '.') }}
                        {{ $isLunas ? '(LUNAS)' : '(DP)' }}</td>
                </tr>
                <tr>
                    <td class="label" style="border-bottom: none;">Status:</td>
                    <td class="value" style="border-bottom: none;">
                        @if($isLunas)
                            <span class="status-lunas">LUNAS ✅</span>
                        @else
                            <span class="status-dp">BELUM LUNAS (Sisa: Rp
                                {{ number_format($booking->remaining, 0, ',', '.') }})</span>
                        @endif
                    </td>
                </tr>
            </table>

            <div class="btn-container">
                <a href="{{ route('dashboard') }}" class="btn">Buka di Admin Panel</a>
            </div>
        </div>

        <div class="footer">
            <p>Terima kasih,<br><strong>BookPhoto</strong></p>
            <p>&copy; {{ date('Y') }} BookPhoto. All rights reserved.</p>
        </div>
    </div>
</body>

</html>