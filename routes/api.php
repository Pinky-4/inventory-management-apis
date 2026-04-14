<?php

use App\Http\Controllers\Api\V1\Category\CategoryController;
use App\Http\Controllers\Api\V1\Inventory\InventoryController;
use App\Http\Controllers\Api\V1\Product\ProductController;
use App\Http\Controllers\Api\V1\StockMovement\StockMovementController;
use App\Http\Controllers\Api\V1\Stock\StockController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// 4.1 Category Tree Endpoint 
Route::get('/categories/tree', [CategoryController::class,'tree']);

// 4.2 Product Listing with Filters 
Route::get('/products', [ProductController::class, 'index']);

// 4.3 Stock Adjustment Endpoint 
Route::post('/stock/adjust', [StockController::class, 'adjust']);

// 4.4 Inventory Summary Report 
Route::get('/inventory/summary', [InventoryController::class, 'summary']);

// 4.5 Movement History with Aggregation 
Route::get('/products/{id}/movements', [StockMovementController::class, 'index']);

// 4.6 Low Stock Alert Endpoint 
Route::get('/inventory/low-stock', [InventoryController::class, 'lowStock']);

