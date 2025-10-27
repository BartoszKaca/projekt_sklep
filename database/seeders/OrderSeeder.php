<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderShipping;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run()
    {
        $customer = User::where('role', 'customer')->first();
        $products = Product::take(3)->get();

        // Zamówienie 1 - Opłacone i wysłane
        $order1 = Order::create([
            'order_number' => Order::generateOrderNumber(),
            'user_id' => $customer->id,
            'status' => 'shipped',
            'subtotal' => 124.97,
            'shipping_cost' => 15.00,
            'tax' => 0,
            'discount' => 0,
            'total' => 139.97,
            'payment_method' => 'blik',
            'payment_status' => 'paid',
            'tracking_number' => 'PL123456789',
            'carrier' => 'InPost',
            'paid_at' => now()->subDays(2),
            'shipped_at' => now()->subDay(),
        ]);

        OrderShipping::create([
            'order_id' => $order1->id,
            'first_name' => 'Jan',
            'last_name' => 'Kowalski',
            'street_address' => 'ul. Przykładowa 123',
            'apartment' => '5',
            'city' => 'Warszawa',
            'postal_code' => '00-001',
            'country' => 'PL',
            'phone' => '+48987654321',
            'email' => 'jan@example.com',
        ]);

        foreach ($products as $product) {
            OrderItem::create([
                'order_id' => $order1->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'sku' => $product->sku,
                'price' => $product->getFinalPrice(),
                'quantity' => 1,
                'total' => $product->getFinalPrice(),
            ]);
        }

        // Zamówienie 2 - Oczekujące na płatność
        $order2 = Order::create([
            'order_number' => Order::generateOrderNumber(),
            'user_id' => $customer->id,
            'status' => 'pending',
            'subtotal' => 159.99,
            'shipping_cost' => 15.00,
            'tax' => 0,
            'discount' => 0,
            'total' => 174.99,
            'payment_method' => 'transfer',
            'payment_status' => 'pending',
            'customer_notes' => 'Proszę o szybką wysyłkę',
        ]);

        OrderShipping::create([
            'order_id' => $order2->id,
            'first_name' => 'Anna',
            'last_name' => 'Nowak',
            'street_address' => 'ul. Testowa 456',
            'city' => 'Kraków',
            'postal_code' => '30-001',
            'country' => 'PL',
            'phone' => '+48555666777',
            'email' => 'anna@example.com',
        ]);

        $hoodie = Product::where('slug', 'bluza-oversize-underground')->first();
        OrderItem::create([
            'order_id' => $order2->id,
            'product_id' => $hoodie->id,
            'product_name' => $hoodie->name,
            'sku' => $hoodie->sku,
            'price' => $hoodie->getFinalPrice(),
            'quantity' => 1,
            'total' => $hoodie->getFinalPrice(),
        ]);
    }
}