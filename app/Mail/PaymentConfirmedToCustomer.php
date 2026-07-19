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
    }

    public function build()
    {
        return $this->subject($this->subjectString)
                    ->view('emails.payment_confirmed_to_customer');
    }
}