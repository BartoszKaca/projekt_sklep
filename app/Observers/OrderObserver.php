<?php

namespace App\Observers;

use App\Mail\OrderStatusUpdateMail;
use App\Mail\PaymentConfirmationMail;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderObserver
{
    /**
     * Tymczasowe przechowywanie informacji o zmianach statusu
     * (nie możemy używać właściwości modelu, bo Laravel próbuje je zapisać do bazy)
     */
    protected static array $statusChanges = [];
    protected static array $paymentChanges = [];

    /**
     * Handle the Order "updating" event.
     */
    public function updating(Order $order): void
    {
        // Sprawdź czy zmienił się status zamówienia
        if ($order->isDirty('status')) {
            $oldStatus = $order->getOriginal('status');
            $newStatus = $order->status;

            // Zapisz informacje o zmianie statusu (używamy ID zamówienia jako klucza)
            self::$statusChanges[$order->id] = [
                'oldStatus' => $oldStatus,
                'newStatus' => $newStatus,
            ];
        }

        // Sprawdź czy zmienił się status płatności
        if ($order->isDirty('payment_status')) {
            $oldPaymentStatus = $order->getOriginal('payment_status');
            $newPaymentStatus = $order->payment_status;

            // Jeśli płatność została potwierdzona
            if ($oldPaymentStatus !== 'paid' && $newPaymentStatus === 'paid') {
                self::$paymentChanges[$order->id] = true;
            }
        }
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        // Załaduj relacje jeśli nie są załadowane
        if (!$order->relationLoaded('shipping')) {
            $order->load('shipping');
        }
        if (!$order->relationLoaded('items')) {
            $order->load('items');
        }

        // Wyślij email o zmianie statusu
        if (isset(self::$statusChanges[$order->id])) {
            $change = self::$statusChanges[$order->id];
            
            // Pobierz email odbiorcy
            $email = $order->shipping?->email;
            
            if ($email) {
                try {
                    Mail::to($email)->send(
                        new OrderStatusUpdateMail($order, $change['oldStatus'], $change['newStatus'])
                    );
                    Log::info("Order status update email sent for order {$order->order_number} to {$email}");
                } catch (\Exception $e) {
                    Log::error('Failed to send order status update email: ' . $e->getMessage());
                }
            } else {
                Log::warning("Cannot send order status email - no shipping email for order {$order->order_number}");
            }
            
            // Wyczyść po wysłaniu
            unset(self::$statusChanges[$order->id]);
        }

        // Wyślij email potwierdzenia płatności
        if (isset(self::$paymentChanges[$order->id])) {
            // Pobierz email odbiorcy
            $email = $order->shipping?->email;
            
            if ($email) {
                try {
                    Mail::to($email)->send(
                        new PaymentConfirmationMail($order)
                    );
                    Log::info("Payment confirmation email sent for order {$order->order_number} to {$email}");
                } catch (\Exception $e) {
                    Log::error('Failed to send payment confirmation email: ' . $e->getMessage());
                }
            } else {
                Log::warning("Cannot send payment confirmation email - no shipping email for order {$order->order_number}");
            }
            
            // Wyczyść po wysłaniu
            unset(self::$paymentChanges[$order->id]);
        }
    }
}
