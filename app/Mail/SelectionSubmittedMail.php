<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SelectionSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $bookingCode;
    public $companyName;
    public $companyPhone;
    public $totalSelected;

    public function __construct($booking, $bookingCode, $companyName, $companyPhone, $totalSelected)
    {
        $booking->load(['serviceType', 'user']);
        $this->booking = $booking;
        $this->bookingCode = $bookingCode;
        $this->companyName = $companyName;
        $this->companyPhone = $companyPhone;
        $this->totalSelected = $totalSelected;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pilihan foto kamu sudah diterima — ' . $this->companyName,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.selection-submitted',
        );
    }
}