<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    protected $sessionKey = 'cart';

    protected function getCart(): array
    {
        return session($this->sessionKey, ['items' => [], 'total' => 0]);
    }

    protected function saveCart(array $cart): void
    {
        $total = 0;
        foreach ($cart['items'] as $item) {
            $total += ($item['price'] * $item['quantity']);
        }
        $cart['total'] = $total;
        session([$this->sessionKey => $cart]);
    }

    public function index()
    {
        $cart = $this->getCart();
        return view('cart.index', compact('cart'));
    }


    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'variant_id' => 'nullable|integer',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $product = Product::find($request->product_id);
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Produkt nie znaleziony.'], 404);
        }

        $quantity = max(1, (int) $request->quantity);


        $variant = null;
        if ($request->variant_id) {
            $variant = ProductVariant::find($request->variant_id);
            if (!$variant) {
                return response()->json(['success' => false, 'message' => 'Wariant nie znaleziony.'], 404);
            }
            $price = $variant->price ?? $product->getFinalPrice() ?? $product->price;
            $stock = $variant->stock ?? null;
            $itemKey = 'v'.$variant->id;
        } else {

            $variant = $product->variants()->first();
            if ($variant) {
                $price = $variant->price ?? $product->getFinalPrice() ?? $product->price;
                $stock = $variant->stock ?? null;
                $itemKey = 'v'.$variant->id;
            } else {
                $price = $product->getFinalPrice() ?? $product->price;
                $stock = $product->stock ?? null; 

                $itemKey = 'p'.$product->id;
            }
        }


        if ($stock !== null && $stock < $quantity) {
            return response()->json(['success' => false, 'message' => 'Brak wystarczającego stanu magazynowego.'], 400);
        }

        $cart = $this->getCart();
        if (!isset($cart['items'][$itemKey])) {
            $cart['items'][$itemKey] = [
                'product_id' => $product->id,
                'variant_id' => $variant ? $variant->id : null,
                'name' => $product->name,
                'price' => (float) $price,
                'quantity' => $quantity,
                'slug' => $product->slug,
                'image' => optional($product->primaryImage)->path ?? null,
            ];
        } else {
            $cart['items'][$itemKey]['quantity'] += $quantity;
        }

        $this->saveCart($cart);


        $count = 0;
        foreach ($cart['items'] as $it) { $count += $it['quantity']; }

        return response()->json([
            'success' => true,
            'message' => 'Dodano do koszyka',
            'cart_count' => $count,
            'cart_total' => $cart['total']
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'item_key' => 'required|string',
            'quantity' => 'required|integer|min:0',
        ]);

        $cart = $this->getCart();
        $key = $request->item_key;

        if (!isset($cart['items'][$key])) {
            return response()->json(['success' => false, 'message' => 'Pozycja nie znaleziona'], 404);
        }

        $qty = (int) $request->quantity;
        if ($qty <= 0) {
            unset($cart['items'][$key]);
        } else {
            $cart['items'][$key]['quantity'] = $qty;
        }

        $this->saveCart($cart);

        $count = 0;
        foreach ($cart['items'] as $it) { $count += $it['quantity']; }

        return response()->json(['success' => true, 'cart_count' => $count, 'cart_total' => $cart['total']]);
    }

    public function remove(Request $request)
    {
        $request->validate([
            'item_key' => 'required|string'
        ]);

        $cart = $this->getCart();
        $key = $request->item_key;

        if (isset($cart['items'][$key])) {
            unset($cart['items'][$key]);
            $this->saveCart($cart);
        }

        $count = 0;
        foreach ($cart['items'] as $it) { $count += $it['quantity']; }

        return response()->json(['success' => true, 'cart_count' => $count, 'cart_total' => $cart['total']]);
    }

    public function count()
    {
        $cart = $this->getCart();
        $count = 0;
        foreach ($cart['items'] as $it) { $count += $it['quantity']; }

        return response()->json(['count' => $count, 'total' => $cart['total']]);
    }
}