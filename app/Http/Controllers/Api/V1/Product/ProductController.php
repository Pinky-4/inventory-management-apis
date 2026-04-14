<?php

namespace App\Http\Controllers\Api\V1\Product;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Product\ProductCollection;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }
    /**
     * Get Product Listing with filters
     */
    public function index(Request $request)
    {
        $data = $this->productService->getProducts($request);
        
        return $this->successResponse(
            new ProductCollection($data),
            'Product list fetched successfully'
        );
    }
}