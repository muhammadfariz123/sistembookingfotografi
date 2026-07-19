<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminPaymentApprovedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $bookingCode;
    public $isLunas;
    public $amountPaid;

    /**
     * Create a new message instance.
     */
    public function __construct(Booking $booking, $bookingCode, $amountPaid)
    {
        $this->booking = $booking;
        $this->bookingCode = $bookingCode;
        $this->amountPaid = $amountPaid;

        // Cek secara dinamis untuk menentukan subjek email
        $this->isLunas = strtoupper($booking->payment_type) === 'LUNAS' || strtoupper($booking->payment_type) === 'PELUNASAN';
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subjectTag = $this->isLunas ? '[Lunas]' : '[DP]';
        
        return $this->subject($subjectTag . ' Booking ' . $this->bookingCode)
                    ->view('emails.admin_payment_approved_notification');
    }
}