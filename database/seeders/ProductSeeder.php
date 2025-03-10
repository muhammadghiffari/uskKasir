<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'category_id' => 1,
                'name'        => 'Potato Chips',
                'price'       => 10000,
                'stock'       => 100,
            ],
            [
                'category_id' => 2,
                'name'        => 'Mineral Water',
                'price'       => 5000,
                'stock'       => 200,
            ],
            // Add more products as needed
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
