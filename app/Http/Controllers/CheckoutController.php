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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

/**
 * Controller for checkout process.
 * Handles both guest and authenticated user checkout.
 */
class CheckoutController extends Controller
{
    /**
     * Shipping costs for different methods.
     */
    protected array $shippingCosts = [
        'standard' => 12.99,
        'express' => 24.99,
        'pickup' => 0.00,
    ];

    /**
     * Display the checkout page.
     */
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
        if ($appliedCoupon) {
            $coupon = Coupon::where('code', $appliedCoupon)->first();
            if ($coupon && $coupon->isValid($cart['total'])) {
                $discount = $coupon->calculateDiscount($cart['total']);
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

    /**
     * Apply a coupon code.
     */
    public function applyCoupon(Request $request): RedirectResponse
    {
        $request->validate([
            'coupon_code' => 'required|string|max:50',
        ]);

        $cart = session('cart', ['items' => [], 'total' => 0]);
        $coupon = Coupon::where('code', $request->coupon_code)->first();

        if (!$coupon) {
            return redirect()->back()
                ->with('error', 'Nieprawidłowy kod kuponu.');
        }

        if (!$coupon->isValid($cart['total'])) {
            return redirect()->back()
                ->with('error', 'Kupon jest nieważny lub nie spełniasz wymagań minimalnego zamówienia.');
        }

        session(['applied_coupon' => $coupon->code]);

        return redirect()->back()
            ->with('success', 'Kupon został zastosowany.');
    }

    /**
     * Remove applied coupon.
     */
    public function removeCoupon(): RedirectResponse
    {
        session()->forget('applied_coupon');

        return redirect()->back()
            ->with('success', 'Kupon został usunięty.');
    }

    /**
     * Process the checkout and create order.
     */
    public function processOrder(CheckoutRequest $request): RedirectResponse
    {
        $cart = session('cart', ['items' => [], 'total' => 0]);
        
        if (empty($cart['items'])) {
            return redirect()->route('cart.index')
                ->with('error', 'Twój koszyk jest pusty.');
        }

        try {
            DB::beginTransaction();

            // Calculate totals
            $subtotal = $cart['total'];
            $shippingCost = $this->shippingCosts[$request->shipping_method] ?? $this->shippingCosts['standard'];
            $tax = 0; // VAT included in prices
            $discount = 0;

            // Apply coupon if present
            $couponCode = session('applied_coupon');
            if ($couponCode) {
                $coupon = Coupon::where('code', $couponCode)->first();
                if ($coupon && $coupon->isValid($subtotal)) {
                    $discount = $coupon->calculateDiscount($subtotal);
                    $coupon->increment('usage_count');
                }
            }

            $total = $subtotal + $shippingCost + $tax - $discount;

            // Create order
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

            // Create shipping info
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

            // Create order items and update stock
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

                // Decrease stock
                if ($variant) {
                    if ($variant->stock !== null) {
                        $variant->decrement('stock', $item['quantity']);
                    }
                } else {
                    $product->decreaseStock($item['quantity']);
                }
            }

            DB::commit();

            // Clear cart and coupon
            session()->forget('cart');
            session()->forget('applied_coupon');

            // Send confirmation email
            try {
                Mail::to($request->email)->send(new OrderConfirmationMail($order));
            } catch (\Exception $e) {
                // Log email error but don't fail the order
                \Log::error('Failed to send order confirmation email: ' . $e->getMessage());
            }

            // Redirect based on payment method
            if ($request->payment_method === 'payu') {
                return redirect()->route('payment.process', ['order' => $order->id]);
            }

            return redirect()->route('checkout.success', ['order' => $order->id]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Checkout error: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Wystąpił błąd podczas składania zamówienia: ' . $e->getMessage());
        }
    }

    /**
     * Show order success page.
     */
    public function success(Order $order): View|RedirectResponse
    {
        // Verify order belongs to current user (if logged in) or is accessible
        if (auth()->check() && $order->user_id && $order->user_id !== auth()->id()) {
            return redirect()->route('home')
                ->with('error', 'Nie masz dostępu do tego zamówienia.');
        }

        $order->load(['items.product', 'shipping']);

        return view('checkout.success', compact('order'));
    }
}
