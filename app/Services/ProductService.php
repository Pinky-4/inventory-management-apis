<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Cache;

class ProductService
{
    public function getProducts($request)
    {
        $key = 'inventory:products:' . md5(json_encode($request->all()));
        return Cache::remember($key, 600, function () use ($request) {
            $query = Product::query()->with(['category', 'stocks']);
            $query->when($request->filled('search'), function ($q) use ($request) {
                $q->search($request->search);
            });
            if ($request->filled('category_id')) {
                $allCategories = Category::select('id', 'parent_id')->get();
                $categoryIds = $this->getCategoryWithChildren($request->category_id, $allCategories);
                $query->whereIn('category_id', $categoryIds);
            }
            if ($request->filled('warehouse_id')) {
                $query->whereHas('stocks', function ($q) use ($request) {
                    if ($request->filled('warehouse_id')) {
                        $q->where('warehouse_id', $request->warehouse_id);
                    }
                    if ($request->filled('available')) {
                        if ($request->available == 1) {
                            $q->whereRaw('(quantity - reserved_quantity) > 0');
                        } elseif ($request->available == 0) {
                            $q->whereRaw('(quantity - reserved_quantity) <= 0');
                        }
                    }
                });
            }elseif ($request->filled('available')) {
               $request->available == 1 ? $query->available() : $query->outOfStock();
            }
            $query->priceRange(
                $request->min_price,
                $request->max_price
            );
            $direction = $request->get('sort_order', 'asc');
            if ($request->filled('sort_by')) {
                switch ($request->sort_by) {
                    case 'name':
                        $query->orderBy('name', $direction);
                        break;
                    case 'price':
                        $query->orderBy('base_price', $direction);
                        break;
                    case 'stock':
                        $query->withSum('stocks as available_stock', \DB::raw('quantity - reserved_quantity'))
                            ->orderBy('available_stock', $direction === 'asc' ? 'asc' : 'desc');
                        break;
                }
            } else {
                $query->orderBy('id', 'desc');
            }
            return $query->paginate(Product::PAGINATION_LENGTH);
        });
    }

    private function getCategoryWithChildren($categoryId, $categories)
    {
        $grouped = $categories->groupBy('parent_id');
        $ids = [$categoryId];
        $collect = function ($parentId) use (&$collect, $grouped, &$ids) {
            if (empty($grouped[$parentId])) {
                return;
            }
            foreach ($grouped[$parentId] as $child) {
                $ids[] = $child->id;
                $collect($child->id);
            }
        };
        $collect($categoryId);
        return $ids;
    }
}