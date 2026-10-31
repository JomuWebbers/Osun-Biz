<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['Restaurants', 'restaurants', 'Places to eat and drink'],
            ['Fashion & Clothing', 'fashion-clothing', 'Clothing, shoes, and accessories'],
            ['Technology', 'technology', 'Electronics, computers, and IT services'],
            ['Arts & Crafts', 'arts-crafts', 'Handmade crafts and artwork'],
            ['Books & Stationery', 'books-stationery', 'Books, writing materials, and office supplies'],
            ['Food & Beverages', 'food-beverages', 'Grocery stores, bakeries, and beverage shops'],
            ['Health & Wellness', 'health-wellness', 'Pharmacies, clinics, and wellness centers'],
            ['Professional Services', 'professional-services', 'Legal, accounting, consulting, and other services'],
        ];

        DB::table('categories')->insertOrIgnore(array_map(
            fn (array $category): array => [
                'name' => $category[0],
                'slug' => $category[1],
                'description' => $category[2],
                'created_at' => now(),
            ],
            $categories,
        ));
    }
}
