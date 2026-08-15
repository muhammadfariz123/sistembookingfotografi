<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentConfirmedToCustomer extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $bookingCode;
    public $companyName;
    public $companyPhone;
    
    public $currentPaymentAmount; // Nominal transaksi yang barusan dikonfirmasi
    public $paymentTypeString;    // Label: "Pelunasan", "Pembayaran penuh", atau "DP (uang muka)"
    public $subjectString;        // Subjek Email
    public $isFullyPaid;          // Cek apakah hasil akhirnya Lunas (Boolean)
    public $sessionTime;          // Waktu Sesi (Otomatis hitung durasi jam selesai)

    public function __construct(Booking $booking, $bookingCode, $companyName, $companyPhone, $currentPaymentAmount)
    {
        $this->booking = $booking;
        $this->bookingCode = $bookingCode;
        $this->companyName = $companyName;
        $this->companyPhone = $companyPhone;
        $this->currentPaymentAmount = $currentPaymentAmount;

        // LOGIKA PENENTUAN LABEL & SUBJEK EMAIL CUSTOMER
        $type = strtoupper($booking->payment_type);
        
        if ($type === 'PELUNASAN') {
            $this->paymentTypeString = 'Pelunasan';
            $this->subjectString = 'Pembayaran pelunasan diterima • ' . $bookingCode;
            $this->isFullyPaid = true;
        } elseif ($type === 'LUNAS') {
            $this->paymentTypeString = 'Pembayaran penuh';
            $this->subjectString = 'Pembayaran diterima • ' . $bookingCode;
            $this->isFullyPaid = true;
        } else {
            $this->paymentTypeString = 'DP (uang muka)';
            $this->subjectString = 'Pembayaran DP diterima • ' . $bookingCode;
            $this->isFullyPaid = false;
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
        return $this->subject($this->subjectString)
                    ->view('emails.payment_confirmed_to_customer');
    }
}