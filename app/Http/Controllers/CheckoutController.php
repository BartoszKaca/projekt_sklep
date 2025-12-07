<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Mail\OrderConfirmationMail;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderShipping;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;


class CheckoutController extends Controller
{

    protected array $shippingCosts = [
        'standard' => 12.99,
        'express' => 24.99,
        'pickup' => 0.00,
    ];


    public function index(): View|RedirectResponse
    {
        $cart = session('cart', ['items' => [], 'total' => 0]);

        if (empty($cart['items'])) {
            return redirect()->route('cart.index')
                ->with('error', 'Twój koszyk jest pusty.');
        }

        $user = auth()->user();
        $addresses = $user ? $user->addresses()->orderBy('is_default', 'desc')->get() : collect();
        $defaultAddress = $user ? $user->defaultAddress : null;

        $shippingMethods = [
            'standard' => ['name' => 'Kurier DPD (2-3 dni)', 'price' => $this->shippingCosts['standard']],
            'express' => ['name' => 'Kurier ekspresowy (1 dzień)', 'price' => $this->shippingCosts['express']],
            'pickup' => ['name' => 'Odbiór osobisty', 'price' => $this->shippingCosts['pickup']],
        ];

        $paymentMethods = [
            'cash_on_delivery' => 'Płatność przy odbiorze',
            'bank_transfer' => 'Przelew bankowy',
            'payu' => 'PayU (karta/BLIK)',
        ];

        $appliedCoupon = session('applied_coupon');
        $discount = 0;
        $coupon = null;
        
        if ($appliedCoupon) {
            $coupon = Coupon::where('code', $appliedCoupon)->first();
            if ($coupon && $coupon->isValid($cart['total'])) {
                $discount = $coupon->calculateDiscount($cart['total']);
                // Debug log
                Log::info('Coupon applied in checkout', [
                    'code' => $appliedCoupon,
                    'cart_total' => $cart['total'],
                    'discount' => $discount,
                    'coupon_type' => $coupon->type,
                    'coupon_value' => $coupon->value
                ]);
            } else {
                // Kupon nieważny - usuń go z sesji
                $invalidCode = $appliedCoupon; // Zapisz przed usunięciem
                session()->forget('applied_coupon');
                $appliedCoupon = null;
                Log::warning('Invalid coupon removed from session', [
                    'code' => $invalidCode,
                    'coupon_exists' => $coupon ? 'yes' : 'no',
                    'is_valid' => $coupon ? ($coupon->isValid($cart['total']) ? 'yes' : 'no') : 'N/A',
                    'cart_total' => $cart['total']
                ]);
            }
        }

        return view('checkout.index', compact(
            'cart',
            'user',
            'addresses',
            'defaultAddress',
            'shippingMethods',
            'paymentMethods',
            'appliedCoupon',
            'discount'
        ));
    }


    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string|max:50',
        ]);

        $cart = session('cart', ['items' => [], 'total' => 0]);
        $code = strtoupper(trim($request->coupon_code));
        $coupon = Coupon::where('code', $code)->first();

        Log::info('Attempting to apply coupon', [
            'code' => $code,
            'cart_total' => $cart['total'],
            'coupon_found' => $coupon ? 'yes' : 'no'
        ]);

        if (!$coupon) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nieprawidłowy kod kuponu.'
                ], 400);
            }
            return redirect()->back()
                ->with('error', 'Nieprawidłowy kod kuponu.');
        }

        if (!$coupon->isValid($cart['total'])) {
            Log::warning('Coupon invalid', [
                'code' => $code,
                'is_active' => $coupon->is_active,
                'valid_from' => $coupon->valid_from,
                'valid_until' => $coupon->valid_until,
                'usage_count' => $coupon->usage_count,
                'usage_limit' => $coupon->usage_limit,
                'min_order_value' => $coupon->min_order_value,
                'cart_total' => $cart['total']
            ]);
            
            $errorMessage = 'Kupon jest nieważny lub nie spełniasz wymagań minimalnego zamówienia.';
            
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 400);
            }
            
            return redirect()->back()
                ->with('error', $errorMessage);
        }

        session(['applied_coupon' => $coupon->code]);
        
        $discount = $coupon->calculateDiscount($cart['total']);
        Log::info('Coupon applied successfully', [
            'code' => $coupon->code,
            'discount' => $discount
        ]);

        $successMessage = 'Kupon został zastosowany.';
        
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMessage,
                'discount' => $discount,
                'coupon_code' => $coupon->code
            ]);
        }

        return redirect()->back()
            ->with('success', $successMessage);
    }


    public function removeCoupon(Request $request)
    {
        session()->forget('applied_coupon');

        $successMessage = 'Kupon został usunięty.';
        
        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMessage
            ]);
        }

        return redirect()->back()
            ->with('success', $successMessage);
    }


    public function processOrder(CheckoutRequest $request): RedirectResponse
    {
        $cart = session('cart', ['items' => [], 'total' => 0]);

        if (empty($cart['items'])) {
            return redirect()->route('cart.index')
                ->with('error', 'Twój koszyk jest pusty.');
        }

        try {
            DB::beginTransaction();

            

            foreach ($cart['items'] as $item) {
                $product = Product::lockForUpdate()->find($item['product_id']);
                if (!$product) {
                    throw new \Exception("Produkt {$item['name']} nie jest już dostępny.");
                }

                if ($item['variant_id']) {
                    $variant = ProductVariant::lockForUpdate()->find($item['variant_id']);
                    if (!$variant) {
                        throw new \Exception("Wariant produktu {$item['name']} nie jest już dostępny.");
                    }
                    if ($variant->stock_quantity !== null && $variant->stock_quantity < $item['quantity']) {
                        throw new \Exception("Produkt {$item['name']} ma tylko {$variant->stock_quantity} sztuk w magazynie.");
                    }
                } else {
                    if ($product->stock_quantity !== null && $product->stock_quantity < $item['quantity']) {
                        throw new \Exception("Produkt {$item['name']} ma tylko {$product->stock_quantity} sztuk w magazynie.");
                    }
                }
            }

            

            $subtotal = $cart['total'];
            $shippingCost = $this->shippingCosts[$request->shipping_method] ?? $this->shippingCosts['standard'];
            $tax = 0; 

            $discount = 0;

            

            $couponCode = session('applied_coupon');
            if ($couponCode) {
                $coupon = Coupon::where('code', $couponCode)->first();
                if ($coupon && $coupon->isValid($subtotal)) {
                    $discount = $coupon->calculateDiscount($subtotal);
                    $coupon->increment('usage_count');
                } else {
                    // Kupon nieważny - usuń z sesji
                    session()->forget('applied_coupon');
                    $couponCode = null;
                }
            }

            $total = $subtotal + $shippingCost + $tax - $discount;

            

            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => auth()->id(),
                'status' => 'pending',
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'tax' => $tax,
                'discount' => $discount,
                'total' => $total,
                'coupon_code' => $couponCode,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'customer_notes' => $request->customer_notes,
            ]);

            

            OrderShipping::create([
                'order_id' => $order->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'street_address' => $request->street_address,
                'apartment' => $request->apartment,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
                'country' => $request->country,
                'phone' => $request->phone,
                'email' => $request->email,
            ]);

            

            foreach ($cart['items'] as $key => $item) {
                $product = Product::find($item['product_id']);
                if (!$product) {
                    throw new \Exception("Produkt nie istnieje: {$item['name']}");
                }

                $variant = null;
                if ($item['variant_id']) {
                    $variant = ProductVariant::find($item['variant_id']);
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_variant_id' => $item['variant_id'],
                    'product_name' => $item['name'],
                    'variant_name' => $variant ? $variant->name : null,
                    'sku' => $product->sku,
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'total' => $item['price'] * $item['quantity'],
                ]);

                

                if ($variant) {
                    if ($variant->stock_quantity !== null) {
                        $variant->decreaseStock($item['quantity'], $order->id);
                    }
                } else {
                    $product->decreaseStock($item['quantity']);
                    // Link movement to order
                    $product->stockMovements()->latest()->first()->update(['order_id' => $order->id]);
                }
            }

            DB::commit();

            

            session()->forget('cart');
            session()->forget('applied_coupon');

            

            try {
                Mail::to($request->email)->send(new OrderConfirmationMail($order));
            } catch (\Exception $e) {
                

                Log::error('Failed to send order confirmation email: ' . $e->getMessage());
            }

            

            if ($request->payment_method === 'payu') {
                return redirect()->route('payment.process', ['order' => $order->id]);
            }

            return redirect()->route('checkout.success', ['order' => $order->id]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout error: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Wystąpił błąd podczas składania zamówienia: ' . $e->getMessage());
        }
    }


    public function success(Order $order): View|RedirectResponse
    {
        

        if (auth()->check() && $order->user_id && $order->user_id !== auth()->id()) {
            return redirect()->route('home')
                ->with('error', 'Nie masz dostępu do tego zamówienia.');
        }

        $order->load(['items.product', 'shipping']);

        return view('checkout.success', compact('order'));
    }
}