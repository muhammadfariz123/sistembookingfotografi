<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomerBookingReceived extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $bookingCode;
    // VARIABEL DI BAWAH INI HARUS SAMA PERSIS DENGAN YANG DIPANGGIL DI BLADE
    public $companyName; 
    public $companyPhone;

    /**
     * Create a new message instance.
     */
    public function __construct(Booking $booking, $bookingCode, $companyName, $companyPhone)
    {
        $this->booking = $booking;
        $this->bookingCode = $bookingCode;
        $this->companyName = $companyName;
        $this->companyPhone = $companyPhone;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Booking kamu sudah kami terima • ' . $this->bookingCode)
                    ->view('emails.customer_booking_received');
    }
}