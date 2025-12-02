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
        // Wymaga logowania wszędzie oprócz strony głównej i listy produktów
        $this->middleware('auth')->except(['index', 'products']);
    }

    // Strona główna
    public function index(): View
    {
        // Pobierz wyróżnione produkty (8 najnowszych)
        $featuredProducts = Product::where('is_active', 1)
            ->where('is_featured', 1)
            ->with(['primaryImage', 'category', 'reviews'])
            ->take(8)
            ->get();

        // Pobierz najnowsze produkty (8 ostatnich)
        $latestProducts = Product::where('is_active', 1)
            ->with(['primaryImage', 'category'])
            ->latest()
            ->take(8)
            ->get();

        // Pobierz aktywne kategorie
        $categories = Category::active()->withCount(['products' => function($q) {
            $q->where('is_active', 1);
        }])->get();

        return view('home', compact('featuredProducts', 'latestProducts', 'categories'));
    }

    // Lista wszystkich produktów z filtrami
    public function products(Request $request): View
    {
        // Początkowe zapytanie - aktywne produkty
        $query = Product::where('is_active', 1)
            ->with(['primaryImage', 'category', 'reviews']);

        // Filtr po kategorii
        if ($request->filled('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filtr po minimalnej cenie
        if ($request->filled('min_price')) {
            $query->where(function($q) use ($request) {
                $q->where('price', '>=', $request->min_price)
                  ->orWhere('discount_price', '>=', $request->min_price);
            });
        }

        // Filtr po maksymalnej cenie
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

        // Filtr po typie (album/merch)
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filtr po formacie (CD/Vinyl/Kaseta)
        if ($request->filled('format')) {
            $query->where('format', $request->format);
        }

        // Wyszukiwanie po nazwie, opisie lub artyście
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('artist', 'like', "%{$search}%");
            });
        }

        // Sortowanie wyników
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

        // Paginacja - 20 produktów na stronę
        $products = $query->paginate(20)->withQueryString();
        
        // Pobierz kategorie do filtrów
        $categories = Category::active()->get();

        // Oblicz zakres cen dla suwaka
        $priceRange = Product::where('is_active', 1)->selectRaw('
            MIN(COALESCE(discount_price, price)) as min_price,
            MAX(COALESCE(discount_price, price)) as max_price
        ')->first();

        return view('products.index', compact('products', 'categories', 'priceRange'));
    }
}
