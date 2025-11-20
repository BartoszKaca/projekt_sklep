<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_category_page(): void
    {
        $category = Category::factory()->create([
            'name' => 'Test Category',
            'slug' => 'test-category',
        ]);

        $response = $this->get('/kategoria/test-category');

        $response->assertStatus(200);
        $response->assertViewIs('category.show');
        $response->assertViewHas('category');
    }

    public function test_category_page_displays_active_products(): void
    {
        $category = Category::factory()->create([
            'name' => 'Test Category',
            'slug' => 'test-category',
        ]);

        $activeProduct = Product::factory()->create([
            'is_active' => true,
            'category_id' => $category->id,
        ]);

        $inactiveProduct = Product::factory()->create([
            'is_active' => false,
            'category_id' => $category->id,
        ]);

        $response = $this->get('/kategoria/test-category');

        $response->assertStatus(200);
        $response->assertViewHas('products', function ($products) use ($activeProduct, $inactiveProduct) {
            return $products->contains('id', $activeProduct->id) 
                && !$products->contains('id', $inactiveProduct->id);
        });
    }

    public function test_category_page_returns_404_for_invalid_slug(): void
    {
        $response = $this->get('/kategoria/non-existent-category');

        $response->assertStatus(404);
    }

    public function test_category_page_paginates_products(): void
    {
        $category = Category::factory()->create([
            'name' => 'Test Category',
            'slug' => 'test-category',
        ]);

        // Create 25 products (more than the pagination limit of 20)
        Product::factory()->count(25)->create([
            'is_active' => true,
            'category_id' => $category->id,
        ]);

        $response = $this->get('/kategoria/test-category');

        $response->assertStatus(200);
        $response->assertViewHas('products', function ($products) {
            return $products->count() === 20; // Default pagination limit
        });
    }
}
