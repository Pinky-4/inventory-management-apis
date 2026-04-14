<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Stock extends Model
{
    use HasFactory;
    protected $table = 'stock';
    protected $primaryKey = 'id';
    protected $guarded = [];

    protected $casts = [
        'quantity' => 'integer',
        'reserved_quantity' => 'integer',
    ];

    protected static function booted()
    {
        static::saved(function () {
            Cache::forget('inventory:summary');
        });

        static::deleted(function () {
            Cache::forget('inventory:summary');
        });
    }
}
