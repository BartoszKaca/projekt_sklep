<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except('index');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
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
