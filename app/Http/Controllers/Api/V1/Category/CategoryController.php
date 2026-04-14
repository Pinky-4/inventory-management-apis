<?php

namespace App\Http\Controllers\Api\V1\Category;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Category\CategoryCollection;
use App\Services\CategoryService;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }
    
    /**
     * Get category tree
     */
    public function tree()
    {
        $data = $this->categoryService->getTree();
        
        return $this->successResponse(
            new CategoryCollection($data),
            'Category tree fetched successfully'
        );
    }
}
