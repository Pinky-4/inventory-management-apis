<?php

namespace App\Http\Controllers\Api\V1\Inventory;

use App\Http\Controllers\Controller;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use App\Http\Resources\Api\V1\Inventory\InventorySummaryResource;
use App\Http\Resources\Api\V1\Stock\StockCollection;

class InventoryController extends Controller
{
    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Inventory Summary Report 
     */
    public function summary()
    {
        $data = $this->inventoryService->getSummary();

        return $this->successResponse(
            InventorySummaryResource::collection($data),
            'Inventory summary fetched successfully'
        );
    }

    /**
     * Low Stock Alert 
     */
    public function lowStock(Request $request)
    {
        $threshold = (int) $request->query('threshold', 10);

        $data = $this->inventoryService->getLowStockItems($threshold);

        return $this->successResponse(
            new StockCollection($data),
            'Low stock items fetched successfully'
        );
    }
}