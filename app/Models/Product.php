<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $guarded = [];
    const IS_ACTIVE_YES = 1;
    const IS_ACTIVE_NO = 0;
    public const PAGINATION_LENGTH = 10;

    protected $casts = [
        'base_price' => 'float',
        'is_active' => 'boolean',
    ];

    // Product belongs to a category
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    // Product has multiple stock records
    public function stocks()
    {
        return $this->hasMany(Stock::class, 'product_id', 'id');
    }

    // Scope: search by name or SKU
    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%$term%")
              ->orWhere('sku', 'like', "%$term%");
        });
    }

    // Scope: filter by price range
    public function scopePriceRange($query, $min, $max)
    {
        return $query
            ->when($min, fn($q) => $q->where('base_price', '>=', $min))
            ->when($max, fn($q) => $q->where('base_price', '<=', $max));
    }

    // Scope: filter products with available stock
    public function scopeAvailable($query)
    {
        return $query->whereHas('stocks', function ($q) {
            $q->whereRaw('(quantity - reserved_quantity) > 0');
        });
    }

    public function scopeOutOfStock($query)
    {
        return $query->whereDoesntHave('stocks', function ($q) {
            $q->whereRaw('(quantity - reserved_quantity) > 0');
        });
    }
}
