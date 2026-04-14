<?php

namespace App\Http\Controllers\Api\V1\StockMovement;

use App\Http\Controllers\Controller;
use App\Services\StockMovementService;
use Illuminate\Http\Request;
use App\Http\Resources\Api\V1\StockMovement\StockMovementCollection;
use App\Http\Resources\Api\V1\StockMovement\StockMovementSummaryResource;

class StockMovementController extends Controller
{
    protected $stockMovementService;

    public function __construct(StockMovementService $stockMovementService)
    {
        $this->stockMovementService = $stockMovementService;
    }

    /**
     * Movement History with Aggregation  
     */
    public function index(Request $request, $id)
    {
        $data = $this->stockMovementService->getMovements($id, $request);

        return $this->successResponse([
            'summary' => new StockMovementSummaryResource($data['summary']),
            'movements' => new StockMovementCollection($data['movements']),
        ], 'Product movement history fetched successfully');
    }
}