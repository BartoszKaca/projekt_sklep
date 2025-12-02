<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;



class OrderStatusUpdateMail extends Mailable
{
    use Queueable, SerializesModels;

    

    public function __construct(
        public Order $order,
        public string $oldStatus,
        public string $newStatus
    ) {}

    

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Aktualizacja statusu zamówienia #' . $this->order->order_number,
        );
    }

    

    public function content(): Content
    {
        $statusNames = [
            'pending' => 'Oczekujące',
            'processing' => 'W realizacji',
            'shipped' => 'Wysłane',
            'delivered' => 'Dostarczone',
            'cancelled' => 'Anulowane',
        ];

        return new Content(
            view: 'emails.order-status-update',
            with: [
                'oldStatusName' => $statusNames[$this->oldStatus] ?? $this->oldStatus,
                'newStatusName' => $statusNames[$this->newStatus] ?? $this->newStatus,
            ]
        );
    }

    

    public function attachments(): array
    {
        return [];
    }
}
