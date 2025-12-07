<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'primaryImage']);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%')
                  ->orWhere('artist', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('stock')) {
            if ($request->stock === 'low') {
                $query->lowStock();
            } elseif ($request->stock === 'out') {
                $query->where('stock_quantity', 0);
            }
        }

        $products = $query->latest()->paginate(20);
        $categories = Category::all();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::active()->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:album,merch',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'artist' => 'nullable|string|max:255',
            'release_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'format' => 'nullable|string',
            'label' => 'nullable|string|max:255',
            'stock_quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:1',
            'sku' => 'required|string|unique:products',
            'barcode' => 'nullable|string',
            'weight' => 'nullable|numeric|min:0',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'variants' => 'nullable|array',
            'variants.*.size' => 'nullable|string|max:10',
            'variants.*.color' => 'nullable|string|max:50',
            'variants.*.stock_quantity' => 'nullable|integer|min:0',
            'variants.*.price_modifier' => 'nullable|numeric',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        
        // Obsługa pól boolean - gdy checkbox nie jest zaznaczony, nie jest wysyłany
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;
        $validated['is_featured'] = $request->has('is_featured') ? 1 : 0;
        
        $product = Product::create($validated);

        // Obsługa wariantów (rozmiarów)
        if ($request->has('variants') && $validated['type'] === 'merch') {
            foreach ($request->variants as $index => $variantData) {
                if (!empty($variantData['size']) || !empty($variantData['color'])) {
                    $variantName = trim(($variantData['size'] ?? '') . ' ' . ($variantData['color'] ?? ''));
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'name' => $variantName ?: 'Wariant ' . ($index + 1),
                        'size' => $variantData['size'] ?? null,
                        'color' => $variantData['color'] ?? null,
                        'stock_quantity' => $variantData['stock_quantity'] ?? 0,
                        'price_modifier' => $variantData['price_modifier'] ?? 0,
                        'sku' => $product->sku . '-' . strtoupper($variantData['size'] ?? 'V' . $index),
                        'is_active' => true,
                    ]);
                }
            }
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'path' => $path,
                    'is_primary' => $index === 0,
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Produkt został dodany pomyślnie!');
    }

    public function edit(Product $product)
    {
        $product->load(['category', 'images', 'variants']);
        $categories = Category::active()->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:album,merch',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'artist' => 'nullable|string|max:255',
            'release_year' => 'nullable|integer',
            'format' => 'nullable|string',
            'label' => 'nullable|string|max:255',
            'stock_quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:1',
            'sku' => 'required|string|unique:products,sku,' . $product->id,
            'barcode' => 'nullable|string',
            'weight' => 'nullable|numeric|min:0',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'variants' => 'nullable|array',
            'variants.*.id' => 'nullable|integer|exists:product_variants,id',
            'variants.*.size' => 'nullable|string|max:10',
            'variants.*.color' => 'nullable|string|max:50',
            'variants.*.stock_quantity' => 'nullable|integer|min:0',
            'variants.*.price_modifier' => 'nullable|numeric',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        
        // Obsługa pól boolean - gdy checkbox nie jest zaznaczony, nie jest wysyłany
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;
        $validated['is_featured'] = $request->has('is_featured') ? 1 : 0;
        
        $product->update($validated);

        // Obsługa wariantów (rozmiarów)
        if ($validated['type'] === 'merch') {
            $existingVariantIds = [];
            
            if ($request->has('variants')) {
                foreach ($request->variants as $index => $variantData) {
                    if (!empty($variantData['size']) || !empty($variantData['color'])) {
                        $variantName = trim(($variantData['size'] ?? '') . ' ' . ($variantData['color'] ?? ''));
                        
                        if (!empty($variantData['id'])) {
                            // Update existing variant
                            $variant = ProductVariant::find($variantData['id']);
                            if ($variant && $variant->product_id === $product->id) {
                                $oldStock = $variant->stock_quantity;
                                $newStock = $variantData['stock_quantity'] ?? 0;
                                
                                $variant->update([
                                    'name' => $variantName ?: $variant->name,
                                    'size' => $variantData['size'] ?? null,
                                    'color' => $variantData['color'] ?? null,
                                    'stock_quantity' => $newStock,
                                    'price_modifier' => $variantData['price_modifier'] ?? 0,
                                ]);
                                
                                // Twórz ruch magazynowy jeśli zmienił się stan
                                if ($oldStock != $newStock) {
                                    $difference = $newStock - $oldStock;
                                    $type = $difference > 0 ? 'in' : 'out';
                                    $quantity = abs($difference);
                                    
                                    StockMovement::create([
                                        'product_id' => $product->id,
                                        'product_variant_id' => $variant->id,
                                        'type' => $type,
                                        'quantity' => $quantity,
                                        'stock_before' => $oldStock,
                                        'stock_after' => $newStock,
                                        'reason' => 'manual_adjustment',
                                        'reference' => 'Edycja produktu',
                                        'user_id' => auth()->id(),
                                    ]);
                                }
                                
                                $existingVariantIds[] = $variant->id;
                            }
                        } else {
                            // Create new variant
                            $newStock = $variantData['stock_quantity'] ?? 0;
                            
                            $variant = ProductVariant::create([
                                'product_id' => $product->id,
                                'name' => $variantName ?: 'Wariant ' . ($index + 1),
                                'size' => $variantData['size'] ?? null,
                                'color' => $variantData['color'] ?? null,
                                'stock_quantity' => $newStock,
                                'price_modifier' => $variantData['price_modifier'] ?? 0,
                                'sku' => $product->sku . '-' . strtoupper($variantData['size'] ?? 'V' . time()),
                                'is_active' => true,
                            ]);
                            
                            // Twórz ruch magazynowy dla nowego wariantu z początkowym stanem
                            if ($newStock > 0) {
                                StockMovement::create([
                                    'product_id' => $product->id,
                                    'product_variant_id' => $variant->id,
                                    'type' => 'in',
                                    'quantity' => $newStock,
                                    'stock_before' => 0,
                                    'stock_after' => $newStock,
                                    'reason' => 'initial_stock',
                                    'reference' => 'Dodanie nowego wariantu',
                                    'user_id' => auth()->id(),
                                ]);
                            }
                            
                            $existingVariantIds[] = $variant->id;
                        }
                    }
                }
            }
            
            // Delete removed variants
            $product->variants()->whereNotIn('id', $existingVariantIds)->delete();
        } else {
            // If type changed from merch to album, remove all variants
            $product->variants()->delete();
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Produkt został zaktualizowany!');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')
            ->with('success', 'Produkt został usunięty!');
    }

    public function stock(Product $product)
    {
        try {
            $product->load(['variants', 'category']);
            $movements = $product->stockMovements()
                ->with(['user', 'variant'])
                ->latest()
                ->paginate(20);

            return view('admin.products.stock', compact('product', 'movements'));
        } catch (\Exception $e) {
            \Log::error('Stock page error: ' . $e->getMessage(), [
                'product_id' => $product->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('admin.products.index')
                ->with('error', 'Wystąpił błąd podczas ładowania strony stanu magazynowego.');
        }
    }

    public function adjustStock(Request $request, Product $product)
    {
        $validated = $request->validate([
            'variant_id' => 'nullable|integer|exists:product_variants,id',
            'type' => 'required|in:in,out,adjustment',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string',
            'reference' => 'nullable|string',
        ]);

        // Jeśli wybrano wariant, obsłuż go osobno
        if (!empty($validated['variant_id'])) {
            $variant = ProductVariant::where('id', $validated['variant_id'])
                ->where('product_id', $product->id)
                ->firstOrFail();
            
            $stockBefore = $variant->stock_quantity;

            if ($validated['type'] === 'in') {
                $variant->increment('stock_quantity', $validated['quantity']);
            } elseif ($validated['type'] === 'out') {
                if ($variant->stock_quantity < $validated['quantity']) {
                    return back()->withErrors(['quantity' => 'Niewystarczająca ilość na stanie wariantu!']);
                }
                $variant->decrement('stock_quantity', $validated['quantity']);
            } else {
                $variant->update(['stock_quantity' => $validated['quantity']]);
            }

            StockMovement::create([
                'product_id' => $product->id,
                'product_variant_id' => $variant->id,
                'type' => $validated['type'],
                'quantity' => $validated['quantity'],
                'stock_before' => $stockBefore,
                'stock_after' => $variant->fresh()->stock_quantity,
                'reason' => $validated['reason'],
                'reference' => $validated['reference'] ?? null,
                'user_id' => auth()->user()->id ?? null,
            ]);

            return back()->with('success', 'Stan magazynowy wariantu został zaktualizowany!');
        }

        // Obsługa produktu głównego (bez wariantu)
        $stockBefore = $product->stock_quantity;

        if ($validated['type'] === 'in') {
            $product->increment('stock_quantity', $validated['quantity']);
        } elseif ($validated['type'] === 'out') {
            if ($product->stock_quantity < $validated['quantity']) {
                return back()->withErrors(['quantity' => 'Niewystarczająca ilość na stanie!']);
            }
            $product->decrement('stock_quantity', $validated['quantity']);
        } else {
            $product->update(['stock_quantity' => $validated['quantity']]);
        }

        $product->stockMovements()->create([
            'type' => $validated['type'],
            'quantity' => $validated['quantity'],
            'stock_before' => $stockBefore,
            'stock_after' => $product->fresh()->stock_quantity,
            'reason' => $validated['reason'],
            'reference' => $validated['reference'] ?? null,
            'user_id' => auth()->user()->id ?? null,
        ]);

        return back()->with('success', 'Stan magazynowy został zaktualizowany!');
    }

    public function adjustVariantStock(Request $request, ProductVariant $variant)
    {
        $validated = $request->validate([
            'type' => 'required|in:in,out,adjustment',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string',
            'reference' => 'nullable|string',
        ]);

        $stockBefore = $variant->stock_quantity;

        if ($validated['type'] === 'in') {
            $variant->increaseStock($validated['quantity'], $validated['reason'], $validated['reference'], auth()->id());
        } elseif ($validated['type'] === 'out') {
            if ($variant->stock_quantity < $validated['quantity']) {
                return back()->withErrors(['quantity' => 'Niewystarczająca ilość na stanie!']);
            }
            $variant->decreaseStock($validated['quantity']);
            // Update reason and reference
            $variant->stockMovements()->latest()->first()->update([
                'reason' => $validated['reason'],
                'reference' => $validated['reference'],
                'user_id' => auth()->user()->id,
            ]);
        } else {
            $variant->update(['stock_quantity' => $validated['quantity']]);
            StockMovement::create([
                'product_id' => $variant->product_id,
                'product_variant_id' => $variant->id,
                'type' => 'adjustment',
                'quantity' => $validated['quantity'],
                'stock_before' => $stockBefore,
                'stock_after' => $variant->stock_quantity,
                'reason' => $validated['reason'],
                'reference' => $validated['reference'] ?? null,
                'user_id' => auth()->user()->id ?? null,
            ]);
        }

        return back()->with('success', 'Stan magazynowy wariantu został zaktualizowany!');
    }
}