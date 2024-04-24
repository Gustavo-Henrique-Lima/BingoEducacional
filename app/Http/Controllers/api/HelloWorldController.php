<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tag;
use Illuminate\Support\Facades\Log;

class HelloWorldController extends Controller
{

    /**
     * @OA\Get(
     *     tags={"Initial"},
     *     summary="Get Hello World",
     *     description="This endpoint returns a Hello World.",
     *     path="/api/initial",
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *         @OA\JsonContent(
     *            @OA\Property(property="hello", type="string", example="Olá mundo!"),
     *         )
     *     )
     * )
     */
    public function helloWorld()
    {
        return response()->json("Olá mundo!", 200);
    }
}
