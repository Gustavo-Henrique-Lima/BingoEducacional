<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

        return response()->json(["data" => $categories], 200);
    }

    public function saveCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required|string|min:3",
        ]);

        if ($validator->fails()) {
            return response()->json("A pergunta deve conter no mínimo 3 caracteres", 422);
        }

        $existCategory = $this->categoryService->findCategory($request->name);

        if ($existCategory) {
            return response()->json(["message" => "Essa categoria já está cadastrada"], 409);
        }

        $category = Category::create($request->only(["name"]));

        if ($this->categoryService->saveCategory($category)) {
            return response()->json(["message" => "Categoria salva com sucesso"], 201);
        }

        return response()->json(["error" => "Erro ao cadastrar uma nova categoria"], 500);
    }
}
