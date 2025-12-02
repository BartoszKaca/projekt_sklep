<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;



class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    

    public function __construct(
        public string $token,
        public string $email
    ) {}

    

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Zresetuj hasło - Rap Shop',
        );
    }

    

    public function content(): Content
    {
        return new Content(
            view: 'emails.password-reset',
            with: [
                'resetUrl' => route('password.reset', ['token' => $this->token, 'email' => $this->email]),
            ]
        );
    }

    

    public function attachments(): array
    {
        return [];
    }
}
