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

    /**
     * @OA\Get(
     *     tags={"Category"},
     *     summary="Get all Categories",
     *     description="This endpoint is used to return a collection of Categories.",
     *     path="/api/categories/getAll",
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *         @OA\JsonContent(
     *             @OA\Property(type="array",property="data",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="int", example=1),
     *                     @OA\Property(property="name", type="string", example="Value 1")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",  
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthorized"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",  
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Forbidden"),
     *         )
     *     ),
     * )
     */
    public function getAllCategories()
    {
        $categories = $this->categoryService->findAllCategories();

        $categories->each(function ($category) {
            $category->makeHidden(["created_at", "updated_at"]);
        });

        return response()->json(["data" => $categories], 200);
    }

    /**
     * @OA\Post(
     *     tags={"Category"},
     *     summary="Save Category",
     *     description="This endpoint is used to save a new Category at database.",
     *     path="/api/categories/saveCategory",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="json",
     *             @OA\Schema(
     *                 required={"name"},
     *                 @OA\Property(property="name", type="string", example="New Category")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Categoria salva com sucesso."),
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",  
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthorized"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",  
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Forbidden"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Conflict",  
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Essa categoria já está cadastrada."),
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity",  
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="A categoria deve conter no mínimo 3 caracteres."),
     *         )
     *     ),
     * )
     */
    public function saveCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required|string|min:3",
        ]);

        if ($validator->fails()) {
            return response()->json("A categoria deve conter no mínimo 3 caracteres.", 422);
        }

        $existCategory = $this->categoryService->findCategory($request->name);

        if ($existCategory) {
            return response()->json(["message" => "Essa categoria já está cadastrada."], 409);
        }

        $category = Category::create($request->only(["name"]));

        if ($this->categoryService->saveCategory($category)) {
            return response()->json(["message" => "Categoria salva com sucesso."], 201);
        }

        return response()->json(["error" => "Erro ao cadastrar uma nova categoria"], 500);
    }
}
