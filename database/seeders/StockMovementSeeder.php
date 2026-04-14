<?php

namespace Database\Seeders;

use App\Models\StockMovement;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class StockMovementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $types = [StockMovement::STOCK_IN, StockMovement::STOCK_OUT, StockMovement::RESERVATION, StockMovement::RESERVATION_RELEASE]; // movement types

        for ($i = 1; $i <= 200; $i++) {

            StockMovement::create([
                'product_id' => Product::inRandomOrder()->value('id'),
                'warehouse_id' => Warehouse::inRandomOrder()->value('id'),
                'movement_type' => $types[array_rand($types)],
                'quantity' => rand(1, 20),
                'reference_id' => rand(1, 50),
                'reference_type' => 'order',
                'note' => 'Auto generated movement',
                'moved_at' => now()->subDays(rand(0, 30)),
            ]);
        }
    }
}
