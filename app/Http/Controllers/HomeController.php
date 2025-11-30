<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

/**
 * Controller for the home page and product listings.
 * Provides filtering, sorting, and search functionality.
 */
class HomeController extends Controller
{
    public function __construct()
    {
        // Pozwól gościom zobaczyć stronę główną
        $this->middleware('auth')->except(['index', 'search', 'products']);
    }

    /**
     * Display the home page with featured and latest products.
     */
    public function index(): View
    {
        $featuredProducts = Product::where('is_active', 1)
            ->where('is_featured', 1)
            ->with(['primaryImage', 'category', 'reviews'])
            ->take(8)
            ->get();

        $latestProducts = Product::where('is_active', 1)
            ->with(['primaryImage', 'category'])
            ->latest()
            ->take(8)
            ->get();

        $categories = Category::active()->withCount(['products' => function($q) {
            $q->where('is_active', 1);
        }])->get();

        return view('home', compact('featuredProducts', 'latestProducts', 'categories'));
    }

    /**
     * Display all products with filtering and sorting.
     */
    public function products(Request $request): View
    {
        $query = Product::where('is_active', 1)
            ->with(['primaryImage', 'category', 'reviews']);

        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where(function($q) use ($request) {
                $q->where('price', '>=', $request->min_price)
                  ->orWhere('discount_price', '>=', $request->min_price);
            });
        }

        if ($request->filled('max_price')) {
            $query->where(function($q) use ($request) {
                $q->where(function($inner) use ($request) {
                    $inner->whereNotNull('discount_price')
                          ->where('discount_price', '<=', $request->max_price);
                })->orWhere(function($inner) use ($request) {
                    $inner->whereNull('discount_price')
                          ->where('price', '<=', $request->max_price);
                });
            });
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by format
        if ($request->filled('format')) {
            $query->where('format', $request->format);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('artist', 'like', "%{$search}%");
            });
        }

        // Sorting
        switch ($request->get('sort', 'newest')) {
            case 'price_asc':
                $query->orderByRaw('COALESCE(discount_price, price) ASC');
                break;
            case 'price_desc':
                $query->orderByRaw('COALESCE(discount_price, price) DESC');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'popular':
                $query->orderBy('views_count', 'desc');
                break;
            case 'newest':
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(20)->withQueryString();
        $categories = Category::active()->get();

        // Get price range for filters
        $priceRange = Product::where('is_active', 1)->selectRaw('
            MIN(COALESCE(discount_price, price)) as min_price,
            MAX(COALESCE(discount_price, price)) as max_price
        ')->first();

        return view('products.index', compact('products', 'categories', 'priceRange'));
    }

    /**
     * Search products via AJAX.
     */
    public function search(Request $request): \Illuminate\Http\JsonResponse
    {
        $search = $request->get('q', '');
        
        if (strlen($search) < 2) {
            return response()->json(['products' => []]);
        }

        $products = Product::where('is_active', 1)
            ->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('artist', 'like', "%{$search}%");
            })
            ->with(['primaryImage', 'category'])
            ->take(10)
            ->get()
            ->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'artist' => $product->artist,
                    'price' => $product->getFinalPrice(),
                    'url' => route('products.show', $product->slug),
                    'image' => $product->primaryImage ? asset('storage/' . $product->primaryImage->path) : null,
                    'category' => $product->category->name ?? '',
                ];
            });

        return response()->json(['products' => $products]);
    }
}