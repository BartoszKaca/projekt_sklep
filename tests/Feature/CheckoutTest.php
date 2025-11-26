<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a category and product
        $this->category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'is_active' => true,
        ]);
        
        $this->product = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Test Product',
            'slug' => 'test-product',
            'price' => 49.99,
            'stock_quantity' => 10,
            'is_active' => true,
        ]);
    }

    protected function addProductToCart()
    {
        $this->postJson(route('cart.add'), [
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);
    }

    public function test_checkout_page_redirects_with_empty_cart()
    {
        $response = $this->get(route('checkout.index'));
        $response->assertRedirect(route('cart.index'));
    }

    public function test_checkout_page_is_accessible_with_items_in_cart()
    {
        $this->addProductToCart();
        
        $response = $this->get(route('checkout.index'));
        $response->assertStatus(200);
    }

    public function test_guest_can_place_order()
    {
        $this->addProductToCart();
        
        $response = $this->post(route('checkout.process'), [
            'first_name' => 'Jan',
            'last_name' => 'Kowalski',
            'email' => 'jan@example.com',
            'phone' => '123456789',
            'street_address' => 'ul. Testowa 1',
            'city' => 'Warszawa',
            'postal_code' => '00-001',
            'country' => 'Polska',
            'shipping_method' => 'standard',
            'payment_method' => 'cash_on_delivery',
            'terms_accepted' => '1',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'payment_method' => 'cash_on_delivery',
        ]);
        $this->assertDatabaseHas('order_shipping', [
            'first_name' => 'Jan',
            'last_name' => 'Kowalski',
            'email' => 'jan@example.com',
        ]);
    }

    public function test_authenticated_user_can_place_order()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $this->actingAs($user);
        $this->addProductToCart();
        
        $response = $this->post(route('checkout.process'), [
            'first_name' => 'Jan',
            'last_name' => 'Kowalski',
            'email' => 'jan@example.com',
            'phone' => '123456789',
            'street_address' => 'ul. Testowa 1',
            'city' => 'Warszawa',
            'postal_code' => '00-001',
            'country' => 'Polska',
            'shipping_method' => 'standard',
            'payment_method' => 'bank_transfer',
            'terms_accepted' => '1',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'payment_method' => 'bank_transfer',
        ]);
    }

    public function test_checkout_validates_required_fields()
    {
        $this->addProductToCart();
        
        $response = $this->post(route('checkout.process'), [
            // Empty data
        ]);

        $response->assertSessionHasErrors([
            'first_name',
            'last_name',
            'email',
            'phone',
            'street_address',
            'city',
            'postal_code',
            'country',
            'shipping_method',
            'payment_method',
            'terms_accepted',
        ]);
    }

    public function test_checkout_validates_email_format()
    {
        $this->addProductToCart();
        
        $response = $this->post(route('checkout.process'), [
            'first_name' => 'Jan',
            'last_name' => 'Kowalski',
            'email' => 'invalid-email',
            'phone' => '123456789',
            'street_address' => 'ul. Testowa 1',
            'city' => 'Warszawa',
            'postal_code' => '00-001',
            'country' => 'Polska',
            'shipping_method' => 'standard',
            'payment_method' => 'cash_on_delivery',
            'terms_accepted' => '1',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_order_success_page_is_accessible()
    {
        $this->addProductToCart();
        
        // Place order
        $response = $this->post(route('checkout.process'), [
            'first_name' => 'Jan',
            'last_name' => 'Kowalski',
            'email' => 'jan@example.com',
            'phone' => '123456789',
            'street_address' => 'ul. Testowa 1',
            'city' => 'Warszawa',
            'postal_code' => '00-001',
            'country' => 'Polska',
            'shipping_method' => 'standard',
            'payment_method' => 'cash_on_delivery',
            'terms_accepted' => '1',
        ]);

        // Get order from database
        $order = \App\Models\Order::latest()->first();
        
        // Access success page
        $successResponse = $this->get(route('checkout.success', ['order' => $order->id]));
        $successResponse->assertStatus(200);
        $successResponse->assertSee($order->order_number);
    }
}
