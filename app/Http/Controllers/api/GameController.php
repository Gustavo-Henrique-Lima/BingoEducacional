<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Services\GameService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GameController extends Controller
{
    protected $gameService;

    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;
    }

    /**
     * @OA\Post(
     *     tags={"Games"},
     *     summary="Save Game",
     *     description="This endpoint is used to save a new game.",
     *     path="/api/game/createGame",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="json",
     *             @OA\Schema(
     *                 required={"questions", "name"},
     *                 @OA\Property(
     *                     property="questions",
     *                     type="array",
     *                     @OA\Items(type="integer", example=1),
     *                     example="[1, 2]",
     *                     description="Array of question IDs"
     *                 ),
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     example="Teste jogo 3",
     *                     description="Name of the game"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Jogo criado com sucesso!"),
     *             @OA\Property(property="id", type="integer", example=1)
     *         )
     *     )
     * )
     */
    public function createGame(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'questions' => 'required|array',
            'questions.*' => 'exists:questions,id',
            'name' => 'required|string|min:3',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['errors' => $errors], 422);
        }

        $validated = $request->validate([
            'questions' => 'required|array',
            'questions.*' => 'exists:questions,id',
            'name' => 'required|string|min:3',
        ]);

        $game = $this->gameService->createGame($validated['questions'], $validated["name"]);

        if ($game) {
            return response()->json(["message" => "Jogo criado com sucesso!", "id" => $game->id], 201);
        }

        return response()->json(["message" => "Erro ao criar o jogo."], 400);
    }

    /**
     * @OA\Get(
     *     tags={"Games"},
     *     summary="Get Game By Owner",
     *     description="This endpoint is used to get all games by owner",
     *     path="/api/game/myGames",
     *      @OA\Response(
     *          response=200,
     *          description="Ok",
     *          @OA\JsonContent(
     *              type="object",
     *               @OA\Property(
     *               property="data",
     *               type="array",
     *                @OA\Items(
     *                      type="object",
     *                      @OA\Property(property="id", type="integer", example=1),
     *                      @OA\Property(property="name", type="string", example="Teste de nome de jogo"),
     *                      @OA\Property(property="created_at", type="string", format="date-time", example="2024-07-13T13:23:16.000000Z")
     *                ),
     *                description="Array of game objects"
     *               )
     *          )
     *     )
     * )
     */
    public function getGameByOwner()
    {
        $game = $this->gameService->getGameByOwnerId();

        return response()->json(["data" => $game], 200);
    }


    /**
     * @OA\Get(
     *     tags={"Games"},
     *     summary="Get Game By id",
     *     description="This endpoint is used to get game by id",
     *     path="/api/game/{id}",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Game id",
     *         @OA\Schema(type="int")
     *     ),
     * @OA\Response(
     *     response=200,
     *     description="Ok",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(
     *             property="data",
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=3),
     *             @OA\Property(property="name", type="string", example="Teste jogo 3"),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2024-07-13T13:32:45.000000Z"),
     *             @OA\Property(
     *                 property="questions",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="question", type="string", example="What is the capital of France?"),
     *                     @OA\Property(property="answer", type="string", example="Paris")
     *                 ),
     *                 description="Array of related questions with their answers"
     *             ),
     *             description="Detailed information of a single game including related questions"
     *         )
     *     )
     * )
     * )
     */
    public function getGameById(int $id)
    {
        $game = $this->gameService->getGameById($id);

        return response()->json(["data" => $game], 200);
    }
}
