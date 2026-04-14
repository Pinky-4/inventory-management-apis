<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Parent Categories
        $electronics = Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics',
            'is_active' => Category::IS_ACTIVE_YES
        ]);

        $fashion = Category::create([
            'name' => 'Fashion',
            'slug' => 'fashion',
            'is_active' => Category::IS_ACTIVE_YES
        ]);

        $emptyCategory = Category::create([
            'name' => 'Empty Category',
            'slug' => 'empty-category',
            'is_active' => Category::IS_ACTIVE_YES
        ]);

        // Children
        Category::create([
            'name' => 'Mobiles',
            'slug' => 'mobiles',
            'parent_id' => $electronics->id,
            'is_active' => Category::IS_ACTIVE_YES
        ]);

        Category::create([
            'name' => 'Laptops',
            'slug' => 'laptops',
            'parent_id' => $electronics->id,
            'is_active' => Category::IS_ACTIVE_YES
        ]);

        Category::create([
            'name' => 'Men Clothing',
            'slug' => 'men-clothing',
            'parent_id' => $fashion->id,
            'is_active' => Category::IS_ACTIVE_NO
        ]);
    }
}
