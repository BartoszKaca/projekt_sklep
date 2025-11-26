<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a category
        $this->category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'is_active' => true,
        ]);
        
        // Create a product
        $this->product = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Test Product',
            'slug' => 'test-product',
            'price' => 49.99,
            'stock_quantity' => 10,
            'is_active' => true,
        ]);
    }

    public function test_cart_page_is_accessible()
    {
        $response = $this->get(route('cart.index'));
        $response->assertStatus(200);
    }

    public function test_can_add_product_to_cart()
    {
        $response = $this->postJson(route('cart.add'), [
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Dodano do koszyka',
            ]);
    }

    public function test_cart_count_is_updated_after_adding_product()
    {
        // Add product to cart
        $this->postJson(route('cart.add'), [
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        $response = $this->getJson(route('cart.count'));
        
        $response->assertStatus(200)
            ->assertJson([
                'count' => 2,
            ]);
    }

    public function test_can_update_cart_quantity()
    {
        // First add to cart
        $this->postJson(route('cart.add'), [
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);

        // Get the item key from session
        $cart = session('cart', ['items' => []]);
        $itemKey = array_key_first($cart['items']);

        // Update quantity
        $response = $this->postJson(route('cart.update'), [
            'item_key' => $itemKey,
            'quantity' => 5,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'cart_count' => 5,
            ]);
    }

    public function test_can_remove_item_from_cart()
    {
        // First add to cart
        $this->postJson(route('cart.add'), [
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);

        // Get the item key from session
        $cart = session('cart', ['items' => []]);
        $itemKey = array_key_first($cart['items']);

        // Remove item
        $response = $this->postJson(route('cart.remove'), [
            'item_key' => $itemKey,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'cart_count' => 0,
            ]);
    }

    public function test_adding_nonexistent_product_returns_error()
    {
        $response = $this->postJson(route('cart.add'), [
            'product_id' => 99999,
            'quantity' => 1,
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
            ]);
    }
}
