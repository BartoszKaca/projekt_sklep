<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function show($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $products = $category->products()
            ->where('is_active', 1)
            ->with('primaryImage')
            ->paginate(20);

        return view('category.show', compact('category', 'products'));
    }
}