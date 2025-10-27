<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        // Płyty CD
        $cdCategory = Category::where('slug', 'plyty-cd')->first();
        
        $products = [
            [
                'category_id' => $cdCategory->id,
                'name' => 'Taco Hemingway - Café Belga',
                'slug' => 'taco-hemingway-cafe-belga',
                'description' => 'Kultowy album Taco Hemingwaya z 2015 roku. Zawiera hity takie jak "Białkoholicy" i "Wosk".',
                'type' => 'album',
                'price' => 39.99,
                'discount_price' => null,
                'artist' => 'Taco Hemingway',
                'release_year' => 2015,
                'format' => 'CD',
                'label' => 'Antena Krzyku',
                'stock_quantity' => 50,
                'low_stock_threshold' => 10,
                'sku' => 'CD-TACO-001',
                'barcode' => '5901234567890',
                'is_featured' => true,
                'is_active' => true,
                'weight' => 0.1,
            ],
            [
                'category_id' => $cdCategory->id,
                'name' => 'Quebonafide - Romantic Psycho',
                'slug' => 'quebonafide-romantic-psycho',
                'description' => 'Przełomowy album Quebonafide z 2017 roku. Platynowa płyta z hitami jak "Candy" i "Tamagotchi".',
                'type' => 'album',
                'price' => 44.99,
                'discount_price' => 34.99,
                'artist' => 'Quebonafide',
                'release_year' => 2017,
                'format' => 'CD',
                'label' => 'QueQuality',
                'stock_quantity' => 3,
                'low_stock_threshold' => 5,
                'sku' => 'CD-QUEBO-001',
                'barcode' => '5901234567891',
                'is_featured' => true,
                'is_active' => true,
                'weight' => 0.1,
            ],
            [
                'category_id' => $cdCategory->id,
                'name' => 'Sokół - 100 Barów 2.0',
                'slug' => 'sokol-100-barow-20',
                'description' => 'Legendarny album jednego z najważniejszych raperów w Polsce.',
                'type' => 'album',
                'price' => 42.99,
                'discount_price' => null,
                'artist' => 'Sokół',
                'release_year' => 2014,
                'format' => 'CD',
                'label' => 'Asfalt Records',
                'stock_quantity' => 25,
                'low_stock_threshold' => 10,
                'sku' => 'CD-SOKOL-001',
                'barcode' => '5901234567892',
                'is_featured' => false,
                'is_active' => true,
                'weight' => 0.1,
            ],
        ];

        foreach ($products as $productData) {
            $product = Product::create($productData);
            
            // Dodaj placeholder image
            ProductImage::create([
                'product_id' => $product->id,
                'path' => 'products/' . $product->slug . '.jpg',
                'is_primary' => true,
                'sort_order' => 0,
            ]);
        }

        // Winyle
        $vinylCategory = Category::where('slug', 'winyle')->first();
        
        Product::create([
            'category_id' => $vinylCategory->id,
            'name' => 'O.S.T.R. - Tylko Dla Dorosłych (Vinyl)',
            'slug' => 'ostr-tylko-dla-doroslych-vinyl',
            'description' => 'Klasyczny album OSTR-a w limitowanej edycji winylowej.',
            'type' => 'album',
            'price' => 89.99,
            'discount_price' => null,
            'artist' => 'O.S.T.R.',
            'release_year' => 2010,
            'format' => 'Vinyl',
            'label' => 'Asfalt Records',
            'stock_quantity' => 15,
            'low_stock_threshold' => 5,
            'sku' => 'VIN-OSTR-001',
            'is_featured' => true,
            'is_active' => true,
            'weight' => 0.3,
        ]);

        // Merch - Koszulki
        $shirtCategory = Category::where('slug', 'koszulki')->first();
        
        $tshirt = Product::create([
            'category_id' => $shirtCategory->id,
            'name' => 'Koszulka "Polish Hip-Hop"',
            'slug' => 'koszulka-polish-hip-hop',
            'description' => 'Premium koszulka z nadrukiem Polish Hip-Hop. 100% bawełna, wysokiej jakości nadruk.',
            'type' => 'merch',
            'price' => 79.99,
            'discount_price' => null,
            'format' => 'Clothing',
            'stock_quantity' => 0, // Warianty mają własny stock
            'low_stock_threshold' => 5,
            'sku' => 'TSH-PHH-001',
            'is_featured' => true,
            'is_active' => true,
            'weight' => 0.2,
        ]);

        // Dodaj warianty rozmiarów
        $sizes = ['S', 'M', 'L', 'XL', 'XXL'];
        $stocks = [8, 15, 20, 12, 5];
        
        foreach ($sizes as $index => $size) {
            ProductVariant::create([
                'product_id' => $tshirt->id,
                'name' => "Rozmiar $size",
                'size' => $size,
                'color' => 'Czarny',
                'price_modifier' => 0,
                'stock_quantity' => $stocks[$index],
                'sku' => "TSH-PHH-001-$size",
                'is_active' => true,
            ]);
        }

        // Bluzy
        $hoodieCategory = Category::where('slug', 'bluzy')->first();
        
        $hoodie = Product::create([
            'category_id' => $hoodieCategory->id,
            'name' => 'Bluza Oversize "Underground"',
            'slug' => 'bluza-oversize-underground',
            'description' => 'Ciepła bluza z kapturem, oversize fit. Idealny streetwear.',
            'type' => 'merch',
            'price' => 159.99,
            'discount_price' => 139.99,
            'format' => 'Clothing',
            'stock_quantity' => 0,
            'low_stock_threshold' => 3,
            'sku' => 'HDD-UND-001',
            'is_featured' => true,
            'is_active' => true,
            'weight' => 0.6,
        ]);

        foreach (['S', 'M', 'L', 'XL'] as $size) {
            ProductVariant::create([
                'product_id' => $hoodie->id,
                'name' => "Rozmiar $size",
                'size' => $size,
                'color' => 'Czarny',
                'price_modifier' => 0,
                'stock_quantity' => rand(5, 15),
                'sku' => "HDD-UND-001-$size",
                'is_active' => true,
            ]);
        }

        // Czapki
        $capCategory = Category::where('slug', 'czapki')->first();
        
        Product::create([
            'category_id' => $capCategory->id,
            'name' => 'Czapka Snapback "Rap PL"',
            'slug' => 'czapka-snapback-rap-pl',
            'description' => 'Klasyczna czapka snapback z haftowanym logo.',
            'type' => 'merch',
            'price' => 59.99,
            'discount_price' => null,
            'format' => 'Accessories',
            'stock_quantity' => 45,
            'low_stock_threshold' => 10,
            'sku' => 'CAP-RPL-001',
            'is_featured' => false,
            'is_active' => true,
            'weight' => 0.15,
        ]);
    }
}
