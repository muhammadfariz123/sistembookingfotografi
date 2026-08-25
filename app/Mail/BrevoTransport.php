<?php

namespace App\Mail;

use Illuminate\Support\Facades\Http;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Email;

class BrevoTransport extends AbstractTransport
{
    protected $apiKey;

    public function __construct($apiKey, $dispatcher = null, $logger = null)
    {
        parent::__construct($dispatcher, $logger);
        $this->apiKey = $apiKey;
    }

    protected function doSend(SentMessage $message): void
    {
        $email = $message->getOriginalMessage();

        if (!$email instanceof Email) {
            throw new \Exception("Only Symfony\Component\Mime\Email is supported by BrevoTransport");
        }

        // Format sender (from)
        $from = $email->getFrom();
        $sender = null;
        if (!empty($from)) {
            $sender = [
                'email' => $from[0]->getAddress(),
                'name' => $from[0]->getName() ?: null,
            ];
        }

        // Format recipients (to)
        $to = [];
        foreach ($email->getTo() as $address) {
            $to[] = [
                'email' => $address->getAddress(),
                'name' => $address->getName() ?: null,
            ];
        }

        // Build payload
        $payload = [
            'sender' => $sender,
            'to' => $to,
            'subject' => $email->getSubject(),
            'htmlContent' => $email->getHtmlBody(),
        ];

        // Add text content if present
        if ($email->getTextBody()) {
            $payload['textContent'] = $email->getTextBody();
        }

        // Send API request to Brevo
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'api-key' => $this->apiKey,
            'content-type' => 'application/json',
        ])->post('https://api.brevo.com/v3/smtp/email', $payload);

        if (!$response->successful()) {
            $keyPrefix = substr($this->apiKey, 0, 8);
            $keyLen = strlen($this->apiKey);
            throw new \Exception("Brevo HTTP API failed to send email: " . $response->body() . " (Used Key Prefix: '{$keyPrefix}', Length: {$keyLen})");
        }
    }

    public function __toString(): string
    {
        return 'brevo';
    }
}
