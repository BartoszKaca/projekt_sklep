<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Controller for payment processing.
 * Handles PayU integration for online payments.
 */
class PaymentController extends Controller
{
    /**
     * Configure PayU SDK with credentials.
     */
    private function configurePayU(): void
    {
        \OpenPayU_Configuration::setEnvironment(config('payu.environment', 'sandbox'));
        \OpenPayU_Configuration::setMerchantPosId(config('payu.pos_id'));
        \OpenPayU_Configuration::setSignatureKey(config('payu.signature_key'));
        \OpenPayU_Configuration::setOauthClientId(config('payu.client_id'));
        \OpenPayU_Configuration::setOauthClientSecret(config('payu.client_secret'));
    }

    /**
     * Configure PayU SDK with minimal credentials for notifications.
     */
    private function configurePayUForNotifications(): void
    {
        \OpenPayU_Configuration::setEnvironment(config('payu.environment', 'sandbox'));
        \OpenPayU_Configuration::setMerchantPosId(config('payu.pos_id'));
        \OpenPayU_Configuration::setSignatureKey(config('payu.signature_key'));
    }

    /**
     * Initialize PayU payment.
     */
    public function process(Order $order): RedirectResponse|View
    {
        // Verify order ownership
        if (auth()->check() && $order->user_id && $order->user_id !== auth()->id()) {
            return redirect()->route('home')
                ->with('error', 'Nie masz dostępu do tego zamówienia.');
        }

        // Check if order is already paid
        if ($order->payment_status === 'paid') {
            return redirect()->route('checkout.success', ['order' => $order->id])
                ->with('info', 'To zamówienie zostało już opłacone.');
        }

        // Check if PayU is configured
        if (!config('payu.pos_id') || !config('payu.signature_key')) {
            // Fallback to bank transfer if PayU not configured
            Log::warning('PayU not configured, falling back to bank transfer');
            $order->update(['payment_method' => 'bank_transfer']);
            
            return redirect()->route('checkout.success', ['order' => $order->id])
                ->with('info', 'Płatność online jest chwilowo niedostępna. Prosimy o przelew bankowy.');
        }

        try {
            // Initialize PayU SDK
            $this->configurePayU();

            // Prepare order data
            $orderData = [
                'notifyUrl' => route('payment.notify'),
                'continueUrl' => route('payment.return', ['order' => $order->id]),
                'customerIp' => request()->ip(),
                'merchantPosId' => config('payu.pos_id'),
                'description' => 'Zamówienie ' . $order->order_number,
                'currencyCode' => 'PLN',
                'totalAmount' => (int)($order->total * 100), // Amount in cents
                'extOrderId' => $order->order_number,
                'buyer' => [
                    'email' => $order->shipping->email,
                    'phone' => $order->shipping->phone,
                    'firstName' => $order->shipping->first_name,
                    'lastName' => $order->shipping->last_name,
                ],
                'products' => $order->items->map(function ($item) {
                    return [
                        'name' => $item->product_name,
                        'unitPrice' => (int)($item->price * 100),
                        'quantity' => $item->quantity,
                    ];
                })->toArray(),
            ];

            // Add shipping as product if applicable
            if ($order->shipping_cost > 0) {
                $orderData['products'][] = [
                    'name' => 'Dostawa',
                    'unitPrice' => (int)($order->shipping_cost * 100),
                    'quantity' => 1,
                ];
            }

            $response = \OpenPayU_Order::create($orderData);
            $status = $response->getStatus();

            if ($status === 'SUCCESS') {
                $redirectUri = $response->getResponse()->redirectUri;
                return redirect()->away($redirectUri);
            }

            throw new \Exception('PayU order creation failed: ' . $status);

        } catch (\Exception $e) {
            Log::error('PayU payment error: ' . $e->getMessage());
            
            return redirect()->route('checkout.success', ['order' => $order->id])
                ->with('error', 'Wystąpił błąd podczas inicjalizacji płatności. Prosimy o przelew bankowy.');
        }
    }

    /**
     * Handle return from PayU.
     */
    public function return(Order $order): RedirectResponse
    {
        return redirect()->route('checkout.success', ['order' => $order->id])
            ->with('info', 'Dziękujemy za płatność. Status zostanie zaktualizowany po weryfikacji.');
    }

    /**
     * Handle PayU webhook notification.
     */
    public function notify(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            // Initialize PayU SDK
            $this->configurePayUForNotifications();

            $body = file_get_contents('php://input');
            $result = \OpenPayU_Order::consumeNotification($body);

            if ($result->getResponse()->order->extOrderId) {
                $orderNumber = $result->getResponse()->order->extOrderId;
                $payuStatus = $result->getResponse()->order->status;

                $order = Order::where('order_number', $orderNumber)->first();

                if ($order) {
                    switch ($payuStatus) {
                        case 'COMPLETED':
                            $order->markAsPaid();
                            $order->update(['status' => 'processing']);
                            break;
                        case 'CANCELED':
                            $order->update(['payment_status' => 'failed', 'status' => 'cancelled']);
                            break;
                        case 'PENDING':
                            $order->update(['payment_status' => 'pending']);
                            break;
                    }
                }
            }

            return response()->json(['status' => 'OK']);

        } catch (\Exception $e) {
            Log::error('PayU notification error: ' . $e->getMessage());
            return response()->json(['status' => 'ERROR'], 500);
        }
    }
}
