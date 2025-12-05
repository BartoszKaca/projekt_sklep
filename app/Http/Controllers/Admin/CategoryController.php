<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        
        // Obsługa pola boolean - gdy checkbox nie jest zaznaczony, nie jest wysyłany
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;
        
        Category::create($validated);

        return back()->with('success', 'Kategoria została dodana!');
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        
        // Obsługa pola boolean - gdy checkbox nie jest zaznaczony, nie jest wysyłany
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;
        
        $category->update($validated);

        return back()->with('success', 'Kategoria została zaktualizowana!');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0) {
            return back()->withErrors(['error' => 'Nie można usunąć kategorii z przypisanymi produktami!']);
        }

        $category->delete();
        return back()->with('success', 'Kategoria została usunięta!');
    }
}
