<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Snacks', 'description' => 'Various snacks and chips'],
            ['name' => 'Beverages', 'description' => 'Drinks and beverages'],
            ['name' => 'Groceries', 'description' => 'Daily groceries'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
