<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;



class EmailVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    

    public function __construct(
        public User $user,
        

        public ?string $verificationPayload = null
    ) {}

    

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Potwierdź swój adres email - Rap Shop',
        );
    }

    

    public function content(): Content
    {
        return new Content(
            view: 'emails.email-verification',
        );
    }

    

    public function attachments(): array
    {
        return [];
    }
}
