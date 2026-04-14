<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Facades\Cache;

class CategoryService
{
    public function getTree()
    {
        return Cache::remember('inventory:categories:tree', now()->addMinutes(10), function () {
            $all = Category::select('id', 'name', 'parent_id', 'is_active')->get();
            $active = Category::active()
                ->select('id', 'name', 'parent_id', 'is_active')
                ->get();
            $byId = $all->keyBy('id');
            $grouped = [];
            foreach ($active as $cat) {
                $parentId = $cat->parent_id;
                // if parent is inactive, keep moving up
                while ($parentId && isset($byId[$parentId]) && !$byId[$parentId]->is_active) {
                    $parentId = $byId[$parentId]->parent_id;
                }
                $grouped[$parentId][] = $cat;
            }
            $build = function ($parentId) use (&$build, $grouped) {
                if (empty($grouped[$parentId])) {
                    return collect();
                }
                return collect($grouped[$parentId])->map(function ($cat) use ($build) {
                    return [
                        'id' => $cat->id,
                        'name' => $cat->name,
                        'children' => $build($cat->id),
                    ];
                });
            };
            return $build(null);
        });
    }
}