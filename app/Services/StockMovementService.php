<?php

namespace App\Services;

use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class StockMovementService
{
    public function getMovements($productId, $request)
    {
        $query = StockMovement::where('product_id', $productId);

        $query->dateRange(
            $request->date_from ?? null,
            $request->date_to ?? null
        );

        $movements = (clone $query)
            ->orderBy('moved_at', 'desc')
            ->paginate(StockMovement::PAGINATION_LENGTH);

        $summary = (clone $query)
            ->select(
                DB::raw("SUM(CASE WHEN movement_type = " . StockMovement::STOCK_IN . " THEN quantity ELSE 0 END) as total_in"),
                DB::raw("SUM(CASE WHEN movement_type = " . StockMovement::STOCK_OUT . " THEN quantity ELSE 0 END) as total_out"),
                DB::raw("
                    SUM(
                        CASE 
                            WHEN movement_type = " . StockMovement::STOCK_IN . " THEN quantity
                            WHEN movement_type = " . StockMovement::STOCK_OUT . " THEN -quantity
                            ELSE 0
                        END
                    ) as net_movement
                ")
            )
            ->first();

        return [
            'summary' => $summary,
            'movements' => $movements,
        ];
    }
}