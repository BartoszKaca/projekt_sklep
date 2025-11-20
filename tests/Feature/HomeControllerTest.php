<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;

class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_access_homepage(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('home');
    }

    public function test_homepage_loads_featured_products(): void
    {
        $category = Category::factory()->create(['name' => 'Test Category', 'slug' => 'test-category']);
        
        // Create featured products
        $featuredProduct = Product::factory()->create([
            'is_active' => true,
            'is_featured' => true,
            'category_id' => $category->id,
        ]);

        // Create non-featured product
        Product::factory()->create([
            'is_active' => true,
            'is_featured' => false,
            'category_id' => $category->id,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewHas('featuredProducts', function ($products) use ($featuredProduct) {
            return $products->contains('id', $featuredProduct->id);
        });
    }

    public function test_homepage_loads_latest_products(): void
    {
        $category = Category::factory()->create(['name' => 'Test Category', 'slug' => 'test-category']);
        
        $latestProduct = Product::factory()->create([
            'is_active' => true,
            'category_id' => $category->id,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewHas('latestProducts', function ($products) use ($latestProduct) {
            return $products->contains('id', $latestProduct->id);
        });
    }

    public function test_authenticated_user_can_access_homepage(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('home');
    }
}
