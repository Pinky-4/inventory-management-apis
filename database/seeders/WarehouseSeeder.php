<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Warehouse::insert([
            ['name' => 'Warehouse A', 'city' => 'Ahmedabad', 'is_active' => Warehouse::IS_ACTIVE_YES],
            ['name' => 'Warehouse B', 'city' => 'Surat', 'is_active' => Warehouse::IS_ACTIVE_NO],
            ['name' => 'Warehouse C', 'city' => 'Mumbai', 'is_active' => Warehouse::IS_ACTIVE_YES],
        ]);
    }
}
