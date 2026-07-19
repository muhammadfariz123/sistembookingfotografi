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
    }

    public function build()
    {
        return $this->subject('⏳ Bukti Transfer ' . $this->paymentTypeText . ' — Booking ' . $this->bookingCode . ' Menunggu Konfirmasi')
                    ->view('emails.payment_proof_submitted');
    }
}