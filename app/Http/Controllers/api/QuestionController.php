<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Services\QuestionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuestionController extends Controller
{
    protected $questionService;

    public function __construct(QuestionService $questionService)
    {
        $this->questionService = $questionService;
    }

        /**
     * @OA\Post(
     *     tags={"Question"},
     *     summary="Save question",
     *     description="This endpoint is used to save a new question.",
     *     path="/api/question/saveQuestion",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="json",
     *             @OA\Schema(
     *                 required={"question","answer","password","category_id"},
     *                 @OA\Property(property="question", type="string", example="Value"),
     *                 @OA\Property(property="answer", type="string", example="Value"),
     *                 @OA\Property(property="category_id", type="int", example=1),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Pergunta salva com sucesso.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity",  
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="A pergunta deve conter no mínimo 8 caracteres e a resposta no mínimo 3 caracteres.")
     *         )
     *     )
     * )
     */
    public function saveQuestion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "question" => "required|string|min:8",
            "answer" => "required|string|min:3",
            "category_id" => "required|int|exists:categories,id",
        ]);

        if ($validator->fails()) {
            return response()->json("A pergunta deve conter no mínimo 8 caracteres e a resposta no mínimo 3 caracteres.", 422);
        }

        $question = Question::create($request->only(["question", "answer", "category_id"]));

        if ($this->questionService->saveQuestion($question)) {
            return response()->json(["message" => "Pergunta salva com sucesso."], 201);
        }

        return response()->json(["error" => "Erro ao cadastrar uma nova pergunta"], 500);
    }


    /**
     * @OA\Get(
     *     tags={"Question"},
     *     summary="Get all Question",
     *     description="This endpoint is used to return a collection of Question filtered by category.",
     *     path="/api/question/getByCategory/{categoryId}",
     *     @OA\Parameter(
     *         name="categoryId",
     *         in="path",
     *         required=true,
     *         description="The ID of the category",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *         @OA\JsonContent(
     *             @OA\Property(type="array",property="data",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="int", example=1),
     *                     @OA\Property(property="question", type="string", example="Value 1"),
     *                     @OA\Property(property="answer", type="string", example="Value 123"),
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
    public function getQuestionByCategory($categoryId)
    {
        $questions = $this->questionService->getQuestionsByCategoryId($categoryId);

        $questions->each(function ($question) {
            $question->makeHidden(["category_id", "created_at", "updated_at"]);
        });

        return response()->json(["data" => $questions], 200);
    }
}
