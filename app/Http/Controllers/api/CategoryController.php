<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function getAllCategories()
    {
        $categories = $this->categoryService->findAllCategories();

        $categories->each(function ($category) {
            $category->makeHidden(["created_at", "updated_at"]);
        });

        return response()->json(['data' => $categories], 200);
    }
}
