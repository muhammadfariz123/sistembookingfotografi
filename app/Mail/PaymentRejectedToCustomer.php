<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentRejectedToCustomer extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $bookingCode;
    public $companyName;
    public $companyPhone;
    public $rejectReason;
    public $paymentTypeText;
    public $sessionTime; // Tambahan untuk durasi sesi

    /**
     * Create a new message instance.
     */
    public function __construct(Booking $booking, $bookingCode, $companyName, $companyPhone, $rejectReason)
    {
        $this->booking = $booking;
        $this->bookingCode = $bookingCode;
        $this->companyName = $companyName;
        $this->companyPhone = $companyPhone;
        $this->rejectReason = $rejectReason;

        // Mendeteksi jenis pembayaran untuk dicetak dinamis di subjek dan body
        $this->paymentTypeText = strtoupper($booking->payment_type) === 'LUNAS' ? 'Lunas Penuh' : 'DP';

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

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('❌ Bukti Transfer Booking ' . $this->bookingCode . ' Ditolak')
                    ->view('emails.payment_rejected_to_customer');
    }
}