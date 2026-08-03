<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hasil Foto Sudah Siap</title>
</head>
<body style="background-color: #f4f6f8; font-family: 'Inter', Arial, sans-serif; padding: 30px 0; margin: 0;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: 1px solid #e5e7eb;">
        
        {{-- HEADER --}}
        <div style="background-color: #ffffff; padding: 25px 30px;">
            <h2 style="color: #f59e0b; margin: 0; font-size: 20px; font-weight: 800;">{{ $companyName }}</h2>
        </div>

        {{-- BANNER GELAP --}}
        <div style="background-color: #111827; color: #ffffff; padding: 30px;">
            <p style="color: #9ca3af; font-size: 10px; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 10px 0; font-weight: bold;">Hasil Foto</p>
            <h1 style="font-size: 24px; font-weight: 800; margin: 0 0 10px 0; color: #ffffff;">📷 Foto kamu sudah siap!</h1>
            <p style="color: #d1d5db; font-size: 14px; line-height: 1.6; margin: 0;">
                Halo <b>{{ $booking->client_name }}</b>, hasil foto kamu dari {{ $companyName }} sudah selesai diedit dan siap untuk didownload.
            </p>
        </div>

        {{-- BANNER DOWNLOAD (ORANYE) --}}
        <div style="background-color: #f59e0b; padding: 30px; text-align: center;">
            <p style="color: #ffffff; font-size: 11px; text-transform: uppercase; font-weight: bold; letter-spacing: 1px; margin: 0 0 12px 0;">Link Download Hasil Foto</p>
            <a href="{{ $booking->link_hasil }}" style="background-color: #ffffff; color: #d97706; padding: 14px 28px; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 15px; display: inline-block; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                📥 Buka & Download Foto &rarr;
            </a>
        </div>

        {{-- INFO & CATATAN --}}
        <div style="padding: 30px; background-color: #ffffff;">
            
            {{-- Peringatan Waktu --}}
            <div style="background-color: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
                <p style="color: #b45309; font-size: 13px; line-height: 1.5; margin: 0;">
                    <b>⚠️ Penting:</b> File foto hanya tersimpan untuk waktu terbatas sesuai kebijakan {{ $companyName }}. Segera download dan simpan di perangkat atau cloud storage kamu agar tidak kehilangan hasil fotomu.
                </p>
            </div>

            {{-- Catatan dari Admin (Hanya muncul jika ada) --}}
            @if(!empty($booking->admin_notes))
                <div style="background-color: #f8fafc; border-left: 4px solid #f59e0b; border-radius: 4px; padding: 16px; margin-bottom: 25px;">
                    <p style="color: #9ca3af; font-size: 11px; text-transform: uppercase; font-weight: bold; margin: 0 0 8px 0; letter-spacing: 0.5px;">Catatan dari {{ $companyName }}</p>
                    <p style="color: #4b5563; font-size: 14px; line-height: 1.5; margin: 0;">
                        {!! nl2br(e($booking->admin_notes)) !!}
                    </p>
                </div>
            @endif

            {{-- Detail Tabel --}}
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 25px;">
                <tr>
                    <td width="50%" style="vertical-align: top;">
                        <p style="color: #9ca3af; font-size: 10px; text-transform: uppercase; font-weight: bold; margin: 0 0 4px 0;">Kode Booking</p>
                        <p style="color: #111827; font-size: 14px; font-weight: bold; margin: 0; font-family: monospace;">{{ $bookingCode }}</p>
                    </td>
                    <td width="50%" style="vertical-align: top;">
                        <p style="color: #9ca3af; font-size: 10px; text-transform: uppercase; font-weight: bold; margin: 0 0 4px 0;">Paket</p>
                        <p style="color: #111827; font-size: 14px; font-weight: bold; margin: 0;">{{ $booking->serviceType->name ?? '-' }}</p>
                    </td>
                </tr>
            </table>

            <p style="color: #4b5563; font-size: 13px; line-height: 1.6; margin: 0;">
                Terima kasih sudah mempercayakan momen berharga kamu kepada <b>{{ $companyName }}</b>. Kami sangat senang bisa mengabadikan kenangan indah bersamamu 📸
            </p>
        </div>

        {{-- AKSES TRACKING (GELAP) --}}
        <div style="background-color: #111827; padding: 25px 30px; display: flex; align-items: center; justify-content: space-between;">
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="vertical-align: middle;">
                        <p style="color: #d1d5db; font-size: 13px; margin: 0;">Lihat detail atau akses ulang link di sini:</p>
                    </td>
                    <td style="vertical-align: middle; text-align: right;">
                        <a href="{{ url('/cek-booking/result?booking_code=' . $bookingCode . '&email=' . urlencode($booking->client_email)) }}" style="background-color: #f59e0b; color: #ffffff; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 13px; display: inline-block;">
                            Cek Booking &rarr;
                        </a>
                    </td>
                </tr>
            </table>
        </div>

        {{-- FOOTER --}}
        <div style="background-color: #f4f6f8; color: #9ca3af; padding: 25px 30px; text-align: center; font-size: 12px; line-height: 1.6;">
            @if($companyPhone)
                <p style="margin: 0 0 10px 0; color: #6b7280;">Ada pertanyaan tentang foto? <span style="color: #f59e0b; font-weight: bold;">Hubungi {{ $companyPhone }}</span></p>
            @endif
            <p style="margin: 0 0 5px 0;">Email ini dikirim otomatis oleh sistem booking {{ $companyName }}.</p>
            <p style="margin: 0 0 15px 0;">Kalau kamu tidak merasa pernah booking, abaikan email ini.</p>
            <p style="margin: 0; color: #9ca3af; font-size: 11px;">Powered by <b style="color:#f59e0b;">BookPhoto</b></p>
        </div>

    </div>
</body>
</html>