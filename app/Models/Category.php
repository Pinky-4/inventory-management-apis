<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class Category extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'categories';
    protected $primaryKey = 'id';
    protected $guarded = [];
    const IS_ACTIVE_YES = 1;
    const IS_ACTIVE_NO = 0;

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Children categories
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id', 'id');
    }

    // Scope: filter active categories
    public function scopeActive($query)
    {
        return $query->where('is_active', self::IS_ACTIVE_YES);
    }

    protected static function booted()
    {
        static::saved(function () {
            Cache::forget('inventory:categories:tree');
        });

        static::deleted(function () {
            Cache::forget('inventory:categories:tree');
        });
    }
}
