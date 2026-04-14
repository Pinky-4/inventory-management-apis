<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;
    protected $table = 'warehouses';
    protected $primaryKey = 'id';
    protected $guarded = [];
    const IS_ACTIVE_YES = 1;
    const IS_ACTIVE_NO = 0;

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
