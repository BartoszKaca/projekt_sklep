<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;


class PaymentController extends Controller
{

    private function configurePayU(): void
    {
        \OpenPayU_Configuration::setEnvironment(config('payu.environment', 'sandbox'));
        \OpenPayU_Configuration::setMerchantPosId(config('payu.pos_id'));
        \OpenPayU_Configuration::setSignatureKey(config('payu.signature_key'));
        \OpenPayU_Configuration::setOauthClientId(config('payu.client_id'));
        \OpenPayU_Configuration::setOauthClientSecret(config('payu.client_secret'));
    }


    private function configurePayUForNotifications(): void
    {
        \OpenPayU_Configuration::setEnvironment(config('payu.environment', 'sandbox'));
        \OpenPayU_Configuration::setMerchantPosId(config('payu.pos_id'));
        \OpenPayU_Configuration::setSignatureKey(config('payu.signature_key'));
    }


    public function process(Order $order): RedirectResponse|View
    {

        if (auth()->check() && $order->user_id && $order->user_id !== auth()->id()) {
            return redirect()->route('home')
                ->with('error', 'Nie masz dostępu do tego zamówienia.');
        }


        if ($order->payment_status === 'paid') {
            return redirect()->route('checkout.success', ['order' => $order->id])
                ->with('info', 'To zamówienie zostało już opłacone.');
        }


        if (!config('payu.pos_id') || !config('payu.signature_key')) {

            Log::warning('PayU not configured, falling back to bank transfer');
            $order->update(['payment_method' => 'bank_transfer']);

            return redirect()->route('checkout.success', ['order' => $order->id])
                ->with('info', 'Płatność online jest chwilowo niedostępna. Prosimy o przelew bankowy.');
        }

        try {

            $this->configurePayU();


            $orderData = [
                'notifyUrl' => route('payment.notify'),
                'continueUrl' => route('payment.return', ['order' => $order->id]),
                'customerIp' => request()->ip(),
                'merchantPosId' => config('payu.pos_id'),
                'description' => 'Zamówienie ' . $order->order_number,
                'currencyCode' => 'PLN',
                'totalAmount' => (int)($order->total * 100), 

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


            if ($order->shipping_cost > 0) {
                $orderData['products'][] = [
                    'name' => 'Dostawa',
                    'unitPrice' => (int)($order->shipping_cost * 100),
                    'quantity' => 1,
                ];
            }

            // Dodaj zniżkę z kuponu jako osobną pozycję
            if ($order->discount > 0) {
                $orderData['products'][] = [
                    'name' => 'Rabat' . ($order->coupon_code ? ' (' . $order->coupon_code . ')' : ''),
                    'unitPrice' => -(int)($order->discount * 100), // Ujemna wartość dla zniżki
                    'quantity' => 1,
                ];
            }

            $response = \OpenPayU_Order::create($orderData);
            $status = $response->getStatus(); 

            if ($status === 'SUCCESS') {
                $redirectUri = $response->getResponse()->redirectUri;
                

                $order->update([
                    'payu_order_id' => $response->getResponse()->orderId ?? null
                ]);
                
                Log::info("PayU payment initialized for order {$order->order_number}");
                
                return redirect()->away($redirectUri);
            }

            throw new \Exception('PayU order creation failed: ' . $status);

        } catch (\Exception $e) {
            Log::error('PayU payment error: ' . $e->getMessage());

            return redirect()->route('checkout.success', ['order' => $order->id])
                ->with('error', 'Wystąpił błąd podczas inicjalizacji płatności. Prosimy o przelew bankowy.');
        }
    }


    public function return(Order $order): RedirectResponse
    {

        $order->refresh();
        
        if ($order->payment_status === 'paid') {
            return redirect()->route('checkout.success', ['order' => $order->id])
                ->with('success', 'Płatność została zrealizowana pomyślnie!');
        }
        
        return redirect()->route('checkout.success', ['order' => $order->id])
            ->with('info', 'Dziękujemy za płatność. Status zostanie zaktualizowany po weryfikacji.');
    }


    public function notify(Request $request): \Illuminate\Http\JsonResponse
    {
        try {

            $this->configurePayUForNotifications();


            $body = file_get_contents('php://input');
            

            $result = \OpenPayU_Order::consumeNotification($body);

            if (!$result || !isset($result->getResponse()->order)) {
                Log::error('PayU notification: Invalid notification format');
                return response()->json(['status' => 'ERROR'], 400);
            }

            $payuOrder = $result->getResponse()->order;
            $orderNumber = $payuOrder->extOrderId ?? null;
            $payuStatus = $payuOrder->status ?? null;

            if (!$orderNumber || !$payuStatus) {
                Log::error('PayU notification: Missing order number or status');
                return response()->json(['status' => 'ERROR'], 400);
            }

            $order = Order::where('order_number', $orderNumber)->first();

            if (!$order) {
                Log::error("PayU notification: Order not found: {$orderNumber}");
                return response()->json(['status' => 'ERROR'], 404);
            }

            Log::info("PayU notification for order {$orderNumber}: {$payuStatus}");


            switch ($payuStatus) {
                case 'COMPLETED':

                    if ($order->payment_status !== 'paid') {
                        $order->markAsPaid();
                        

                        if ($order->status === 'pending') {
                            $order->update(['status' => 'processing']);
                        }
                        
                        Log::info("Order {$orderNumber} marked as paid and processing");
                    }
                    break;

                case 'CANCELED':
                    $order->update([
                        'payment_status' => 'failed',
                        'status' => 'cancelled'
                    ]);
                    Log::info("Order {$orderNumber} marked as cancelled");
                    break;

                case 'PENDING':
                case 'WAITING_FOR_CONFIRMATION':
                    if ($order->payment_status === 'pending') {
                        Log::info("Order {$orderNumber} payment still pending");
                    } else {
                        $order->update(['payment_status' => 'pending']);
                    }
                    break;

                case 'REJECTED':
                    $order->update(['payment_status' => 'failed']);
                    Log::warning("Order {$orderNumber} payment rejected");
                    break;

                default:
                    Log::warning("Order {$orderNumber} unknown PayU status: {$payuStatus}");
            }


            return response()->json(['status' => 'OK'], 200);

        } catch (\OpenPayU_Exception $e) {
            Log::error('PayU notification exception: ' . $e->getMessage());
            return response()->json(['status' => 'ERROR'], 500);
        } catch (\Exception $e) {
            Log::error('PayU notification error: ' . $e->getMessage());
            return response()->json(['status' => 'ERROR'], 500);
        }
    }


    public function checkStatus(Order $order): \Illuminate\Http\JsonResponse
    {

        if (auth()->check() && $order->user_id && $order->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $this->configurePayU();

            $response = \OpenPayU_Order::retrieve($order->order_number);

            if ($response->getStatus() === 'SUCCESS') {
                $payuStatus = $response->getResponse()->orders[0]->status ?? 'unknown';

                return response()->json([
                    'order_number' => $order->order_number,
                    'payment_status' => $order->payment_status,
                    'order_status' => $order->status,
                    'payu_status' => $payuStatus,
                ]);
            }

            return response()->json(['error' => 'Could not retrieve payment status'], 500);

        } catch (\Exception $e) {
            Log::error('PayU status check error: ' . $e->getMessage());
            return response()->json(['error' => 'Error checking payment status'], 500);
        }
    }
}
