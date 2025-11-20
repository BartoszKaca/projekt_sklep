<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Category;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->words(3, true);
        $price = fake()->randomFloat(2, 20, 200);
        
        return [
            'category_id' => Category::factory(),
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'description' => fake()->paragraph(),
            'type' => fake()->randomElement(['album', 'merch']),
            'price' => $price,
            'discount_price' => fake()->optional(0.3)->randomFloat(2, $price * 0.5, $price * 0.9),
            'artist' => fake()->name(),
            'release_year' => fake()->year(),
            'format' => fake()->randomElement(['CD', 'Vinyl', 'Digital', 'Clothing', 'Accessories']),
            'label' => fake()->company(),
            'stock_quantity' => fake()->numberBetween(0, 100),
            'low_stock_threshold' => 5,
            'sku' => 'SKU-' . fake()->unique()->numerify('######'),
            'barcode' => fake()->ean13(),
            'is_featured' => fake()->boolean(20),
            'is_active' => true,
            'views_count' => fake()->numberBetween(0, 1000),
            'weight' => fake()->randomFloat(2, 0.1, 2),
        ];
    }
}
