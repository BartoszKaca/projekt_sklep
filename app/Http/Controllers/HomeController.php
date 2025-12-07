<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function __construct()
    {
        

        $this->middleware('auth')->except(['index', 'products']);
    }

    

    public function index(): View
    {
        

        $featuredProducts = Product::where('is_active', 1)
            ->where('is_featured', 1)
            ->with(['primaryImage', 'category', 'reviews', 'variants'])
            ->take(8)
            ->get();

        

        $latestProducts = Product::where('is_active', 1)
            ->with(['primaryImage', 'category', 'variants'])
            ->latest()
            ->take(8)
            ->get();

        

        $categories = Category::active()->withCount(['products' => function($q) {
            $q->where('is_active', 1);
        }])->get();

        return view('home', compact('featuredProducts', 'latestProducts', 'categories'));
    }

    

    public function products(Request $request): View
    {
        $query = Product::where('is_active', 1)
            ->with(['primaryImage', 'category', 'reviews']);

        // Filtr po kategoriach (wielokrotny wybór)
        if ($request->filled('categories')) {
            $categoryIds = $request->input('categories', []);
            if (is_array($categoryIds) && count($categoryIds) > 0) {
                $query->whereIn('category_id', $categoryIds);
            }
        }

        // Filtr po typie produktu
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filtr po cenie minimalnej
        if ($request->filled('price_min')) {
            $query->whereRaw('COALESCE(discount_price, price) >= ?', [$request->price_min]);
        }

        // Filtr po cenie maksymalnej
        if ($request->filled('price_max')) {
            $query->whereRaw('COALESCE(discount_price, price) <= ?', [$request->price_max]);
        }

        // Filtr po formacie (wielokrotny wybór)
        if ($request->filled('formats')) {
            $formats = $request->input('formats', []);
            if (is_array($formats) && count($formats) > 0) {
                $query->whereIn('format', $formats);
            }
        }

        // Filtr dostępności w magazynie
        if ($request->filled('in_stock')) {
            $query->where('stock_quantity', '>', 0);
        }

        // Filtr produktów w promocji
        if ($request->filled('on_sale')) {
            $query->whereNotNull('discount_price');
        }

        // Wyszukiwanie
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('artist', 'like', "%{$search}%");
            });
        }

        // Sortowanie
        switch ($request->get('sort')) {
            case 'price_asc':
                $query->orderByRaw('COALESCE(discount_price, price) ASC');
                break;
            case 'price_desc':
                $query->orderByRaw('COALESCE(discount_price, price) DESC');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'newest':
                $query->latest();
                break;
            default:
                $query->latest();
                break;
        }

        // Paginacja z zachowaniem parametrów
        $products = $query->with('variants')->paginate(20)->withQueryString();
        
        // Pobierz kategorie z liczbą produktów
        $categories = Category::active()
            ->withCount(['products' => function($q) {
                $q->where('is_active', 1);
            }])
            ->get();

        // Zakres cen dla informacji
        $priceRange = Product::where('is_active', 1)->selectRaw('
            MIN(COALESCE(discount_price, price)) as min_price,
            MAX(COALESCE(discount_price, price)) as max_price
        ')->first();

        return view('products.index', compact('products', 'categories', 'priceRange'));
    }
}
