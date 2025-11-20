<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class HomeController extends Controller
{
    public function __construct()
    {
        // pozwól gościom zobaczyć stronę główną, chronij inne metody
        $this->middleware('auth')->except('index');
    }

    public function index()
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

        return view('home', compact('featuredProducts', 'latestProducts'));
    }
}