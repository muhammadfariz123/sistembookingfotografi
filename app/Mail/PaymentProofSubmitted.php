<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentProofSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $bookingCode;
    public $amountToPay;
    public $paymentTypeText;
    public $sessionTime; // Tambahan untuk durasi sesi

    public function __construct(Booking $booking, $bookingCode, $amountToPay)
    {
        $this->booking = $booking;
        $this->bookingCode = $bookingCode;
        $this->amountToPay = $amountToPay;

        // LOGIKA BARU: MEMBEDAKAN 3 JENIS PEMBAYARAN
        $type = strtoupper($booking->payment_type);
        if ($type === 'PELUNASAN') {
            $this->paymentTypeText = 'Pelunasan';
        } elseif ($type === 'LUNAS') {
            $this->paymentTypeText = 'Lunas Penuh';
        } else {
            $this->paymentTypeText = 'DP';
        }

        // LOGIKA WAKTU SESI BERDASARKAN DURASI
        $waktuMulaiStr = $booking->booking_time ? \Carbon\Carbon::parse($booking->booking_time)->format('H:i') : null;
        $durasiJam = $booking->serviceType->duration ?? 0;
        
        if ($waktuMulaiStr && $durasiJam > 0) {
            $waktuSelesaiStr = \Carbon\Carbon::parse($booking->booking_time)->addHours($durasiJam)->format('H:i');
            $this->sessionTime = "{$waktuMulaiStr} - {$waktuSelesaiStr} WIB";
        } else {
            $this->sessionTime = $waktuMulaiStr ? "{$waktuMulaiStr} WIB" : '-';
        }
    }

    public function build()
    {
        return $this->subject('⏳ Bukti Transfer ' . $this->paymentTypeText . ' — Booking ' . $this->bookingCode . ' Menunggu Konfirmasi')
                    ->view('emails.payment_proof_submitted');
    }
}