<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class InventoryService
{
    private string $cacheKey = 'inventory:summary';
    private string $lowStockKey = 'inventory:low-stock';

    public function getSummary()
    {
        return Cache::remember($this->cacheKey, 300, function () {

            return DB::table('products as p')
                ->leftJoin('stock as s', 'p.id', '=', 's.product_id')
                ->leftJoin('warehouses as w', 'w.id', '=', 's.warehouse_id')
                ->leftJoinSub(
                    DB::table('stock as s2')
                        ->select(
                            's2.product_id',
                            's2.warehouse_id',
                            DB::raw('SUM(s2.quantity) as warehouse_qty')
                        )
                        ->groupBy('s2.product_id', 's2.warehouse_id'),
                    'ws',
                    'ws.product_id',
                    '=',
                    'p.id'
                )
                ->leftJoinSub(
                    DB::table('stock as s3')
                        ->select(
                            's3.product_id',
                            DB::raw('MAX(s3.quantity) as max_qty')
                        )
                        ->groupBy('s3.product_id'),
                    'mx',
                    'mx.product_id',
                    '=',
                    'p.id'
                )
                ->leftJoin('stock as s4', function ($join) {
                    $join->on('s4.product_id', '=', 'p.id')
                         ->on('s4.quantity', '=', 'mx.max_qty');
                })
                ->leftJoin('warehouses as tw', 'tw.id', '=', 's4.warehouse_id')
                ->select(
                    'p.id as product_id',
                    'p.name as product_name',

                    DB::raw('COALESCE(SUM(s.quantity),0) as total_quantity'),
                    DB::raw('COALESCE(SUM(s.reserved_quantity),0) as total_reserved'),

                    DB::raw('COALESCE(SUM(s.quantity),0) - COALESCE(SUM(s.reserved_quantity),0) as available_quantity'),

                    DB::raw('CASE WHEN COALESCE(SUM(s.quantity),0) = 0 THEN 1 ELSE 0 END as is_out_of_stock'),

                    DB::raw('COALESCE(tw.id, 0) as top_warehouse_id'),
                    DB::raw('COALESCE(tw.name, "No Warehouse") as top_warehouse_name'),
                )
                ->groupBy('p.id', 'p.name', 'tw.id', 'tw.name')
                ->orderBy('p.id')
                ->get();
        });
    }

    /**
     * Call this after any stock change
     */
    public function clearSummaryCache()
    {
        Cache::forget($this->cacheKey);
    }

    public function getLowStockItems(int $threshold = 10)
    {
        return Cache::remember($this->lowStockKey, now()->addMinutes(10), function () use ($threshold) {

            return DB::table('stock')
                ->join('products', 'products.id', '=', 'stock.product_id')
                ->join('warehouses', 'warehouses.id', '=', 'stock.warehouse_id')
                ->select(
                    'products.id as product_id',
                    'products.name as product_name',
                    'products.sku',
                    'warehouses.id as warehouse_id',
                    'warehouses.name as warehouse_name',
                    'stock.quantity',
                    'stock.reserved_quantity',
                    DB::raw('(stock.quantity - stock.reserved_quantity) as available_quantity')
                )
                ->having('available_quantity', '<', $threshold)
                ->orderBy('available_quantity', 'asc')
                ->get();
        });
    }

    /**
     * Call this after any stock change
     */
    public function clearLowStockCache()
    {
        Cache::forget($this->lowStockKey);
    }
}