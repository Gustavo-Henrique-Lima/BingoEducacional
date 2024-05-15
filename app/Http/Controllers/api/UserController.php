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

    public function updatePassword(Request $request, $email)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json("A senha deve conter no mínimo 6 caracteres", 422);
        }

        $loggedInUser = Auth::user();

        if ($loggedInUser->email !== $email) {
            return response()->json(['message' => 'Forbiden'], 403);
        }

        $user = $this->userService->findUserByEmail($email);

        if (!$user) {
            return response()->json(['message' => 'Usuário não encontrado'], 404);
        }

        if ($this->userService->updatePassword($user, $request->password)) {
            return response()->json(['message' => 'Senha atualizada com sucesso'], 200);
        }

        return response()->json(['message' => 'Erro ao atualizar a senha'], 500);
    }

    public function saveRecoverPasswordCode(Request $request, $email)
    {
        $user = $this->userService->findUserByEmail($email);

        if (!$user) {
            return response()->json(['message' => 'Usuário não encontrado'], 404);
        }

        $code = $request->input('code');

        if ($this->userService->saveCodeRecuperation($user, $code)) {
            return response()->json(['message' => 'Código salvo com sucesso'], 200);
        }

        return response()->json(['message' => 'Erro ao tentar salvar código'], 500);
    }

    public function recoverPassword(Request $request, $email)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:6',
            'code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json("A senha deve conter no mínimo 6 caracteres e o código é obrigatório", 422);
        }

        $user = $this->userService->findUserByEmail($email);

        if (!$user) {
            return response()->json(['message' => 'Usuário não encontrado'], 404);
        }

        if ($user->recuperationCode != $request->code) {
            return response()->json(['message' => 'Erro ao verificar o código'], 409);
        }

        if ($this->userService->updatePassword($user, $request->password)) {
            return response()->json(['message' => 'Senha atualizada com sucesso'], 200);
        }

        return response()->json(['message' => 'Erro ao atualizar a senha'], 500);
    }

    public function saveActivationCode(Request $request, $email)
    {
        $user = $this->userService->findUserByEmail($email);

        if (!$user) {
            return response()->json(['message' => 'Usuário não encontrado'], 404);
        }

        $code = $request->input('code');

        if ($this->userService->saveCodeActivation($user, $code)) {
            return response()->json(['message' => 'Código salvo com sucesso'], 200);
        }

        return response()->json(['message' => 'Erro ao tentar salvar código'], 500);
    }

    public function activeAccount(Request $request, $email)
    {
        $user = $this->userService->findUserByEmail($email);

        if (!$user) {
            return response()->json(['message' => 'Usuário não encontrado'], 404);
        }

        $code = $request->code;

        if ($code === $user->activationCode) {
            if ($this->userService->activeAccount($user)) {
                return response()->json(['message' => 'Conta ativada com sucesso'], 200);
            }
        } else {
            return response()->json(['message' => 'Código de ativação inválido'], 404);
        }

        return response()->json(['message' => 'Erro ao atualizar a senha'], 500);
    }

    public function saveUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:8',
            'email' => 'required|email|min:25',
            'password' => 'required|string|min:6',
            'role' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json("A senha deve conter no mínimo 6 caracteres", 422);
        }

        $user = $this->userService->findUserByEmail($request->email);

        if ($user) {
            return response()->json(['message' => 'Esse email já está cadastrado'], 409);
        }

        $user = User::create($request->only(['name', 'email', 'password', 'role']));

        if ($this->userService->saveUser($user)) {
            return response()->json(['message' => 'Usuário cadastrado com sucesso'], 201);
        }

        return response()->json(['message' => 'Erro ao cadastar usuário'], 500);
    }
}
