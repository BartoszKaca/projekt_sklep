<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderShipping;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session('cart', ['items' => [], 'total' => 0]);
        
        if (empty($cart['items'])) {
            return redirect()->route('cart.index')->with('error', 'Twój koszyk jest pusty.');
        }
        
        return view('checkout.index', compact('cart'));
    }

    public function store(Request $request)
    {
        $cart = session('cart', ['items' => [], 'total' => 0]);
        
        if (empty($cart['items'])) {
            return redirect()->route('cart.index')->with('error', 'Twój koszyk jest pusty.');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'street_address' => 'required|string|max:255',
            'apartment' => 'nullable|string|max:100',
            'city' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
            'country' => 'required|string|max:100',
            'shipping_method' => 'required|in:courier,inpost,personal',
            'payment_method' => 'required|in:transfer,card,cod',
            'customer_notes' => 'nullable|string|max:500',
        ]);

        $shippingCosts = [
            'courier' => 14.99,
            'inpost' => 9.99,
            'personal' => 0,
        ];

        $subtotal = $cart['total'];
        $shippingCost = $shippingCosts[$validated['shipping_method']] ?? 0;
        $total = $subtotal + $shippingCost;

        try {
            DB::beginTransaction();

            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => auth()->id(),
                'status' => 'pending',
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'tax' => 0,
                'discount' => 0,
                'total' => $total,
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'pending',
                'customer_notes' => $validated['customer_notes'] ?? null,
            ]);

            // Create order items
            foreach ($cart['items'] as $key => $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['variant_id'] ?? null,
                    'product_name' => $item['name'],
                    'sku' => $key,
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'total' => $item['price'] * $item['quantity'],
                ]);
            }

            // Create shipping info
            OrderShipping::create([
                'order_id' => $order->id,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'street_address' => $validated['street_address'],
                'apartment' => $validated['apartment'] ?? null,
                'city' => $validated['city'],
                'postal_code' => $validated['postal_code'],
                'country' => $validated['country'],
            ]);

            // Clear cart
            session()->forget('cart');

            DB::commit();

            return redirect()->route('checkout.success', $order->order_number)
                ->with('success', 'Zamówienie zostało złożone pomyślnie!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Wystąpił błąd podczas składania zamówienia: ' . $e->getMessage()])->withInput();
        }
    }

    public function success($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->with(['items', 'shipping'])->firstOrFail();
        
        // Only allow viewing own orders (or for guest orders within session)
        if (auth()->check() && $order->user_id && $order->user_id !== auth()->id()) {
            abort(403);
        }

        return view('checkout.success', compact('order'));
    }
}
