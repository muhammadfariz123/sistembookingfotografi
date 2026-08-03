<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Proses Editing Dimulai</title>
</head>
<body style="background-color: #f4f6f8; font-family: 'Inter', Arial, sans-serif; padding: 30px 0; margin: 0;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: 1px solid #e5e7eb;">
        
        {{-- HEADER --}}
        <div style="background-color: #ffffff; padding: 25px 30px; border-bottom: 1px solid #f3f4f6;">
            <h2 style="color: #8b5cf6; margin: 0; font-size: 20px; font-weight: 800;">{{ $companyName }}</h2>
            <p style="color: #6b7280; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; margin: 4px 0 0 0; font-weight: bold;">Update Booking</p>
        </div>

        {{-- BANNER UTAMA --}}
        <div style="background-color: #111827; color: #ffffff; padding: 35px 30px; text-align: center;">
            <div style="font-size: 32px; margin-bottom: 10px;">✏️</div>
            <h1 style="font-size: 22px; font-weight: 800; margin: 0 0 10px 0; color: #ffffff;">Foto kamu sedang diedit!</h1>
            <p style="color: #9ca3af; font-size: 14px; line-height: 1.6; margin: 0; max-width: 450px; display: inline-block;">
                Halo <b>{{ $booking->client_name }}</b>, foto kamu sudah masuk ke proses editing. Kami akan mengabari kamu begitu selesai.
            </p>
        </div>

        {{-- DETAIL INFO BOOKING (Dalam Tabel agar rapi di email) --}}
        <div style="padding: 30px; background-color: #ffffff;">
            
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 25px; background: #f9fafb; border-radius: 8px; border: 1px solid #f3f4f6;">
                <tr>
                    <td width="33%" style="padding: 15px; border-right: 1px solid #e5e7eb; text-align: center;">
                        <p style="color: #6b7280; font-size: 10px; text-transform: uppercase; font-weight: bold; margin: 0 0 4px 0;">Status</p>
                        <p style="color: #8b5cf6; font-size: 13px; font-weight: bold; margin: 0;">Proses Editing</p>
                    </td>
                    <td width="33%" style="padding: 15px; border-right: 1px solid #e5e7eb; text-align: center;">
                        <p style="color: #6b7280; font-size: 10px; text-transform: uppercase; font-weight: bold; margin: 0 0 4px 0;">Posisi Antrian</p>
                        <p style="color: #111827; font-size: 14px; font-weight: bold; margin: 0;">#{{ $booking->queue_number ?? '-' }}</p>
                    </td>
                    <td width="33%" style="padding: 15px; text-align: center;">
                        <p style="color: #6b7280; font-size: 10px; text-transform: uppercase; font-weight: bold; margin: 0 0 4px 0;">Estimasi Selesai</p>
                        <p style="color: #111827; font-size: 13px; font-weight: bold; margin: 0;">
                            {{ $booking->estimate_date ? \Carbon\Carbon::parse($booking->estimate_date)->translatedFormat('d M Y') : 'Menyusul' }}
                        </p>
                    </td>
                </tr>
            </table>

            <table width="100%" cellpadding="0" cellspacing="0" style="border-bottom: 1px solid #f3f4f6; padding-bottom: 20px; margin-bottom: 20px;">
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

            <p style="color: #4b5563; font-size: 13px; line-height: 1.5; margin: 0 0 25px 0;">
                Pantau progress kamu di halaman tracking booking. Kami akan mengirimkan notifikasi begitu hasil foto siap untuk didownload.
            </p>

            {{-- TOMBOL AKSI KE HALAMAN TRACKING --}}
            <div style="text-align: center; margin-bottom: 10px;">
                <p style="color: #4b5563; font-size: 13px; margin: 0 0 12px 0;">Pantau status editing di sini:</p>
                <a href="{{ url('/cek-booking/result?booking_code=' . $bookingCode . '&email=' . urlencode($booking->client_email)) }}" style="background-color: #8b5cf6; color: #ffffff; padding: 14px 28px; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 14px; display: inline-block; box-shadow: 0 2px 4px rgba(139, 92, 246, 0.3);">
                    Cek Status &rarr;
                </a>
            </div>
        </div>

        {{-- FOOTER --}}
        <div style="background-color: #111827; color: #9ca3af; padding: 25px 30px; text-align: center; font-size: 12px; line-height: 1.6;">
            @if($companyPhone)
                <p style="margin: 0 0 10px 0; color: #ffffff;">Ada pertanyaan? Hubungi <span style="color: #8b5cf6; font-weight: bold;">{{ $companyPhone }}</span></p>
            @endif
            <p style="margin: 0 0 5px 0;">Email ini dikirim otomatis oleh sistem booking {{ $companyName }}.</p>
            <p style="margin: 0 0 15px 0;">Kalau kamu tidak merasa pernah booking, abaikan email ini.</p>
            <p style="margin: 0; color: #6b7280; font-size: 11px;">Powered by <b>BookPhoto</b></p>
        </div>

    </div>
</body>
</html>