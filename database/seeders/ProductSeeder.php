<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $categoryIds = Category::whereNotNull('parent_id')->pluck('id');

        if ($categoryIds->isEmpty()) {
            $this->command->warn('No child categories found, skipping products seeding.');
            return;
        }

        for ($i = 1; $i <= 50; $i++) {

            Product::create([
                'category_id' => $categoryIds->random(),
                'name' => 'Product ' . $faker->word,
                'sku' => 'SKU-' . $faker->unique()->randomNumber(),
                'description' => $faker->text(200),
                'base_price' => $faker->randomFloat(2, 10, 10000),
                'is_active' => Category::IS_ACTIVE_YES,
            ]);
        }
    }
}