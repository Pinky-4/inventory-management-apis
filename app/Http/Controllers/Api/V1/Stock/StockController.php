<?php

namespace App\Http\Controllers\Api\V1\Stock;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Stock\StockAdjustRequest;
use App\Services\StockService;

class StockController extends Controller
{
    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * Stock Adjustment
     */
    public function adjust(StockAdjustRequest $request)
    {
        try {
            $available = $this->stockService->adjust($request->all());

            return $this->successResponse([
                'available_quantity' => $available
            ], 'Stock updated successfully');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse(
                $e->errors()
            );
        }catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}