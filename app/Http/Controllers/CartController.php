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

        // Sprawdź czy produkt ma warianty
        $hasVariants = $product->variants()->count() > 0;

        $variant = null;
        if ($request->variant_id) {
            $variant = ProductVariant::where('id', $request->variant_id)
                ->where('product_id', $product->id)
                ->first();
            if (!$variant) {
                return response()->json(['success' => false, 'message' => 'Wariant nie znaleziony.'], 404);
            }
            $price = $variant->getFinalPrice();
            $stock = $variant->stock_quantity;
            $itemKey = 'v'.$variant->id;
        } else {
            // Jeśli produkt ma warianty, wymagaj wyboru
            if ($hasVariants) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Wybierz rozmiar przed dodaniem do koszyka.',
                    'requires_variant' => true
                ], 400);
            }
            
            // Produkt bez wariantów
            $price = $product->getFinalPrice() ?? $product->price;
            $stock = $product->stock_quantity;
            $itemKey = 'p'.$product->id;
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
                'variant_name' => $variant ? $variant->name : null,
                'size' => $variant ? $variant->size : null,
                'color' => $variant ? $variant->color : null,
                'price' => (float) ($variant ? $variant->getFinalPrice() : ($product->getFinalPrice() ?? $product->price)),
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
            // Sprawdź stan magazynowy przed aktualizacją
            $item = $cart['items'][$key];
            $product = Product::find($item['product_id']);
            
            if (!$product) {
                return response()->json(['success' => false, 'message' => 'Produkt nie znaleziony'], 404);
            }

            $stock = null;
            if ($item['variant_id']) {
                $variant = ProductVariant::find($item['variant_id']);
                if (!$variant) {
                    return response()->json(['success' => false, 'message' => 'Wariant nie znaleziony'], 404);
                }
                $stock = $variant->stock_quantity;
            } else {
                $stock = $product->stock_quantity;
            }

            if ($stock !== null && $stock < $qty) {
                return response()->json([
                    'success' => false, 
                    'message' => "Dostępne tylko {$stock} sztuk w magazynie."
                ], 400);
            }

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