<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminManualMessage extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $recipientName,
        public string $messageBody,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pesan dari Admin',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-manual-message',
        );
    }
}
