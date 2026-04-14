<?php

namespace Database\Seeders;

use App\Models\Stock;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class StockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $products = Product::all();
        $warehouses = Warehouse::all();

        foreach ($products as $product) {

            foreach ($warehouses as $warehouse) {

                // Randomly skip some → creates edge case
                if (rand(0, 1)) continue;

                $quantity = rand(0, 100);
                $reserved = rand(0, $quantity);

                Stock::create([
                    'product_id' => $product->id,
                    'warehouse_id' => $warehouse->id,
                    'quantity' => $quantity,
                    'reserved_quantity' => $reserved,
                ]);
            }
        }
    }
}
