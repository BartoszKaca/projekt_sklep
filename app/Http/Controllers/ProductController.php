<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function show($slug)
    {
        $product = Product::where('slug', $slug)
            ->with(['images', 'variants', 'category', 'reviews', 'primaryImage'])
            ->firstOrFail();

        return view('products.show', compact('product'));
    }
}