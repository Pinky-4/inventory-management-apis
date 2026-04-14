<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;
    protected $table = 'stock_movements';
    protected $primaryKey = 'id';
    protected $guarded = [];
   
    protected $casts = [
        'quantity' => 'integer',
        'moved_at' => 'datetime',
    ];

    // Movement type constants
    const STOCK_IN = 1;
    const STOCK_OUT = 2;
    const RESERVATION = 3;
    const RESERVATION_RELEASE = 4;
    public const PAGINATION_LENGTH = 20;

    // Scope: filter by date range
    public function scopeDateRange($query, $from, $to)
    {
        return $query
            ->when($from, fn($q) => $q->where('moved_at', '>=', $from))
            ->when($to, fn($q) => $q->where('moved_at', '<=', $to));
    }
    public function getMovementTypeNameAttribute()
    {
        return match ($this->movement_type) {
            self::STOCK_IN => 'Stock In',
            self::STOCK_OUT => 'Stock Out',
            self::RESERVATION => 'Reservation',
            self::RESERVATION_RELEASE => 'Reservation Release',
            default => 'Unknown',
        };
    }
}
