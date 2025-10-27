<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'Płyty CD',
                'slug' => 'plyty-cd',
                'description' => 'Albumy rapowe w formacie CD',
                'is_active' => true,
            ],
            [
                'name' => 'Winyle',
                'slug' => 'winyle',
                'description' => 'Klasyczne albumy hip-hopowe na winylu',
                'is_active' => true,
            ],
            [
                'name' => 'Kasety',
                'slug' => 'kasety',
                'description' => 'Limitowane wydania na kasetach',
                'is_active' => true,
            ],
            [
                'name' => 'Koszulki',
                'slug' => 'koszulki',
                'description' => 'Odzież rapowa - koszulki z nadrukami',
                'is_active' => true,
            ],
            [
                'name' => 'Bluzy',
                'slug' => 'bluzy',
                'description' => 'Bluzy z kapturem i bez',
                'is_active' => true,
            ],
            [
                'name' => 'Czapki',
                'slug' => 'czapki',
                'description' => 'Czapki snapback i beanie',
                'is_active' => true,
            ],
            [
                'name' => 'Akcesoria',
                'slug' => 'akcesoria',
                'description' => 'Plecaki, torby, biżuteria',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
