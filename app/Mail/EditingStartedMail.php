<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EditingStartedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $bookingCode;
    public $companyName;
    public $companyPhone;

    public function __construct($booking, $bookingCode, $companyName, $companyPhone)
    {
        $booking->load(['serviceType', 'user']);
        $this->booking = $booking;
        $this->bookingCode = $bookingCode;
        $this->companyName = $companyName;
        $this->companyPhone = $companyPhone;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Foto kamu sedang dalam proses editing ✏️ — ' . $this->companyName,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.editing-started',
        );
    }
}