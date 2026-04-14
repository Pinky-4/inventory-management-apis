<?php

namespace App\Services;

use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockService
{
    public function adjust($data)
    {
        return DB::transaction(function () use ($data) {

            // lock row for concurrency
            $stock = Stock::where('product_id', $data['product_id'])
                ->where('warehouse_id', $data['warehouse_id'])
                ->lockForUpdate()
                ->first();

            if (!$stock) {
                $stock = Stock::create([
                    'product_id' => $data['product_id'],
                    'warehouse_id' => $data['warehouse_id'],
                    'quantity' => 0,
                    'reserved_quantity' => 0,
                ]);
            }

            $available = $stock->quantity - $stock->reserved_quantity;
            $qty = (int) $data['quantity'];
            switch ($data['movement_type']) {

                case StockMovement::STOCK_IN :
                    $stock->quantity += $qty;
                    break;

                case StockMovement::STOCK_OUT :
                    if ($available < $qty) {
                        throw ValidationException::withMessages([
                            'quantity' => 'Not enough available stock'
                        ]);
                    }
                    $stock->quantity -= $qty;
                    break;

                case StockMovement::RESERVATION :
                    if ($available < $qty) {
                        throw ValidationException::withMessages([
                            'quantity' => 'Cannot reserve more than available'
                        ]);
                    }
                    $stock->reserved_quantity += $qty;
                    break;

                case StockMovement::RESERVATION_RELEASE :
                    if ($stock->reserved_quantity < $qty) {
                        throw ValidationException::withMessages([
                            'quantity' => 'Invalid reservation release'
                        ]);
                    }
                    $stock->reserved_quantity = max(0, $stock->reserved_quantity - $qty);
                    break;

                default:
                    throw ValidationException::withMessages([
                        'movement_type' => 'Invalid movement type'
                    ]);
            }

            $stock->save();

            StockMovement::create([
                'product_id' => $data['product_id'],
                'warehouse_id' => $data['warehouse_id'],
                'movement_type' => $data['movement_type'],
                'quantity' => $qty,
                'reference_id' => $data['reference_id'] ?? null,
                'reference_type' => $data['reference_type'] ?? null,
                'note' => $data['note'] ?? null,
                'moved_at' => now(),
            ]);

            // Clear cache (simple + realistic)
            cache()->forget('inventory:summary');
            cache()->forget('inventory:low_stock');

            return $stock->quantity - $stock->reserved_quantity;
        });
    }
}