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
    public $sessionTime; // Waktu Sesi (Otomatis hitung durasi jam selesai)

    /**
     * Create a new message instance.
     */
    public function __construct(Booking $booking, $bookingCode, $companyName, $companyPhone)
    {
        $this->booking = $booking;
        $this->bookingCode = $bookingCode;
        $this->companyName = $companyName;
        $this->companyPhone = $companyPhone;

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
        return $this->subject('Booking kamu sudah kami terima • ' . $this->bookingCode)
                    ->view('emails.customer_booking_received');
    }
}