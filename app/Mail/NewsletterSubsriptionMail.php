<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;



class NewsletterSubscriptionMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $email
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Dziękujemy za zapisanie do newslettera - Rap Shop',
            from: config('mail.from.address'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.newsletter',
            with: [
                'email' => $this->email,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}