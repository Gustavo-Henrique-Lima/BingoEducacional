<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Validator;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @OA\Patch(
     *     tags={"Users"},
     *     summary="Update password",
     *     description="This endpoint is used to update a user's password.",
     *     path="/api/users/{email}/updatePassword",
     *     @OA\Parameter(
     *         name="email",
     *         in="path",
     *         required=true,
     *         description="User's email",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="json",
     *             @OA\Schema(
     *                 required={"password"},
     *                 @OA\Property(property="password", type="string", example="password123")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Senha atualizada com sucesso."),
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
     *         response=404,
     *         description="Not found",  
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Usuário não encontrado."),
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity",  
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="A senha deve conter no mínimo 6 caracteres."),
     *         )
     *     ),
     * )
     */
    public function updatePassword(Request $request, $email)
    {
        $validator = Validator::make($request->all(), [
            "password" => "required|string|min:6",
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => "A senha deve conter no mínimo 6 caracteres."], 422);
        }

        $loggedInUser = Auth::user();

        if ($loggedInUser->email !== $email) {
            return response()->json(["message" => "Forbiden"], 403);
        }

        $user = $this->userService->findUserByEmail($email);

        if (!$user) {
            return response()->json(["message" => "Usuário não encontrado."], 404);
        }

        if ($this->userService->updatePassword($user, $request->password)) {
            return response()->json(["message" => "Senha atualizada com sucesso."], 200);
        }

        return response()->json(["message" => "Erro ao atualizar a senha."], 500);
    }

    /**
     * @OA\Post(
     *     tags={"Users"},
     *     summary="Save Recover Password Code",
     *     description="This endpoint is used to save the verification code for the user's password recovery.",
     *     path="/api/users/{email}/saveRecoverPasswordCode",
     *     @OA\Parameter(
     *         name="email",
     *         in="path",
     *         required=true,
     *         description="User's email",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="json",
     *             @OA\Schema(
     *                 required={"code"},
     *                 @OA\Property(property="code", type="string", example="123456")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Código salvo com sucesso."),
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity",  
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="O código deve conter no mínimo 6 caracteres."),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found",  
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Usuário não encontrado."),
     *         )
     *     )
     * )
     */
    public function saveRecoverPasswordCode(Request $request, $email)
    {

        $validator = Validator::make($request->all(), [
            "code" => "required|string|min:6"
        ]);

        if ($validator->fails()) {
            return response()->json("O código deve conter no mínimo 6 caracteres.", 422);
        }

        $user = $this->userService->findUserByEmail($email);

        if (!$user) {
            return response()->json(["message" => "Usuário não encontrado."], 404);
        }

        $code = $request->input("code");

        if ($this->userService->saveCodeRecuperation($user, $code)) {
            return response()->json(["message" => "Código salvo com sucesso."], 200);
        }

        return response()->json(["message" => "Erro ao tentar salvar código"], 500);
    }

    /**
     * @OA\Patch(
     *     tags={"Users"},
     *     summary="Recover password",
     *     description="This endpoint is used to recover a user's password.",
     *     path="/api/users/{email}/recoverpassword",
     *     @OA\Parameter(
     *         name="email",
     *         in="path",
     *         required=true,
     *         description="User's email",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="json",
     *             @OA\Schema(
     *                 required={"password","code"},
     *                 @OA\Property(property="password", type="string", example="password123"),
     *                 @OA\Property(property="code", type="string", example="123456")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Senha atualizada com sucesso."),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found",  
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Usuário não encontrado."),
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity",  
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="A senha e o código deve conter no mínimo 6 caracteres."),
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Conflict",  
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Código de ativação inválido."),
     *         )
     *     )
     * )
     */
    public function recoverPassword(Request $request, $email)
    {
        $validator = Validator::make($request->all(), [
            "password" => "required|string|min:6",
            "code" => "required|string|min:6",
        ]);

        if ($validator->fails()) {
            return response()->json("A senha e o código deve conter no mínimo 6 caracteres.", 422);
        }

        $user = $this->userService->findUserByEmail($email);

        if (!$user) {
            return response()->json(["message" => "Usuário não encontrado."], 404);
        }

        if ($user->recuperationCode != $request->code) {
            return response()->json(["message" => "Código de ativação inválido."], 409);
        }

        if ($this->userService->updatePassword($user, $request->password)) {
            return response()->json(["message" => "Senha atualizada com sucesso."], 200);
        }

        return response()->json(["message" => "Erro ao atualizar a senha"], 500);
    }

    /**
     * @OA\Post(
     *     tags={"Users"},
     *     summary="Save Activation Code",
     *     description="This endpoint is used to save the activation verification code of the administrator account.",
     *     path="/api/users/{email}/saveActivationCode",
     *     @OA\Parameter(
     *         name="email",
     *         in="path",
     *         required=true,
     *         description="User's email",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="json",
     *             @OA\Schema(
     *                 required={"code"},
     *                 @OA\Property(property="code", type="string", example="123456")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Código salvo com sucesso."),
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity",  
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="O código deve conter no mínimo 6 caracteres."),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found",  
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Usuário não encontrado."),
     *         )
     *     )
     * )
     */
    public function saveActivationCode(Request $request, $email)
    {

        $validator = Validator::make($request->all(), [
            "code" => "required|string|min:6"
        ]);

        if ($validator->fails()) {
            return response()->json("O código deve conter no mínimo 6 caracteres.", 422);
        }

        $user = $this->userService->findUserByEmail($email);

        if (!$user) {
            return response()->json(["message" => "Usuário não encontrado."], 404);
        }

        $code = $request->input("code");

        if ($this->userService->saveCodeActivation($user, $code)) {
            return response()->json(["message" => "Código salvo com sucesso."], 200);
        }

        return response()->json(["message" => "Erro ao tentar salvar código"], 500);
    }

    /**
     * @OA\Post(
     *     tags={"Users"},
     *     summary="Activate Account",
     *     description="This endpoint is used to activate an administrator's account.",
     *     path="/api/users/{email}/activeAccount",
     *     @OA\Parameter(
     *         name="email",
     *         in="path",
     *         required=true,
     *         description="User's email",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="json",
     *             @OA\Schema(
     *                 required={"code"},
     *                 @OA\Property(property="code", type="string", example="123456")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ok",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Conta ativada com sucesso."),
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity",  
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="O código deve conter no mínimo 6 caracteres."),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found",  
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Usuário não encontrado."),
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Conflict",  
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Código de ativação inválido."),
     *         )
     *     )
     * )
     */
    public function activeAccount(Request $request, $email)
    {
        $validator = Validator::make($request->all(), [
            "code" => "required|string|min:6"
        ]);

        if ($validator->fails()) {
            return response()->json("O código deve conter no mínimo 6 caracteres.", 422);
        }

        $user = $this->userService->findUserByEmail($email);

        if (!$user) {
            return response()->json(["message" => "Usuário não encontrado."], 404);
        }

        $code = $request->code;

        if ($code === $user->activationCode) {
            if ($this->userService->activeAccount($user)) {
                return response()->json(["message" => "Conta ativada com sucesso."], 200);
            }
        } else {
            return response()->json(["message" => "Código de ativação inválido."], 409);
        }

        return response()->json(["message" => "Erro ao atualizar a senha"], 500);
    }

    /**
     * @OA\Post(
     *     tags={"Users"},
     *     summary="Save user",
     *     description="This endpoint is used to save a new user.",
     *     path="/api/users/saveUser",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="json",
     *             @OA\Schema(
     *                 required={"name","email","password","role"},
     *                 @OA\Property(property="name", type="string", example="Value"),
     *                 @OA\Property(property="email", type="string", example="Value"),
     *                 @OA\Property(property="password", type="string", example="Value"),
     *                 @OA\Property(property="role", type="string", example="Value")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Usuário cadastrado com sucesso.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable Entity",  
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="A senha deve conter no mínimo 6 caracteres, o email deve conter 25 caracteres e o nome deve conter 8 caracteres.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Conflict",  
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Esse email já está cadastrado.")
     *         )
     *     )
     * )
     */
    public function saveUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required|string|min:8",
            "email" => "required|email|min:25",
            "password" => "required|string|min:6",
            "role" => "required|string",
        ]);

        if ($validator->fails()) {
            return response()->json("A senha deve conter no mínimo 6 caracteres.", 422);
        }

        $user = $this->userService->findUserByEmail($request->email);

        if ($user) {
            return response()->json(["message" => "Esse email já está cadastrado."], 409);
        }

        $user = User::create($request->only(["name", "email", "password", "role"]));

        if ($this->userService->saveUser($user)) {
            return response()->json(["message" => "Usuário cadastrado com sucesso."], 201);
        }

        return response()->json(["message" => "Erro ao cadastar usuário"], 500);
    }
}
