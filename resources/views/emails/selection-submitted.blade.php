<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pilihan Foto Diterima</title>
</head>
<body style="background-color: #f4f6f8; font-family: 'Inter', Arial, sans-serif; padding: 30px 0; margin: 0;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: 1px solid #e5e7eb;">
        
        {{-- HEADER --}}
        <div style="background-color: #ffffff; padding: 25px 30px; border-bottom: 1px solid #f3f4f6;">
            <h2 style="color: #f59e0b; margin: 0; font-size: 20px; font-weight: 800;">{{ $companyName }}</h2>
            <p style="color: #6b7280; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; margin: 4px 0 0 0; font-weight: bold;">Seleksi Foto</p>
        </div>

        {{-- BANNER UTAMA --}}
        <div style="background-color: #111827; color: #ffffff; padding: 35px 30px; text-align: center;">
            <div style="font-size: 32px; margin-bottom: 10px;">🖼️</div>
            <h1 style="font-size: 22px; font-weight: 800; margin: 0 0 10px 0; color: #ffffff;">Pilihan fotomu sudah diterima!</h1>
            <p style="color: #9ca3af; font-size: 14px; line-height: 1.6; margin: 0; max-width: 450px; display: inline-block;">
                Halo <b>{{ $booking->client_name }}</b>, pilihan foto kamu sudah kami terima. Tim kami akan segera memproses foto yang kamu pilih.
            </p>
        </div>

        {{-- KOTAK HIGHLIGHT FOTO DIPILIH --}}
        <div style="background-color: #f59e0b; color: #ffffff; padding: 25px; text-align: center;">
            <p style="font-size: 11px; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 5px 0; font-weight: bold; opacity: 0.9;">Foto Dipilih</p>
            <h2 style="font-size: 36px; font-weight: 900; margin: 0;">{{ $totalSelected }} foto</h2>
        </div>

        {{-- DETAIL INFO BOOKING --}}
        <div style="padding: 30px; background-color: #ffffff;">
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
                Jika ingin mengubah pilihan, kamu masih bisa kembali ke halaman seleksi sebelum proses editing dimulai.
            </p>

            {{-- TOMBOL AKSI KE HALAMAN SELEKSI --}}
            <div style="text-align: center; margin-bottom: 30px;">
                <p style="color: #4b5563; font-size: 13px; margin: 0 0 12px 0;">Lihat atau ubah pilihan foto kamu:</p>
                <a href="{{ url('/seleksi/' . $bookingCode) }}" style="background-color: #f59e0b; color: #ffffff; padding: 14px 28px; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 14px; display: inline-block; box-shadow: 0 2px 4px rgba(245, 158, 11, 0.3);">
                    Lihat Pilihan &rarr;
                </a>
            </div>
        </div>

        {{-- FOOTER --}}
        <div style="background-color: #111827; color: #9ca3af; padding: 25px 30px; text-align: center; font-size: 12px; line-height: 1.6;">
            @if($companyPhone)
                <p style="margin: 0 0 10px 0; color: #ffffff;">Ada pertanyaan? Hubungi <span style="color: #f59e0b; font-weight: bold;">{{ $companyPhone }}</span></p>
            @endif
            <p style="margin: 0 0 5px 0;">Email ini dikirim otomatis oleh sistem booking {{ $companyName }}.</p>
            <p style="margin: 0 0 15px 0;">Kalau kamu tidak merasa pernah booking, abaikan email ini.</p>
            <p style="margin: 0; color: #6b7280; font-size: 11px;">Powered by <b>BookPhoto</b></p>
        </div>

    </div>
</body>
</html>