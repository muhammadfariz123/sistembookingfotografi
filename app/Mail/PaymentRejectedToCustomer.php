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