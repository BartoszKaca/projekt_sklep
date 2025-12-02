<?php

namespace App\Observers;

use App\Mail\OrderStatusUpdateMail;
use App\Mail\PaymentConfirmationMail;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderObserver
{
    

    public function updating(Order $order): void
    {
        

        if ($order->isDirty('status')) {
            $oldStatus = $order->getOriginal('status');
            $newStatus = $order->status;

            

            $order->_oldStatus = $oldStatus;
            $order->_statusChanged = true;
        }

        

        if ($order->isDirty('payment_status')) {
            $oldPaymentStatus = $order->getOriginal('payment_status');
            $newPaymentStatus = $order->payment_status;

            

            if ($oldPaymentStatus !== 'paid' && $newPaymentStatus === 'paid') {
                $order->_paymentConfirmed = true;
            }
        }
    }

    

    public function updated(Order $order): void
    {
        

        if (isset($order->_statusChanged) && $order->_statusChanged && $order->shipping) {
            try {
                Mail::to($order->shipping->email)->send(
                    new OrderStatusUpdateMail($order, $order->_oldStatus, $order->status)
                );
            } catch (\Exception $e) {
                Log::error('Failed to send order status update email: ' . $e->getMessage());
            }
        }

        

        if (isset($order->_paymentConfirmed) && $order->_paymentConfirmed && $order->shipping) {
            try {
                Mail::to($order->shipping->email)->send(
                    new PaymentConfirmationMail($order)
                );
            } catch (\Exception $e) {
                Log::error('Failed to send payment confirmation email: ' . $e->getMessage());
            }
        }
    }
}
