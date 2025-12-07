<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use OpenPayU_Exception;
use OpenPayU_Exception_Authorization;
use OpenPayU_Exception_ServerError;
use OpenPayU_Exception_ServerMaintenance;


class PaymentController extends Controller
{

    private function configurePayU(): void
    {
        $environment = config('payu.environment', 'sandbox');
        $posId = config('payu.pos_id');
        $signatureKey = config('payu.signature_key');
        $clientId = config('payu.client_id');
        $clientSecret = config('payu.client_secret');

        // Loguj konfigurację (bez pełnych kluczy)
        Log::debug('PayU configuration', [
            'environment' => $environment,
            'pos_id' => $posId,
            'has_signature_key' => !empty($signatureKey),
            'has_client_id' => !empty($clientId),
            'has_client_secret' => !empty($clientSecret)
        ]);

        \OpenPayU_Configuration::setEnvironment($environment);
        \OpenPayU_Configuration::setMerchantPosId($posId);
        \OpenPayU_Configuration::setSignatureKey($signatureKey);
        \OpenPayU_Configuration::setOauthClientId($clientId);
        \OpenPayU_Configuration::setOauthClientSecret($clientSecret);
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

        // Opcjonalny tryb symulacji (tylko jeśli włączony)
        if (config('payu.simulate', false)) {
            Log::info('PayU simulation mode - automatically approving payment', [
                'order' => $order->order_number
            ]);
            
            // Symuluj opłacone zamówienie
            $order->update([
                'payment_status' => 'paid',
                'paid_at' => now(),
                'status' => 'processing'
            ]);
            
            return redirect()->route('checkout.success', ['order' => $order->id])
                ->with('success', 'Płatność została symulowana.');
        }

        // Sprawdź konfigurację PayU
        $posId = config('payu.pos_id');
        $signatureKey = config('payu.signature_key');
        $clientId = config('payu.client_id');
        $clientSecret = config('payu.client_secret');
        $environment = config('payu.environment', 'sandbox');

        if (!$posId || !$signatureKey || !$clientId || !$clientSecret) {
            Log::warning('PayU not fully configured', [
                'has_pos_id' => !empty($posId),
                'has_signature_key' => !empty($signatureKey),
                'has_client_id' => !empty($clientId),
                'has_client_secret' => !empty($clientSecret),
                'environment' => $environment
            ]);
            
            $order->update(['payment_method' => 'bank_transfer']);

            return redirect()->route('checkout.success', ['order' => $order->id])
                ->with('info', 'Płatność online jest chwilowo niedostępna. Prosimy o przelew bankowy.');
        }

        try {
            Log::info('PayU configuration', [
                'environment' => $environment,
                'pos_id' => $posId,
                'has_client_credentials' => !empty($clientId) && !empty($clientSecret)
            ]);

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

            Log::info('PayU order creation attempt', [
                'order_number' => $order->order_number,
                'total' => $order->total,
                'environment' => config('payu.environment'),
                'pos_id' => config('payu.pos_id'),
                'has_products' => count($orderData['products']) > 0
            ]);

            $response = \OpenPayU_Order::create($orderData);
            $status = $response->getStatus(); 

            Log::info('PayU order creation response', [
                'status' => $status,
                'order_number' => $order->order_number
            ]);

            if ($status === 'SUCCESS') {
                $redirectUri = $response->getResponse()->redirectUri;
                

                $order->update([
                    'payu_order_id' => $response->getResponse()->orderId ?? null
                ]);
                
                Log::info("PayU payment initialized successfully", [
                    'order' => $order->order_number,
                    'payu_order_id' => $response->getResponse()->orderId ?? null,
                    'redirect_uri' => $redirectUri
                ]);
                
                return redirect()->away($redirectUri);
            }

            // Jeśli status nie jest SUCCESS, loguj szczegóły odpowiedzi
            $errorDetails = 'Status: ' . $status;
            if (method_exists($response, 'getResponse') && $response->getResponse()) {
                $responseObj = $response->getResponse();
                if (isset($responseObj->status)) {
                    $errorDetails .= ', PayU Status: ' . ($responseObj->status->statusCode ?? 'N/A');
                    $errorDetails .= ', Message: ' . ($responseObj->status->statusDesc ?? 'N/A');
                }
            }
            
            Log::error('PayU order creation failed', [
                'order' => $order->order_number,
                'status' => $status,
                'details' => $errorDetails
            ]);

            throw new \Exception('PayU order creation failed: ' . $errorDetails);

        } catch (\OpenPayU_Exception_Authorization $e) {
            Log::error('PayU authorization error', [
                'message' => $e->getMessage(),
                'environment' => config('payu.environment'),
                'pos_id' => config('payu.pos_id'),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Błąd autoryzacji - sprawdź czy dane są poprawne dla sandbox
            $errorMessage = 'Błąd autoryzacji PayU sandbox. ';
            if (config('payu.environment') === 'sandbox') {
                $errorMessage .= 'Sprawdź czy dane dostępowe (POS ID, klucze) są poprawne dla środowiska sandbox. ';
            }
            $errorMessage .= 'Sprawdź logi serwera dla szczegółów.';
            
            return redirect()->route('checkout.success', ['order' => $order->id])
                ->with('error', $errorMessage);
                
        } catch (\OpenPayU_Exception_ServerMaintenance $e) {
            Log::error('PayU server maintenance', [
                'message' => $e->getMessage(),
                'environment' => config('payu.environment')
            ]);
            
            return redirect()->route('checkout.success', ['order' => $order->id])
                ->with('error', 'System płatności PayU sandbox jest aktualnie w konserwacji. Spróbuj ponownie za chwilę lub wybierz inną metodę płatności.');
                
        } catch (\OpenPayU_Exception_ServerError $e) {
            Log::error('PayU server error', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'environment' => config('payu.environment'),
                'trace' => $e->getTraceAsString()
            ]);
            
            $errorMessage = 'Wystąpił błąd po stronie PayU sandbox. ';
            if (strpos(strtolower($e->getMessage()), 'unavailable') !== false) {
                $errorMessage .= 'System jest chwilowo niedostępny. ';
            }
            $errorMessage .= 'Sprawdź logi serwera lub spróbuj ponownie za chwilę.';
            
            return redirect()->route('checkout.success', ['order' => $order->id])
                ->with('error', $errorMessage);
                
        } catch (\Exception $e) {
            Log::error('PayU payment error', [
                'message' => $e->getMessage(),
                'class' => get_class($e),
                'environment' => config('payu.environment'),
                'trace' => $e->getTraceAsString()
            ]);

            // Sprawdź czy to komunikat o niedostępności systemu
            $isUnavailable = strpos(strtolower($e->getMessage()), 'unavailable') !== false 
                          || strpos(strtolower($e->getMessage()), 'niedostępny') !== false
                          || strpos(strtolower($e->getMessage()), 'system is unavailable') !== false;

            if ($isUnavailable) {
                return redirect()->route('checkout.success', ['order' => $order->id])
                    ->with('error', 'System płatności PayU sandbox jest chwilowo niedostępny. Spróbuj ponownie za chwilę lub wybierz inną metodę płatności.');
            }

            return redirect()->route('checkout.success', ['order' => $order->id])
                ->with('error', 'Wystąpił błąd podczas inicjalizacji płatności PayU. Sprawdź logi serwera dla szczegółów.');
        }
    }


    public function return(Order $order): RedirectResponse
    {
        $order->refresh();
        
        // Jeśli już opłacone - sukces
        if ($order->payment_status === 'paid') {
            return redirect()->route('checkout.success', ['order' => $order->id])
                ->with('success', 'Płatność została zrealizowana pomyślnie!');
        }
        
        // Sprawdź aktywnie status płatności w PayU
        if ($order->payu_order_id) {
            try {
                $this->configurePayU();
                
                $response = \OpenPayU_Order::retrieve($order->payu_order_id);
                
                if ($response->getStatus() === 'SUCCESS') {
                    $orders = $response->getResponse()->orders ?? [];
                    
                    if (!empty($orders)) {
                        $payuStatus = $orders[0]->status ?? null;
                        
                        Log::info("PayU return check for order {$order->order_number}: status = {$payuStatus}");
                        
                        if ($payuStatus === 'COMPLETED') {
                            // Płatność zakończona - oznacz jako opłacone
                            if ($order->payment_status !== 'paid') {
                                $order->markAsPaid();
                                
                                if ($order->status === 'pending') {
                                    $order->update(['status' => 'confirmed']);
                                }
                                
                                Log::info("Order {$order->order_number} marked as paid on return");
                            }
                            
                            return redirect()->route('checkout.success', ['order' => $order->id])
                                ->with('success', 'Płatność została zrealizowana pomyślnie!');
                        } elseif ($payuStatus === 'CANCELED' || $payuStatus === 'REJECTED') {
                            $order->update(['payment_status' => 'failed']);
                            
                            return redirect()->route('checkout.success', ['order' => $order->id])
                                ->with('error', 'Płatność została anulowana lub odrzucona. Możesz spróbować ponownie.');
                        } elseif ($payuStatus === 'PENDING' || $payuStatus === 'WAITING_FOR_CONFIRMATION') {
                            return redirect()->route('checkout.success', ['order' => $order->id])
                                ->with('info', 'Płatność jest w trakcie przetwarzania. Status zostanie zaktualizowany automatycznie.');
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error('PayU status check on return failed: ' . $e->getMessage());
            }
        }
        
        return redirect()->route('checkout.success', ['order' => $order->id])
            ->with('info', 'Dziękujemy za złożenie zamówienia. Status płatności zostanie zaktualizowany po weryfikacji.');
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
        // Sprawdź uprawnienia - ale pozwól na dostęp do własnych zamówień lub zamówień gości
        if (auth()->check() && $order->user_id && $order->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Jeśli już opłacone, zwróć status od razu
        if ($order->payment_status === 'paid') {
            return response()->json([
                'order_number' => $order->order_number,
                'payment_status' => $order->payment_status,
                'order_status' => $order->status,
            ]);
        }

        // Sprawdź status w PayU jeśli mamy payu_order_id
        if ($order->payu_order_id) {
            try {
                $this->configurePayU();

                $response = \OpenPayU_Order::retrieve($order->payu_order_id);

                if ($response->getStatus() === 'SUCCESS') {
                    $orders = $response->getResponse()->orders ?? [];
                    
                    if (!empty($orders)) {
                        $payuStatus = $orders[0]->status ?? 'unknown';
                        
                        // Jeśli płatność zakończona - zaktualizuj status
                        if ($payuStatus === 'COMPLETED' && $order->payment_status !== 'paid') {
                            $order->markAsPaid();
                            
                            if ($order->status === 'pending') {
                                $order->update(['status' => 'confirmed']);
                            }
                            
                            $order->refresh();
                            Log::info("Order {$order->order_number} marked as paid via status check");
                        } elseif (($payuStatus === 'CANCELED' || $payuStatus === 'REJECTED') && $order->payment_status !== 'failed') {
                            $order->update(['payment_status' => 'failed']);
                            $order->refresh();
                        }

                        return response()->json([
                            'order_number' => $order->order_number,
                            'payment_status' => $order->payment_status,
                            'order_status' => $order->status,
                            'payu_status' => $payuStatus,
                        ]);
                    }
                }
            } catch (\Exception $e) {
                Log::error('PayU status check error: ' . $e->getMessage());
            }
        }

        // Zwróć aktualny status z bazy
        return response()->json([
            'order_number' => $order->order_number,
            'payment_status' => $order->payment_status,
            'order_status' => $order->status,
        ]);
    }
}
