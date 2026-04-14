<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categoryIds = Category::whereNotNull('parent_id')->pluck('id');

        if ($categoryIds->isEmpty()) {
            $this->command->warn('No child categories found, skipping products seeding.');
            return;
        }

        for ($i = 1; $i <= 50; $i++) {

            Product::create([
                'category_id' => $categoryIds->random(),
                'name' => 'Product ' . $i,
                'sku' => 'SKU-' . strtoupper(Str::random(8)),
                'description' => 'Sample product ' . $i . ' description',
                'base_price' => rand(100, 10000),
                'is_active' => Category::IS_ACTIVE_YES,
            ]);
        }
    }
}