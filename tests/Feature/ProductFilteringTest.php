<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductFilteringTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create categories
        $this->category1 = Category::create([
            'name' => 'Płyty CD',
            'slug' => 'plyty-cd',
            'is_active' => true,
        ]);
        
        $this->category2 = Category::create([
            'name' => 'Merch',
            'slug' => 'merch',
            'is_active' => true,
        ]);
        
        // Create products
        Product::create([
            'category_id' => $this->category1->id,
            'name' => 'Album Testowy',
            'slug' => 'album-testowy',
            'type' => 'album',
            'price' => 49.99,
            'stock_quantity' => 10,
            'is_active' => true,
            'artist' => 'Test Artist',
        ]);
        
        Product::create([
            'category_id' => $this->category1->id,
            'name' => 'Drogi Album',
            'slug' => 'drogi-album',
            'type' => 'album',
            'price' => 199.99,
            'stock_quantity' => 5,
            'is_active' => true,
        ]);
        
        Product::create([
            'category_id' => $this->category2->id,
            'name' => 'Koszulka',
            'slug' => 'koszulka',
            'type' => 'merch',
            'price' => 79.99,
            'stock_quantity' => 20,
            'is_active' => true,
        ]);
    }

    public function test_products_page_is_accessible()
    {
        $response = $this->get(route('products.index'));
        $response->assertStatus(200);
    }

    public function test_can_filter_by_category()
    {
        $response = $this->get(route('products.index', ['category' => 'plyty-cd']));
        
        $response->assertStatus(200);
        $response->assertSee('Album Testowy');
        $response->assertSee('Drogi Album');
        $response->assertDontSee('Koszulka');
    }

    public function test_can_filter_by_type()
    {
        $response = $this->get(route('products.index', ['type' => 'merch']));
        
        $response->assertStatus(200);
        $response->assertSee('Koszulka');
        $response->assertDontSee('Album Testowy');
    }

    public function test_can_filter_by_price_range()
    {
        $response = $this->get(route('products.index', [
            'min_price' => 50,
            'max_price' => 100,
        ]));
        
        $response->assertStatus(200);
        $response->assertSee('Koszulka');
        $response->assertDontSee('Album Testowy'); // 49.99 is less than 50
        $response->assertDontSee('Drogi Album'); // 199.99 is more than 100
    }

    public function test_can_sort_by_price_ascending()
    {
        $response = $this->get(route('products.index', ['sort' => 'price_asc']));
        
        $response->assertStatus(200);
        // Check that products are returned
        $response->assertSee('Album Testowy');
    }

    public function test_can_sort_by_price_descending()
    {
        $response = $this->get(route('products.index', ['sort' => 'price_desc']));
        
        $response->assertStatus(200);
        $response->assertSee('Drogi Album');
    }

    public function test_can_search_products()
    {
        $response = $this->get(route('products.index', ['search' => 'Artist']));
        
        $response->assertStatus(200);
        $response->assertSee('Album Testowy');
        $response->assertDontSee('Drogi Album');
    }

    public function test_search_api_returns_json()
    {
        $response = $this->getJson(route('products.search', ['q' => 'Album']));
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'products' => [
                    '*' => ['id', 'name', 'price', 'url'],
                ],
            ]);
    }

    public function test_search_api_returns_empty_for_short_query()
    {
        $response = $this->getJson(route('products.search', ['q' => 'A']));
        
        $response->assertStatus(200)
            ->assertJson(['products' => []]);
    }
}
